@once
<div id="workflow-feedback" role="status" aria-live="polite" class="hidden fixed bottom-4 left-4 right-4 z-50 rounded-xl border bg-white p-4 text-slate-900 shadow-lg dark:bg-slate-800 dark:text-white"></div>
<dialog id="workflow-dialog" class="modal">
    <div class="modal-box bg-white text-slate-900 dark:bg-slate-900 dark:text-white">
        <form id="workflow-action-form" method="POST" enctype="multipart/form-data">
            @csrf
            <h2 id="workflow-dialog-title" class="text-lg font-bold"></h2>
            <p id="workflow-dialog-description" class="my-3 text-sm"></p>
            <input type="hidden" name="confirmation" value="1">
            <label id="workflow-note-label" class="block text-sm">Note <textarea name="body" class="textarea textarea-bordered my-2 w-full bg-transparent" maxlength="5000" rows="4"></textarea></label>
            <label id="workflow-files-label" class="block text-sm">Attachments (up to 3, 5 MiB each)
                <input name="attachments[]" type="file" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.txt" class="my-2 block w-full">
            </label>
            <p data-form-error role="alert" class="text-sm text-rose-600 dark:text-rose-400"></p>
            <div class="modal-action"><button type="button" id="workflow-cancel" class="btn">Cancel</button><button type="submit" class="btn bg-emerald-600 text-white">Confirm</button></div>
        </form>
    </div>
</dialog>
<script src="{{ asset('js/order-workflow.js') }}" defer data-summary-url="{{ route('user.workflow.summary') }}"></script>
@endonce
