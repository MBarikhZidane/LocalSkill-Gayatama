(() => {
    'use strict';
    const script = document.querySelector('script[data-summary-url]');
    if (!script || window.orderWorkflowLoaded) return;
    window.orderWorkflowLoaded = true;
    const dialog = document.getElementById('workflow-dialog');
    const actionForm = document.getElementById('workflow-action-form');
    const conversation = document.getElementById('order-conversation');
    const csrf = actionForm.querySelector('[name="_token"]').value;
    const feedback = text => {
        const box = document.getElementById('workflow-feedback');
        box.textContent = text;
        box.classList.remove('hidden');
        setTimeout(() => box.classList.add('hidden'), 9000);
    };
    const request = async (url, options = {}) => {
        const response = await fetch(url, { credentials: 'same-origin', ...options, headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf, ...options.headers } });
        let data;
        try { data = await response.json(); } catch (_) { data = { message: 'The request failed. Check your connection or sign in again.' }; }
        if (!response.ok) {
            const error = new Error(data.errors ? Object.values(data.errors).flat().join(' ') : data.message || 'Request failed.');
            error.status = response.status;
            throw error;
        }
        return data;
    };
    document.addEventListener('click', event => {
        const button = event.target.closest('[data-order-action]');
        if (!button) return;
        actionForm.reset();
        actionForm.querySelector('[data-form-error]').textContent = '';
        actionForm.action = button.dataset.orderUrl;
        const action = button.dataset.orderAction;
        document.getElementById('workflow-dialog-title').textContent = action === 'accept' ? 'Accept this order?' : action === 'decline' ? 'Decline this order?' : button.dataset.orderTitle;
        document.getElementById('workflow-dialog-description').textContent = {
            accept: 'The order will become In Progress.', decline: 'The order will be Declined. Its history will remain available.',
            submit: 'Describe the completed service. The customer will confirm completion or report an issue.',
            confirm: 'Confirm that the service is complete. This closes the order and conversation.',
            issues: 'Describe the issue so the provider can discuss it and make revisions.',
            revision: 'Return this order to In Progress and begin the revision.'
        }[action];
        const note = actionForm.elements.body;
        note.required = ['submit', 'issues'].includes(action);
        note.maxLength = action === 'decline' ? 1000 : 5000;
        document.getElementById('workflow-note-label').hidden = ['accept', 'confirm'].includes(action);
        document.getElementById('workflow-files-label').hidden = !['submit', 'issues'].includes(action);
        dialog.showModal();
    });
    document.getElementById('workflow-cancel').addEventListener('click', () => dialog.close());
    let summaryBusy = false;
    const refreshSummary = async () => {
        if (document.hidden || summaryBusy || dialog.open) return;
        summaryBusy = true;
        try {
            const url = new URL(script.dataset.summaryUrl, location.origin);
            const ids = [...new Set([...document.querySelectorAll('[data-workflow-actions]')].map(e => e.dataset.workflowActions))];
            ids.slice(0, 50).forEach(id => url.searchParams.append('ids[]', id));
            const data = await request(url);
            // Add one badge per existing message link, including mobile navigation.
            document.querySelectorAll('a[href]').forEach(link => {
                if (new URL(link.href).pathname !== '/user/messages') return;
                let badge = link.querySelector('[data-workflow-total]');
                if (!badge) { badge = document.createElement('span'); badge.dataset.workflowTotal = ''; badge.className = 'badge badge-sm ml-2'; link.append(badge); }
                badge.textContent = data.total;
                badge.hidden = !data.total;
            });
            let filteredStatusChanged = false;
            for (const order of data.orders) {
                document.querySelectorAll(`[data-workflow-actions="${order.id}"]`).forEach(node => {
                    if (node.dataset.status !== order.status && new URLSearchParams(location.search).get('status') && new URLSearchParams(location.search).get('status') !== 'all') filteredStatusChanged = true;
                    node.outerHTML = order.actions;
                });
                document.querySelectorAll(`[data-order-status="${order.id}"]`).forEach(node => node.textContent = order.label);
                document.querySelectorAll(`[data-order-unread="${order.id}"]`).forEach(node => { node.textContent = order.unread; node.hidden = !order.unread; });
            }
            document.querySelectorAll('[data-workflow-pending]').forEach(node => node.textContent = data.pending);
            document.querySelectorAll('[data-workflow-completed]').forEach(node => node.textContent = data.completed);
            if (filteredStatusChanged && !conversation && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) location.reload();
        } catch (error) { /* Keep the current page usable; the next visible poll retries. */ }
        finally { summaryBusy = false; }
    };
    const submit = async (form, onSuccess) => {
        const button = form.querySelector('[type="submit"]');
        if (button.disabled) return;
        button.disabled = true;
        const errorNode = form.querySelector('[data-form-error]');
        errorNode.textContent = '';
        try {
            await request(form.action, { method: 'POST', body: new FormData(form) });
            await onSuccess();
            feedback('Saved.');
        } catch (error) {
            errorNode.textContent = error.message;
            if (error.status === 409) {
                // Keep the draft, but refresh stale controls from committed server state.
                feedback(error.message);
                if (form === actionForm) dialog.close();
                await refreshSummary();
                if (conversation) await loadMessages();
            }
        } finally { button.disabled = false; }
    };
    actionForm.addEventListener('submit', event => {
        event.preventDefault();
        submit(actionForm, async () => {
            dialog.close();
            await refreshSummary();
            if (conversation) await loadMessages(true);
        });
    });
    let cursor = 0, historyBusy = false, hasMore = false, pendingReceipt = null;
    const history = document.getElementById('conversation-messages');
    const nearBottom = () => !history || history.scrollHeight - history.scrollTop - history.clientHeight < 50;
    const markRead = async () => {
        if (!pendingReceipt || document.hidden || !nearBottom()) return;
        const receipt = pendingReceipt;
        try {
            await request(conversation.dataset.readUrl, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ receipt }) });
            if (pendingReceipt === receipt) pendingReceipt = null;
        } catch (_) { /* Retry on focus or the next poll. */ }
    };
    async function loadMessages(manual = false) {
        if (!conversation || historyBusy || document.hidden || (hasMore && !manual)) return;
        historyBusy = true;
        try {
            const url = new URL(conversation.dataset.historyUrl, location.origin);
            url.searchParams.set('after', cursor);
            const data = await request(url);
            const wasAtBottom = nearBottom();
            for (const message of data.messages) {
                if (document.getElementById(`order-message-${message.id}`)) continue;
                const article = document.createElement('article');
                article.id = `order-message-${message.id}`;
                article.className = 'rounded-lg border border-slate-200 p-3 dark:border-slate-700';
                const heading = document.createElement('p');
                heading.className = 'text-xs font-semibold';
                heading.textContent = `${message.sender} · ${new Date(message.time).toLocaleString()}${message.kind ? ' · ' + message.status : ''}`;
                const body = document.createElement('p');
                body.className = 'mt-2 whitespace-pre-wrap break-words text-sm';
                body.textContent = message.body;
                article.append(heading, body);
                message.attachments.forEach(file => {
                    const link = document.createElement('a');
                    link.href = file.url; link.textContent = file.name; link.className = 'mt-2 block break-all text-sm underline'; article.append(link);
                });
                history.append(article);
            }
            cursor = Math.max(cursor, data.cursor);
            if (data.messages.length) pendingReceipt = data.receipt;
            hasMore = data.has_more;
            document.getElementById('conversation-more').hidden = !hasMore;
            document.getElementById('conversation-form').hidden = !data.writable;
            document.getElementById('conversation-readonly').hidden = data.writable;
            document.querySelectorAll(`[data-order-status="${conversation.dataset.orderId}"]`).forEach(node => node.textContent = data.label);
            document.querySelectorAll(`[data-workflow-actions="${conversation.dataset.orderId}"]`).forEach(node => node.outerHTML = data.actions);
            if (wasAtBottom) history.scrollTop = history.scrollHeight;
            document.getElementById('conversation-error').textContent = '';
            await markRead();
        } catch (error) { document.getElementById('conversation-error').textContent = error.message; }
        finally { historyBusy = false; }
    }
    if (conversation) {
        document.getElementById('conversation-more').addEventListener('click', () => loadMessages(true));
        history.addEventListener('scroll', markRead);
        const form = document.getElementById('conversation-form');
        // sessionStorage survives refreshes in this tab without sending drafts to another user.
        const draftKey = `order-draft-${conversation.dataset.orderId}-${csrf.slice(-16)}`;
        try {
            form.elements.body.value = sessionStorage.getItem(draftKey) || '';
            const savedToken = sessionStorage.getItem(draftKey + '-token');
            if (savedToken) form.elements.token.value = savedToken;
            else sessionStorage.setItem(draftKey + '-token', form.elements.token.value);
        } catch (_) {}
        form.elements.body.addEventListener('input', () => { try { sessionStorage.setItem(draftKey, form.elements.body.value); } catch (_) {} });
        form.addEventListener('submit', event => {
            event.preventDefault();
            submit(form, async () => {
                form.reset(); form.elements.token.value = crypto.randomUUID();
                try { sessionStorage.removeItem(draftKey); sessionStorage.setItem(draftKey + '-token', form.elements.token.value); } catch (_) {}
                await loadMessages(true); await refreshSummary();
            });
        });
        loadMessages();
        setInterval(loadMessages, 10000);
    }
    refreshSummary();
    setInterval(refreshSummary, 30000);
    document.addEventListener('visibilitychange', () => { if (!document.hidden) { refreshSummary(); loadMessages(); } });
    window.addEventListener('focus', () => { refreshSummary(); loadMessages(); });
})();
