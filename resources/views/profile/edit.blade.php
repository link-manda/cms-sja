@extends('layouts.vertical', ['title' => 'Profile'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'CMS SJA', 'title' => 'Profile Settings'])

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Sidebar Navigation for Tabs -->
        <div class="lg:col-span-3">
            <div class="card">
                <div class="card-body p-4">
                    <div class="flex flex-col sm:flex-row lg:flex-col gap-2 nav-tabs" role="tablist">
                        <button type="button" 
                                id="tab-btn-info" 
                                onclick="switchTab('info')"
                                class="tab-btn flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-md transition-all text-primary bg-primary/10 w-full text-left cursor-pointer">
                            <i data-lucide="user" class="size-4.5"></i>
                            <span>Profile Info</span>
                        </button>
                        <button type="button" 
                                id="tab-btn-password" 
                                onclick="switchTab('password')"
                                class="tab-btn flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-md transition-all text-default-600 hover:text-primary hover:bg-default-100 dark:hover:bg-zinc-800 w-full text-left cursor-pointer">
                            <i data-lucide="key-round" class="size-4.5"></i>
                            <span>Change Password</span>
                        </button>
                        <button type="button" 
                                id="tab-btn-danger" 
                                onclick="switchTab('danger')"
                                class="tab-btn flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-md transition-all text-default-600 hover:text-danger hover:bg-danger/10 w-full text-left cursor-pointer">
                            <i data-lucide="shield-alert" class="size-4.5"></i>
                            <span>Danger Zone</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Content Cards -->
        <div class="lg:col-span-9">
            <!-- Profile Info Content -->
            <div id="tab-content-info" class="tab-pane card">
                <div class="card-header border-b border-default-200">
                    <h6 class="card-title text-base font-semibold text-default-800">Profile Information</h6>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Password Content -->
            <div id="tab-content-password" class="tab-pane card hidden">
                <div class="card-header border-b border-default-200">
                    <h6 class="card-title text-base font-semibold text-default-800">Change Password</h6>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Danger Zone Content -->
            <div id="tab-content-danger" class="tab-pane card hidden">
                <div class="card-header border-b border-danger/20 bg-danger/5">
                    <h6 class="card-title text-base font-semibold text-danger">Danger Zone</h6>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function switchTab(tabId) {
        // Hide all tab panes
        document.querySelectorAll('.tab-pane').forEach(el => {
            el.classList.add('hidden');
        });
        
        // Show target tab pane
        const targetPane = document.getElementById('tab-content-' + tabId);
        if (targetPane) {
            targetPane.classList.remove('hidden');
        }

        // Reset all button styles
        const btns = {
            info: document.getElementById('tab-btn-info'),
            password: document.getElementById('tab-btn-password'),
            danger: document.getElementById('tab-btn-danger')
        };

        // Default classes
        const defaultClasses = 'text-default-600 hover:text-primary hover:bg-default-100 dark:hover:bg-zinc-800';
        const dangerDefaultClasses = 'text-default-600 hover:text-danger hover:bg-danger/10';

        // Reset info button
        if (btns.info) {
            btns.info.className = 'tab-btn flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-md transition-all w-full text-left cursor-pointer ' + 
                (tabId === 'info' ? 'text-primary bg-primary/10' : defaultClasses);
        }

        // Reset password button
        if (btns.password) {
            btns.password.className = 'tab-btn flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-md transition-all w-full text-left cursor-pointer ' + 
                (tabId === 'password' ? 'text-primary bg-primary/10' : defaultClasses);
        }

        // Reset danger button
        if (btns.danger) {
            btns.danger.className = 'tab-btn flex items-center gap-3 px-4 py-3 text-sm font-semibold rounded-md transition-all w-full text-left cursor-pointer ' + 
                (tabId === 'danger' ? 'text-danger bg-danger/10' : dangerDefaultClasses);
        }
        
        // Save selected tab in localStorage
        localStorage.setItem('activeProfileTab', tabId);
    }

    // Load active tab on page load
    document.addEventListener('DOMContentLoaded', () => {
        let activeTab = 'info';
        
        // Check if there are validation errors for specific forms to auto-switch
        @if ($errors->updatePassword->isNotEmpty())
            activeTab = 'password';
        @elseif ($errors->userDeletion->isNotEmpty())
            activeTab = 'danger';
        @else
            // Fallback to localStorage
            const savedTab = localStorage.getItem('activeProfileTab');
            if (savedTab && ['info', 'password', 'danger'].includes(savedTab)) {
                activeTab = savedTab;
            }
        @endif

        switchTab(activeTab);
    });
</script>
@endsection
