@extends('layouts.landingpage')

@section('title', 'Inbox Notifications | LOCALSKILL')

@section('content')
<main id="content" class="mx-auto w-full max-w-6xl flex-1 px-4 py-6 sm:pb-8 sm:pt-2 transition-colors duration-200">
<a href="{{ route('explore.index') }}"
            class="mb-5 inline-flex items-center gap-1 text-sm font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors">
            <i data-lucide="arrow-left" class="h-4 w-4"></i> Back
        </a>

    {{-- Toast Notification --}}
    @if(session('success'))
        <div class="alert alert-success mb-6 shadow-lg text-white bg-emerald-600">
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">
                {{ Auth::user()->name }}'s workspace &middot; Notifications
            </p>
            <h1 class="mt-2 text-3xl font-extrabold text-slate-900 dark:text-white">Inbox Notifications</h1>
            <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                Stay updated with your service requests, order updates, and activity logs.
            </p>
        </div>

        @if(Auth::user()->unreadNotifications->count() > 0)
            <form action="{{ route('user.notifications.readAll') }}" method="POST" class="mt-4 md:mt-0">
                @csrf
                <button type="submit" class="btn btn-outline btn-emerald text-xs text-emerald-600 dark:text-emerald-400 border-emerald-600 dark:border-emerald-500 hover:bg-emerald-600 hover:text-white">
                    Mark All as Read
                </button>
            </form>
        @endif
    </div>

    {{-- Filter Tabs --}}
    <div class="mb-6 flex border-b border-slate-200 dark:border-slate-700">
        <a href="{{ route('user.notifications.index') }}"
           class="px-4 py-2 font-medium text-sm border-b-2 transition-colors {{ !request('filter') ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
           All Notifications
        </a>
        <a href="{{ route('user.notifications.index', ['filter' => 'unread']) }}"
           class="px-4 py-2 font-medium text-sm border-b-2 transition-colors flex items-center gap-2 {{ request('filter') === 'unread' ? 'border-emerald-600 text-emerald-600 dark:text-emerald-400' : 'border-transparent text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200' }}">
           Unread
           @if(Auth::user()->unreadNotifications->count() > 0)
               <span class="rounded-full bg-emerald-500 px-2 py-0.5 text-[10px] text-white">
                   {{ Auth::user()->unreadNotifications->count() }}
               </span>
           @endif
        </a>
    </div>

    {{-- Notification List --}}
    @if($notifications->count() > 0)
        <div class="grid gap-3">
            @foreach($notifications as $notification)
                @php
                    $data = $notification->data;
                    $isUnread = is_null($notification->read_at);

                    // Menentukan Icon & Warna Badge berdasarkan Tipe Notifikasi
                    $iconColor = match($data['type'] ?? '') {
                        'new_order' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-400',
                        'accepted'  => 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-400',
                        'completed' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/40 dark:text-emerald-400',
                        'cancelled' => 'bg-rose-100 text-rose-600 dark:bg-rose-900/40 dark:text-rose-400',
                        default     => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                    };
                @endphp

                <div class="relative flex items-start justify-between rounded-xl border transition p-4 sm:p-5 {{ $isUnread ? 'bg-emerald-50/40 border-emerald-200 dark:bg-emerald-950/10 dark:border-emerald-900/40' : 'bg-white border-slate-200 dark:bg-slate-800 dark:border-slate-700' }}">

                    <div class="flex items-start gap-4">
                        {{-- Icon Badge --}}
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $iconColor }}">
                            <i data-lucide="bell" class="h-5 w-5"></i>
                        </div>

                        {{-- Main Content --}}
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $data['title'] ?? 'Notification' }}
                                </h3>
                                @if($isUnread)
                                    <span class="inline-block h-2 w-2 rounded-full bg-emerald-500" title="Unread"></span>
                                @endif
                            </div>

                            <p class="text-sm text-slate-600 dark:text-slate-300">
                                {{ $data['message'] ?? '' }}
                            </p>

                            <p class="text-xs text-slate-400 dark:text-slate-500 pt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 shrink-0 ml-4">
                        @if($isUnread)
                            <form action="{{ route('user.notifications.read', $notification->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-ghost btn-xs text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400" title="Mark as read">
                                    <i data-lucide="check" class="h-4 w-4"></i>
                                </button>
                            </form>
                        @endif

                        @if(isset($data['order_id']))
                            <a href="{{ route('user.workflow.show', $data['order_id']) }}" class="btn btn-outline btn-xs dark:border-slate-600 dark:text-slate-300">
                                View Order
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center dark:border-slate-700 dark:bg-slate-800">
            <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <h3 class="mt-2 text-xl font-bold text-slate-900 dark:text-white">No notifications yet</h3>
            <p class="mt-1 text-slate-600 dark:text-slate-400">
                You don't have any notifications at the moment.
            </p>
        </div>
    @endif

</main>
@endsection
