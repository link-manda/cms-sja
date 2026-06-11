@extends('layouts.vertical', ['title' => 'Projects'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'CMS SJA', 'title' => 'Projects'])

    <!-- Success Notification -->
    @if (session('success'))
        <div class="mb-5 p-4 text-sm text-green-800 rounded bg-green-50 dark:bg-zinc-900 dark:text-green-400 border border-green-200 dark:border-green-800 flex items-center gap-2" role="alert">
            <i class="size-4" data-lucide="check-circle-2"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="card">
        <div class="card-header flex justify-between items-center">
            <h6 class="card-title text-base font-semibold text-default-800">Contractor Project Management</h6>
            <div class="flex items-center gap-2">
                <a href="{{ route('projects.archive') }}" class="btn btn-sm bg-default-200 text-default-800 hover:bg-default-300 flex items-center gap-1.5 cursor-pointer">
                    <i class="size-4" data-lucide="archive"></i> View Archive
                </a>
                <a href="{{ route('projects.create') }}" class="btn btn-sm bg-primary text-white flex items-center gap-1.5 cursor-pointer">
                    <i class="size-4" data-lucide="plus"></i> Add Project
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Data Table -->
            <div class="flex flex-col">
                <div class="overflow-x-auto">
                    <div class="min-w-full inline-block align-middle">
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-default-200">
                                <thead class="bg-default-150">
                                    <tr class="text-sm font-normal text-default-500 whitespace-nowrap">
                                        <th class="px-3.5 py-3 text-start" scope="col">Photo</th>
                                        <th class="px-3.5 py-3 text-start" scope="col">Project Name</th>
                                        <th class="px-3.5 py-3 text-start" scope="col">Location</th>
                                        <th class="px-3.5 py-3 text-start" scope="col">Status</th>
                                        <th class="px-3.5 py-3 text-start" scope="col">Date Created</th>
                                        <th class="px-3.5 py-3 text-center" scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-default-200">
                                    @forelse ($projects as $project)
                                        <tr class="text-default-800 font-normal hover:bg-default-50 transition-colors duration-150">
                                            <td class="px-3.5 py-2.5 whitespace-nowrap text-sm">
                                                @if ($project->image)
                                                    <img src="{{ asset('storage/projects/' . $project->image) }}" alt="{{ $project->title }}" class="h-10 w-16 object-cover rounded border border-default-200">
                                                @else
                                                    <div class="h-10 w-16 bg-default-100 dark:bg-zinc-800 flex items-center justify-center rounded text-[10px] text-default-400">No Photo</div>
                                                @endif
                                            </td>
                                            <td class="px-3.5 py-2.5 whitespace-nowrap text-sm font-medium text-default-900">
                                                {{ $project->title }}
                                            </td>
                                            <td class="px-3.5 py-2.5 whitespace-nowrap text-sm">
                                                {{ $project->location }}
                                            </td>
                                            <td class="px-3.5 py-2.5 whitespace-nowrap text-sm">
                                                @if ($project->status === 'Completed')
                                                    <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded bg-success/10 text-success">Completed</span>
                                                @else
                                                    <span class="px-2.5 py-0.5 inline-flex text-xs font-semibold rounded bg-warning/10 text-warning">Ongoing</span>
                                                @endif
                                            </td>
                                            <td class="px-3.5 py-2.5 whitespace-nowrap text-sm">
                                                {{ $project->created_at->format('d M, Y') }}
                                            </td>
                                            <td class="px-3.5 py-2.5 whitespace-nowrap text-sm text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ route('projects.edit', $project->id) }}" class="btn size-7 flex items-center justify-center bg-primary/10 text-primary hover:bg-primary hover:text-white rounded transition-all duration-200 cursor-pointer" title="Edit">
                                                        <i class="size-4" data-lucide="edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn size-7 flex items-center justify-center bg-danger/10 text-danger hover:bg-danger hover:text-white rounded transition-all duration-200 cursor-pointer dynamic-action-btn" 
                                                            title="Delete"
                                                            data-hs-overlay="#dynamic-action-modal"
                                                            data-action-url="{{ route('projects.destroy', $project->id) }}"
                                                            data-action-type="delete">
                                                        <i class="size-4" data-lucide="trash-2"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-3.5 py-8 text-center text-default-500">
                                                No projects registered yet.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="mt-5">
                {{ $projects->links() }}
            </div>
        </div>
    </div>

    <!-- Dynamic Action Modal -->
    <div id="dynamic-action-modal" class="hs-overlay hidden size-full fixed top-0 start-0 z-[80] overflow-x-hidden overflow-y-auto pointer-events-none">
        <div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-lg sm:w-full m-3 sm:mx-auto min-h-[calc(100%-3.5rem)] flex items-center">
            <div class="w-full flex flex-col bg-white border shadow-sm rounded-xl pointer-events-auto dark:bg-zinc-900 dark:border-zinc-800 dark:shadow-slate-700/70">
                <div class="p-6 overflow-y-auto text-center">
                    <div class="inline-flex justify-center items-center size-[62px] rounded-full border-4 border-warning/20 bg-warning/10 text-warning mb-4">
                        <i class="size-6" data-lucide="alert-triangle"></i>
                    </div>
                    <h3 id="modal-title" class="mb-2 text-xl font-bold text-default-800">Are you sure?</h3>
                    <p id="modal-description" class="text-default-500 font-sans">Do you really want to perform this action?</p>
                    <form id="modal-action-form" method="POST" action="">
                        @csrf
                        <input type="hidden" name="_method" id="modal-method" value="DELETE">
                        <div class="mt-8 flex justify-center gap-3">
                            <button type="button" class="btn bg-default-200 text-default-800 hover:bg-default-300 transition-colors" data-hs-overlay="#dynamic-action-modal">Cancel</button>
                            <button type="submit" id="modal-submit-btn" class="btn bg-danger text-white transition-colors">Yes, Confirm</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const actionButtons = document.querySelectorAll('.dynamic-action-btn');
        const modalForm = document.getElementById('modal-action-form');
        const modalTitle = document.getElementById('modal-title');
        const modalDescription = document.getElementById('modal-description');
        const modalMethod = document.getElementById('modal-method');
        const modalSubmitBtn = document.getElementById('modal-submit-btn');

        actionButtons.forEach(button => {
            button.addEventListener('click', function () {
                const actionUrl = this.getAttribute('data-action-url');
                const actionType = this.getAttribute('data-action-type');
                
                modalForm.action = actionUrl;
                
                if (actionType === 'delete') {
                    modalMethod.value = 'DELETE';
                    modalTitle.textContent = 'Move to Trash?';
                    modalDescription.textContent = 'Are you sure you want to soft-delete this project? It can be restored later from the archive.';
                    modalSubmitBtn.className = 'btn bg-danger text-white hover:bg-red-700';
                    modalSubmitBtn.innerHTML = 'Yes, Delete';
                }
            });
        });
    });
</script>
@endsection
