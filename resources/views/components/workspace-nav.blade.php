<nav aria-label="Workspace navigation" class="my-6 flex flex-wrap gap-2">
@foreach(['user.dashboarduser' => 'Overview', 'myorders.index' => 'My Orders', 'user.chat.index' => 'Messages', 'user.reviews.index' => 'Reviews', 'user.skill-profile.edit' => 'My Skill Profile'] as $route => $label)
<a href="{{ route($route) }}" @if(request()->routeIs($route)) aria-current="page" @endif class="btn btn-sm {{ request()->routeIs($route) ? 'bg-emerald-600 text-white' : '' }}">{{ $label }}</a>
@endforeach
</nav>
