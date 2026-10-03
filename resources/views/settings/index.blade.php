@extends('layouts.vertical', ['title' => 'Global Settings'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'System', 'title' => 'Global Settings'])

    <div class="grid grid-cols-1 gap-6">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title text-base font-semibold text-default-800">Manage Website Information</h6>
            </div>
            
            <div class="card-body">
                @if (session('success'))
                    <div class="bg-success/10 text-success border border-success/20 text-sm rounded-md py-3 px-5 mb-5">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('settings.update') }}" class="space-y-6">
                    @csrf
                    
                    <h5 class="text-lg font-semibold text-default-800 border-b border-default-200 pb-2">Global SEO & Metadata</h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="site_title">Homepage Meta Title</label>
                            <input class="form-input" id="site_title" name="site_title" value="{{ setting('site_title') }}" placeholder="e.g. Premium Bali Contractor..." type="text" />
                        </div>
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="site_description">Homepage Meta Description</label>
                            <textarea class="form-input" id="site_description" name="site_description" rows="2" placeholder="Premium contractor in Bali...">{{ setting('site_description') }}</textarea>
                        </div>
                    </div>

                    <h5 class="text-lg font-semibold text-default-800 border-b border-default-200 pb-2 mt-8">Contact Information</h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="contact_whatsapp">WhatsApp Number (CTA)</label>
                            <input class="form-input" id="contact_whatsapp" name="contact_whatsapp" value="{{ setting('contact_whatsapp') }}" placeholder="e.g. 081234567890" type="text" />
                            <p class="text-xs text-default-400 mt-1">Gunakan angka saja, format bebas (0812 atau +62 otomatis dikonversi oleh sistem untuk link).</p>
                        </div>
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="contact_email">Corporate Email</label>
                            <input class="form-input" id="contact_email" name="contact_email" value="{{ setting('contact_email') }}" placeholder="e.g. info@sja-bali.com" type="email" />
                        </div>
                    </div>

                    <h5 class="text-lg font-semibold text-default-800 border-b border-default-200 pb-2 mt-8">Company Details</h5>
                    
                    <div class="grid grid-cols-1 gap-5">
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="company_address">Office Address</label>
                            <textarea class="form-input" id="company_address" name="company_address" rows="3" placeholder="Jl. Raya Bypass Ngurah Rai...">{{ setting('company_address') }}</textarea>
                        </div>
                    </div>

                    <h5 class="text-lg font-semibold text-default-800 border-b border-default-200 pb-2 mt-8">Social Media Links</h5>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="social_instagram">Instagram URL</label>
                            <input class="form-input" id="social_instagram" name="social_instagram" value="{{ setting('social_instagram') }}" placeholder="https://instagram.com/sja.bali" type="url" />
                        </div>
                        <div>
                            <label class="block font-medium text-default-900 text-sm mb-2" for="social_linkedin">LinkedIn URL</label>
                            <input class="form-input" id="social_linkedin" name="social_linkedin" value="{{ setting('social_linkedin') }}" placeholder="https://linkedin.com/company/..." type="url" />
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-default-200">
                        <button type="submit" class="btn bg-primary text-white cursor-pointer hover:bg-primary-600 transition-colors">Save Settings</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Database Backups Card (Cloudflare R2) -->
        <div class="card" id="backup-card">
            <div class="card-header flex justify-between items-center flex-wrap gap-4 border-b border-default-200">
                <div>
                    <h6 class="card-title text-base font-semibold text-default-800">Database Backups (Cloudflare R2)</h6>
                    <p class="text-xs text-default-400 mt-1">Cadangan bulanan MySQL terenkripsi AES-256-CBC dengan retensi bergulir 12 bulan yang tersimpan aman di Cloudflare R2.</p>
                </div>
                <div>
                    <button type="button" id="btn-manual-backup" class="btn bg-primary text-white text-xs px-3.5 py-2 inline-flex items-center gap-2 rounded-md hover:bg-primary-600 transition-colors cursor-pointer shadow-sm">
                        <i data-lucide="database-backup" id="backup-icon" class="size-4"></i>
                        <span id="backup-btn-text">Backup Now</span>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Status & Policy Highlights -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
                    <div class="p-3 rounded-lg border border-default-200 bg-default-50/50 flex items-center gap-3">
                        <div class="p-2 rounded bg-info/10 text-info">
                            <i data-lucide="calendar-clock" class="size-4"></i>
                        </div>
                        <div>
                            <div class="text-[11px] text-default-400 font-medium">Jadwal Otomatis</div>
                            <div class="text-xs font-semibold text-default-700">Bulanan (Tgl 1, 02:00 UTC)</div>
                        </div>
                    </div>
                    <div class="p-3 rounded-lg border border-default-200 bg-default-50/50 flex items-center gap-3">
                        <div class="p-2 rounded bg-success/10 text-success">
                            <i data-lucide="shield-check" class="size-4"></i>
                        </div>
                        <div>
                            <div class="text-[11px] text-default-400 font-medium">Keamanan Data</div>
                            <div class="text-xs font-semibold text-default-700">AES-256-CBC (HKDF App Key)</div>
                        </div>
                    </div>
                    <div class="p-3 rounded-lg border border-default-200 bg-default-50/50 flex items-center gap-3">
                        <div class="p-2 rounded bg-primary/10 text-primary">
                            <i data-lucide="history" class="size-4"></i>
                        </div>
                        <div>
                            <div class="text-[11px] text-default-400 font-medium">Kebijakan Retensi</div>
                            <div class="text-xs font-semibold text-default-700">12 Siklus / 30 Hari Manual</div>
                        </div>
                    </div>
                </div>

                <!-- Alert Messages -->
                <div id="backup-alert-success" class="hidden bg-success/10 text-success border border-success/20 text-sm rounded-md py-3 px-5 mb-5 flex items-center gap-2">
                    <i data-lucide="check-circle" class="size-4 shrink-0"></i>
                    <span id="backup-success-text">Cadangan database berhasil dibuat!</span>
                </div>
                <div id="backup-alert-error" class="hidden bg-danger/10 text-danger border border-danger/20 text-sm rounded-md py-3 px-5 mb-5 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="size-4 shrink-0"></i>
                    <span id="backup-error-text">Gagal membuat cadangan database.</span>
                </div>

                <!-- Backups Table -->
                <div class="overflow-x-auto border border-default-200 rounded-lg">
                    <table class="min-w-full divide-y divide-default-200 text-left text-xs">
                        <thead class="bg-default-100/75 text-default-600 font-semibold uppercase tracking-wider">
                            <tr>
                                <th scope="col" class="px-4 py-3">Waktu Pembuatan</th>
                                <th scope="col" class="px-4 py-3">Sumber</th>
                                <th scope="col" class="px-4 py-3">Nama Berkas</th>
                                <th scope="col" class="px-4 py-3">Ukuran</th>
                                <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-default-200 bg-white dark:bg-default-50 font-normal text-default-700">
                            @forelse ($backups as $backup)
                                <tr class="hover:bg-default-50/80 transition-colors">
                                    <td class="px-4 py-3 whitespace-nowrap font-medium text-default-800">
                                        {{ $backup['created_at_formatted'] }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        @if ($backup['source'] === 'scheduled')
                                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded inline-flex items-center gap-1 bg-info/10 text-info border border-info/20">
                                                <i data-lucide="calendar" class="size-3"></i> Terjadwal
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 text-[11px] font-semibold rounded inline-flex items-center gap-1 bg-primary/10 text-primary border border-primary/20">
                                                <i data-lucide="user" class="size-3"></i> Manual
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 font-mono text-[11px] text-default-600 select-all">
                                        {{ $backup['filename'] }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap font-medium text-default-800">
                                        {{ $backup['size_formatted'] }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-right">
                                        <a href="{{ route('backups.download', $backup['filename']) }}" class="btn btn-sm bg-default-100 hover:bg-default-200 text-default-800 text-xs px-2.5 py-1.5 inline-flex items-center gap-1.5 rounded transition-colors" title="Unduh Berkas Terdekripsi (.sql.gz)">
                                            <i data-lucide="download" class="size-3.5"></i>
                                            <span>Unduh (.sql.gz)</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-10 text-default-400">
                                        <div class="flex flex-col items-center justify-center gap-2">
                                            <div class="p-3 rounded-full bg-default-100 text-default-400 mb-1">
                                                <i data-lucide="database" class="size-6"></i>
                                            </div>
                                            <p class="font-medium text-default-600 text-sm">Belum Ada Cadangan Database</p>
                                            <p class="text-xs text-default-400 max-w-md">Cadangan otomatis dibuat setiap awal bulan pukul 02:00, atau Anda dapat menekan tombol "Backup Now" di atas untuk membuat cadangan langsung ke Cloudflare R2.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btnBackup = document.getElementById('btn-manual-backup');
    const backupIcon = document.getElementById('backup-icon');
    const backupText = document.getElementById('backup-btn-text');
    const alertSuccess = document.getElementById('backup-alert-success');
    const alertError = document.getElementById('backup-alert-error');
    const successText = document.getElementById('backup-success-text');
    const errorText = document.getElementById('backup-error-text');

    if (!btnBackup) return;

    btnBackup.addEventListener('click', function () {
        if (!confirm('Apakah Anda yakin ingin membuat cadangan database sekarang ke Cloudflare R2?')) {
            return;
        }

        // Set UI loading state
        btnBackup.disabled = true;
        btnBackup.classList.add('pointer-events-none', 'opacity-60');
        if (backupIcon) {
            backupIcon.classList.add('animate-spin');
        }
        if (backupText) {
            backupText.textContent = 'Memproses cadangan...';
        }
        alertSuccess.classList.add('hidden');
        alertError.classList.add('hidden');

        fetch("{{ route('backups.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        })
        .then(async (response) => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok) {
                throw new Error(data.message || 'Gagal membuat cadangan database.');
            }
            return data;
        })
        .then((data) => {
            if (successText) {
                successText.textContent = data.message || 'Cadangan database berhasil dibuat! Memperbarui halaman...';
            }
            alertSuccess.classList.remove('hidden');

            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons({ root: alertSuccess });
            }

            setTimeout(() => {
                window.location.reload();
            }, 1200);
        })
        .catch((error) => {
            if (errorText) {
                errorText.textContent = error.message || 'Terjadi kesalahan saat memproses cadangan.';
            }
            alertError.classList.remove('hidden');

            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons({ root: alertError });
            }

            btnBackup.disabled = false;
            btnBackup.classList.remove('pointer-events-none', 'opacity-60');
            if (backupIcon) {
                backupIcon.classList.remove('animate-spin');
            }
            if (backupText) {
                backupText.textContent = 'Backup Now';
            }
        });
    });
});
</script>
@endsection

