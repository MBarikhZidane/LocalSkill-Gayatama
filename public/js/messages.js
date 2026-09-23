document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('conversation-list');
    const panel = document.getElementById('conversation');
    const empty = document.getElementById('conversation-empty');
    const form = document.getElementById('message-form');
    const feedback = document.getElementById('message-feedback');
    const log = document.getElementById('chat-log');
    let active = null, selection = 0;
    const node = (tag, text, classes = '') => {
        const element = document.createElement(tag);
        element.textContent = text;
        element.className = classes;
        return element;
    };
    async function request(url, options = {}) {
        const response = await fetch(url, {...options, headers: {Accept: 'application/json', ...options.headers}});
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Unable to load this conversation.');
        return data;
    }
    async function select(id) {
        const current = ++selection;
        try {
            const data = await request(list.dataset.endpoint + '/' + encodeURIComponent(id));
            if (current !== selection) return;
            active = id;
            panel.hidden = false;
            empty.hidden = true;
            document.getElementById('conversation-name').textContent = data.conversation.other_user_name;
            document.getElementById('conversation-role').textContent = data.conversation.role_label;
            document.getElementById('conversation-order').textContent = data.conversation.order_number + ' · ' + data.conversation.service_title;
            document.getElementById('conversation-service').href = data.conversation.service_url;
            log.replaceChildren();
            data.messages.forEach(message => {
                const item = node('li', '', 'max-w-[90%] rounded-2xl p-4 ' + (message.is_me ? 'self-end bg-emerald-100 text-emerald-950' : 'self-start border border-slate-200 bg-white'));
                item.append(node('p', message.sender_name, 'text-xs font-bold'), node('p', message.message, 'mt-2 whitespace-pre-wrap text-sm leading-6'), node('p', message.time, 'mt-2 text-xs text-slate-500'));
                log.append(item);
            });
            log.scrollTop = log.scrollHeight;
        } catch (error) { empty.hidden = false; document.getElementById('conversation-empty-text').textContent = error.message; }
    }
    async function load() {
        try {
            const data = await request(list.dataset.endpoint);
            list.replaceChildren();
            data.forEach(item => {
                const button = node('button', '', 'rounded-xl border border-slate-200 p-4 text-left hover:bg-slate-50');
                button.type = 'button';
                button.append(node('span', item.other_user_name, 'block font-bold'), node('span', item.role_label, 'mt-1 block text-xs font-semibold text-emerald-700'), node('span', item.service_title, 'mt-2 block text-sm text-slate-600'), node('span', item.order_number, 'mt-1 block text-xs text-slate-500'));
                button.addEventListener('click', () => select(item.id));
                list.append(button);
            });
            if (!data.length) list.append(node('p', 'No order conversations yet.', 'text-sm text-slate-500'));
            const order = new URLSearchParams(location.search).get('order');
            const selected = data.find(item => String(item.order_id) === order);
            if (selected) await select(selected.id);
        } catch (error) { list.replaceChildren(node('p', error.message, 'text-sm text-red-700')); }
    }
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (!active || !form.reportValidity()) return;
        const message = form.elements.message.value.trim();
        if (!message) return;
        const button = form.querySelector('button');
        button.disabled = true;
        try {
            await request(list.dataset.endpoint + '/' + encodeURIComponent(active) + '/send', {method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}, body: JSON.stringify({message})});
            form.reset();
            feedback.textContent = 'Message sent.';
            await select(active);
        } catch (error) { feedback.textContent = error.message; }
        finally { button.disabled = false; }
    });
    load();
});
