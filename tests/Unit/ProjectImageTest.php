<?php

namespace Tests\Unit;

use App\Models\ProjectImage;
use PHPUnit\Framework\TestCase;

class ProjectImageTest extends TestCase
{
    public function test_extract_youtube_id_from_standard_url(): void
    {
        $url = 'https://www.youtube.com/watch?v=dQw4w9WgXcQ';
        $this->assertEquals('dQw4w9WgXcQ', ProjectImage::extractYouTubeId($url));
    }

    public function test_extract_youtube_id_with_extra_query_parameters(): void
    {
        $url = 'https://www.youtube.com/watch?feature=shared&v=dQw4w9WgXcQ&t=42s';
        $this->assertEquals('dQw4w9WgXcQ', ProjectImage::extractYouTubeId($url));
    }

    public function test_extract_youtube_id_from_shortlink(): void
    {
        $url = 'https://youtu.be/dQw4w9WgXcQ?si=trackingParam123';
        $this->assertEquals('dQw4w9WgXcQ', ProjectImage::extractYouTubeId($url));
    }

    public function test_extract_youtube_id_from_shorts_url(): void
    {
        $url = 'https://www.youtube.com/shorts/dQw4w9WgXcQ';
        $this->assertEquals('dQw4w9WgXcQ', ProjectImage::extractYouTubeId($url));
    }

    public function test_extract_youtube_id_from_embed_url(): void
    {
        $url = 'https://www.youtube.com/embed/dQw4w9WgXcQ';
        $this->assertEquals('dQw4w9WgXcQ', ProjectImage::extractYouTubeId($url));
    }

    public function test_extract_youtube_id_returns_null_for_invalid_urls(): void
    {
        $this->assertNull(ProjectImage::extractYouTubeId('https://vimeo.com/123456'));
        $this->assertNull(ProjectImage::extractYouTubeId('https://google.com'));
        $this->assertNull(ProjectImage::extractYouTubeId('not-a-url'));
        $this->assertNull(ProjectImage::extractYouTubeId(null));
    }

    public function test_embed_url_and_thumbnail_url_for_video(): void
    {
        $image = new ProjectImage([
            'type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $this->assertEquals('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0', $image->embed_url);
        $this->assertEquals('https://img.youtube.com/vi/dQw4w9WgXcQ/hqdefault.jpg', $image->thumbnail_url);
    }

    public function test_embed_url_is_null_for_image(): void
    {
        $image = new ProjectImage([
            'type' => 'image',
            'image_path' => 'projects/gallery/sample.jpg',
        ]);

        $this->assertNull($image->embed_url);
    }
}
