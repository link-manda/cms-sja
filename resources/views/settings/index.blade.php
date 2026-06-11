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
    </div>
@endsection
