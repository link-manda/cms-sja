@extends('layouts.base', ['title' => 'Reset Password'])

@section('content')
    <div class="relative min-h-screen w-full flex bg-[#EEEDE6]">
        <!-- Left Panel: Reset Password Form (Light Theme) -->
        <div class="w-full lg:w-1/2 flex justify-center items-center p-8 md:p-16 relative z-10">
            <div class="w-full max-w-md">
                
                <!-- Logo -->
                <a class="flex justify-start mb-12" href="/">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center p-2 border border-[#7C7C89]/20 shadow-sm">
                        <img alt="SJA Logo" class="h-8 object-contain" src="{{ asset('assets/logo.png') }}"/>
                    </div>
                </a>

                <!-- Typography Headers -->
                <div class="mb-8 text-left">
                    <h1 class="text-4xl md:text-5xl font-serif font-bold text-[#055305] tracking-tight mb-3">Reset Password</h1>
                    <p class="text-sm font-sans text-[#5B5F50] leading-relaxed tracking-wide">
                        Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
                    </p>
                </div>

                <!-- Session Status / Flash Messages -->
                @if (session('status'))
                    <div class="mb-6 p-4 rounded-lg bg-[#327447]/10 border border-[#327447]/30 flex items-start gap-3 animate-fade-in-up">
                        <svg class="w-5 h-5 text-[#327447] mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-sans text-[#327447] font-medium">{{ session('status') }}</p>
                    </div>
                @endif

                <!-- Global Error Alert -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg bg-[#DB5916]/10 border border-[#DB5916]/30 flex items-start gap-3 animate-fade-in-up">
                        <svg class="w-5 h-5 text-[#DB5916] mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div class="text-sm font-sans text-[#DB5916] font-medium">
                            <p>We couldn't process your request. Please check your email address.</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="text-left w-full space-y-6">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label class="block font-sans font-semibold text-[#141B23] text-xs uppercase tracking-widest mb-2" for="email">Email Address</label>
                        <input class="w-full bg-white border {{ $errors->has('email') ? 'border-[#DB5916] focus:border-[#DB5916] focus:ring-[#DB5916]' : 'border-[#7C7C89]/40 focus:border-[#327447] focus:ring-[#327447]' }} text-[#141B23] rounded-lg px-4 py-3 font-sans focus:outline-none focus:ring-1 transition-all duration-300 placeholder-[#7C7C89]/50" 
                               id="email" name="email" value="{{ old('email') }}" placeholder="admin@sistemjayaabadi.com" type="email" required autofocus />
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 flex flex-col gap-4">
                        <button class="w-full bg-[#DB5916] hover:bg-[#b0450f] text-white font-sans font-bold py-3.5 rounded-lg shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300" 
                                type="submit">
                            Email Password Reset Link
                        </button>
                        
                        <a href="{{ route('login') }}" class="text-center text-sm font-sans font-medium text-[#7C7C89] hover:text-[#055305] transition-colors mt-2 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Login
                        </a>
                    </div>

                    <!-- Subtle Footer -->
                    <div class="mt-12 pt-8 border-t border-[#7C7C89]/20">
                        <p class="text-xs text-[#7C7C89] font-sans">
                            &copy; {{ date('Y') }} PT. Sistem Jaya Abadi. All rights reserved.
                        </p>
                    </div>
                </form>
            </div>
        </div>

        <!-- Right Panel: Image / Pattern (Light Theme Accent) -->
        <div class="hidden lg:block lg:w-1/2 relative bg-[#EEEDE6] overflow-hidden">
            <!-- Background Image -->
            <div class="absolute inset-0">
                <img src="{{ asset('assets/office_sesetan.png') }}" alt="Architecture Office" class="w-full h-full object-cover opacity-90 mix-blend-multiply filter contrast-[1.1] brightness-[0.95]">
            </div>
            
            <!-- Graphic Element / Accent -->
            <div class="absolute inset-0 bg-gradient-to-tl from-[#055305]/40 via-transparent to-[#EEEDE6]/30"></div>
            
            <div class="absolute bottom-16 left-16 right-16">
                <div class="glass-panel bg-[#EEEDE6]/80 backdrop-blur-md border border-white/40 p-8 rounded-2xl shadow-xl">
                    <h2 class="text-3xl font-serif text-[#055305] font-bold mb-4">Securing Access.</h2>
                    <p class="text-[#141B23]/80 font-sans leading-relaxed">
                        Need help getting back in? Enter your registered email address to receive instructions on securely resetting your administrative password.
                    </p>
                </div>
            </div>
            
            <!-- Subtle Geometric Pattern from Tailwick -->
            <svg aria-hidden="true" class="absolute inset-0 size-full fill-[#055305]/5 stroke-[#055305]/5 pointer-events-none">
                <defs>
                    <pattern height="56" id="authPattern" patternunits="userSpaceOnUse" width="56" x="50%" y="16">
                        <path d="M.5 56V.5H72" fill="none"></path>
                    </pattern>
                </defs>
                <rect fill="url(#authPattern)" height="100%" stroke-width="0" width="100%"></rect>
            </svg>
        </div>
    </div>
@endsection
