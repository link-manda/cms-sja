<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['project_id', 'type', 'image_path', 'video_url'])]
class ProjectImage extends Model
{
    use HasFactory;

    /**
     * The accessors to append to the model's array and JSON form.
     *
     * @var array<int, string>
     */
    protected $appends = ['embed_url', 'thumbnail_url'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Extract 11-character YouTube video ID from various URL formats.
     */
    public static function extractYouTubeId(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $parts = parse_url($url);
        if (! isset($parts['host'])) {
            return null;
        }

        $host = strtolower($parts['host']);
        $path = $parts['path'] ?? '';

        // youtu.be/{id}
        if (str_contains($host, 'youtu.be')) {
            $segments = explode('/', trim($path, '/'));
            $id = $segments[0] ?? null;
            if ($id && preg_match('/^[a-zA-Z0-9_-]{11}$/', $id)) {
                return $id;
            }
        }

        // youtube.com (standard, shorts, embed, mobile, live)
        if (str_contains($host, 'youtube.com')) {
            if (preg_match('#^/(?:shorts|embed|v|live)/([a-zA-Z0-9_-]{11})#', $path, $matches)) {
                return $matches[1];
            }

            if (isset($parts['query'])) {
                parse_str($parts['query'], $query);
                if (isset($query['v']) && preg_match('/^[a-zA-Z0-9_-]{11}$/', $query['v'])) {
                    return $query['v'];
                }
            }
        }

        return null;
    }

    /**
     * Accessor for privacy-enhanced YouTube embed URL.
     */
    public function getEmbedUrlAttribute(): ?string
    {
        if ($this->type !== 'video' || empty($this->video_url)) {
            return null;
        }

        $id = self::extractYouTubeId($this->video_url);

        return $id ? "https://www.youtube-nocookie.com/embed/{$id}?rel=0" : null;
    }

    /**
     * Accessor for thumbnail URL.
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->type === 'video') {
            $id = self::extractYouTubeId($this->video_url);
            if ($id) {
                return "https://img.youtube.com/vi/{$id}/hqdefault.jpg";
            }

            return null;
        }

        return ! empty($this->image_path) ? asset('storage/'.$this->image_path) : null;
    }
}
