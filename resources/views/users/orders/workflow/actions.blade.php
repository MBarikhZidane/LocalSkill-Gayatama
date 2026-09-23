@php
    $isProvider = (int) $order->provider_id === (int) auth()->id();
    $isCustomer = (int) $order->customer_id === (int) auth()->id();
    $buttons = [];
    if ($isProvider && $order->status === 'pending') $buttons = ['accept' => 'Accept', 'decline' => 'Decline'];
    if ($isProvider && $order->status === 'in_progress') $buttons = ['submit' => 'Mark Service Done'];
    if ($isProvider && $order->status === 'issue_reported') $buttons = ['revision' => 'Start Revision'];
    if ($isCustomer && $order->status === 'submitted') $buttons = ['confirm' => 'Confirm Completion', 'issues' => 'Report an Issue'];
@endphp
<div class="flex flex-wrap items-center gap-2" data-workflow-actions="{{ $order->id }}" data-status="{{ $order->status }}">
    @foreach($buttons as $action => $label)
        <button type="button" class="btn btn-sm {{ $action === 'decline' || $action === 'issues' ? 'btn-outline' : 'bg-emerald-600 text-white' }}" data-order-action="{{ $action }}" data-order-url="{{ route('user.workflow.write', [$order, $action]) }}" data-order-title="{{ $label }}">{{ $label }}</button>
    @endforeach
    <a class="btn btn-outline btn-sm" href="{{ route('user.workflow.show', $order) }}">{{ \App\Services\OrderWorkflow::writable($order) ? ($isProvider ? 'Message Customer' : 'Message Provider') : 'View Conversation' }} <span data-order-unread="{{ $order->id }}" class="badge badge-sm" hidden></span></a>
    <a class="text-sm underline" href="{{ route('user.workflow.show', $order) }}">{{ $order->status === 'issue_reported' ? 'View Issue' : 'View Details' }}</a>
    @if($order->status === 'pending' && !$isProvider)<span class="text-xs">Waiting for the provider's decision.</span>@endif
    @if($order->status === 'submitted')<span class="text-xs">Waiting for customer confirmation.</span>@endif
</div>
