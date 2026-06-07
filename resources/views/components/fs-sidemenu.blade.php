@php
    $currentPage = $currentPage ?? request()->route()?->getName() ?? 'team';

    if (str_starts_with($currentPage, 'team')) {
        $currentPage = 'team';
    } elseif ($currentPage !== 'dashboard' && $currentPage !== 'project') {
        $currentPage = 'team';
    }
@endphp

<aside class="app-sidebar">

    <nav>

        <a href="{{ url('/dashboard') }}"
           class="nav-item {{ $currentPage === 'dashboard' ? 'active' : '' }}">

            <i class="bi bi-grid-fill"></i>

            <span>Home</span>

        </a>

        <a href="{{ url('/project') }}"
           class="nav-item {{ $currentPage === 'project' ? 'active' : '' }}">

            <i class="bi bi-kanban"></i>

            <span>Project</span>

        </a>

        <a href="{{ url('/team') }}"
           class="nav-item {{ $currentPage === 'team' ? 'active' : '' }}">

            <i class="bi bi-people-fill"></i>

            <span>Team</span>

        </a>

    </nav>

    <div class="logout">

        <a href="{{ url('/login') }}"
           class="nav-item logout-btn">

            <i class="bi bi-box-arrow-right"></i>

            <span>Logout</span>

        </a>

    </div>

</aside>