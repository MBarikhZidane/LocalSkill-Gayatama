@extends('layouts.landingpage')

@section('title', 'My Orders | LOCALSKILL')

@section('content')
    <main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:py-12">
        <p class="text-xs font-bold uppercase tracking-widest text-emerald-700">
            {{ auth()->user()->name }}'s workspace
        </p>
        <h1 class="mt-3 text-3xl font-bold">Messages</h1>
        <p class="mt-3 max-w-3xl leading-7 text-slate-600">
            Keep your order conversations together, whether you are chatting with a provider or a customer.
        </p>

        <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)]">
            {{-- List Conversations --}}
            <section class="min-w-0 rounded-2xl border border-slate-200 bg-white p-5" aria-labelledby="conversations-title">
                <h2 id="conversations-title" class="text-xl font-bold">
                    Conversations
                </h2>
                <div id="conversation-list" class="mt-4 grid max-h-80 gap-2 overflow-y-auto lg:max-h-[36rem]">
                    <p class="text-sm text-slate-500">Loading conversations...</p>
                </div>
            </section>

            {{-- Detail Message --}}
            <section id="conversation" class="hidden min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white" aria-labelledby="conversation-name">
                <div class="border-b border-slate-200 p-6">
                    <p id="conversation-role" class="text-xs font-bold uppercase tracking-wide text-emerald-700"></p>
                    <h2 id="conversation-name" class="mt-2 text-xl font-bold"></h2>
                    <p id="conversation-order" class="mt-2 text-sm text-slate-600"></p>
                    <a id="conversation-service" href="#" class="mt-3 inline-block text-sm font-semibold text-emerald-700 underline">View service</a>
                </div>

                <ol id="chat-log" role="log" class="flex max-h-96 min-h-64 flex-col gap-4 overflow-y-auto bg-slate-50 p-5"></ol>

                <form id="message-form" class="grid gap-3 border-t border-slate-200 p-6">
                    @csrf
                    <label class="grid gap-2 text-sm font-semibold">Your message
                        <textarea name="message" id="message-input" required maxlength="1000" rows="3" class="textarea textarea-bordered w-full" placeholder="Write about the brief, timing, or delivery..."></textarea>
                    </label>
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <p class="text-xs text-slate-500">
                            Press send to reply
                        </p>
                        <button type="submit" id="send-btn" class="btn border-0 bg-emerald-600 text-white hover:bg-emerald-700">
                            Send message
                        </button>
                    </div>
                </form>
            </section>

            {{-- Empty State --}}
            <section id="conversation-empty" class="rounded-2xl border border-slate-200 bg-white p-8">
                <h2 class="text-xl font-bold">Choose a conversation</h2>
                <p id="conversation-empty-text" class="mt-3 text-slate-600">
                    Select an order on the left to see its provider or customer conversation.
                </p>
            </section>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let activeConversationId = null;

            const conversationListEl = document.getElementById('conversation-list');
            const conversationEl = document.getElementById('conversation');
            const emptyEl = document.getElementById('conversation-empty');
            
            const roleEl = document.getElementById('conversation-role');
            const nameEl = document.getElementById('conversation-name');
            const orderEl = document.getElementById('conversation-order');
            const serviceEl = document.getElementById('conversation-service');
            const chatLogEl = document.getElementById('chat-log');
            const messageForm = document.getElementById('message-form');
            const messageInput = document.getElementById('message-input');

            // Load Daftar Chat
            function loadConversations() {
                fetch('/chat/conversations')
                    .then(res => res.json())
                    .then(data => {
                        conversationListEl.innerHTML = '';

                        if (data.length === 0) {
                            conversationListEl.innerHTML = '<p class="text-sm text-slate-500">No conversations found.</p>';
                            return;
                        }

                        data.forEach(item => {
                            const btn = document.createElement('button');
                            btn.className = `w-full text-left p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition flex flex-col gap-1 ${activeConversationId === item.id ? 'bg-emerald-50 border-emerald-300' : ''}`;
                            btn.onclick = () => selectConversation(item.id);

                            btn.innerHTML = `
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-emerald-700 uppercase">${item.role_label}</span>
                                    <span class="text-xs text-slate-400">${item.last_message_time}</span>
                                </div>
                                <h3 class="font-bold text-slate-800">${item.other_user_name}</h3>
                                <p class="text-xs text-slate-500 truncate">${item.service_title} (#${item.order_number})</p>
                                <p class="text-xs text-slate-600 line-clamp-1 mt-1">${item.last_message}</p>
                            `;
                            conversationListEl.appendChild(btn);
                        });
                    });
            }

            // Pilih Percakapan
            function selectConversation(id) {
                activeConversationId = id;
                emptyEl.classList.add('hidden');
                conversationEl.classList.remove('hidden');

                fetch(`/chat/conversations/${id}`)
                    .then(res => res.json())
                    .then(data => {
                        const conv = data.conversation;
                        roleEl.textContent = conv.role_label;
                        nameEl.textContent = conv.other_user_name;
                        orderEl.textContent = `Order #${conv.order_number} - ${conv.service_title}`;
                        serviceEl.href = conv.service_url;

                        renderMessages(data.messages);
                        loadConversations(); // refresh list
                    });
            }

            // Render Balon Chat
            function renderMessages(messages) {
                chatLogEl.innerHTML = '';
                messages.forEach(msg => {
                    const li = document.createElement('li');
                    li.className = `flex flex-col max-w-[80%] ${msg.is_me ? 'ml-auto items-end' : 'mr-auto items-start'}`;

                    li.innerHTML = `
                        <span class="text-[10px] text-slate-400 mb-1">${msg.sender_name}</span>
                        <div class="rounded-2xl px-4 py-2.5 text-sm ${msg.is_me ? 'bg-emerald-600 text-white rounded-br-none' : 'bg-white border border-slate-200 text-slate-800 rounded-bl-none'}">
                            ${msg.message}
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1">${msg.time}</span>
                    `;
                    chatLogEl.appendChild(li);
                });

                // Scroll ke pesan terbawah
                chatLogEl.scrollTop = chatLogEl.scrollHeight;
            }

            // Kirim Pesan
            messageForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const message = messageInput.value.trim();
                if (!message || !activeConversationId) return;

                fetch(`/chat/conversations/${activeConversationId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({ message })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        messageInput.value = '';
                        selectConversation(activeConversationId);
                    }
                });
            });

            // Jalankan saat pertama kali dibuka
            loadConversations();
        });
    </script>
@endsection