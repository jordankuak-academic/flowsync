<aside class="app-sidebar bg-white border-end d-flex flex-column align-items-center py-4 gap-3 shadow-sm" style="width: var(--sidebar-width); min-height: calc(100vh - var(--header-height));">
    <nav class="w-100 d-flex flex-column align-items-center gap-2">
        <a href="{{ route('dashboard') }}" 
           class="nav-item d-flex flex-column align-items-center justify-content-center rounded-3 text-decoration-none transition {{ request()->routeIs('dashboard') ? 'active bg-light text-primary' : 'text-secondary' }}" 
           style="width: 64px; height: 72px;"
           data-tooltip="true" data-tooltip-placement="right" title="Dashboard">
            <i class="bi bi-grid-fill fs-4 {{ request()->routeIs('dashboard') ? 'text-primary' : 'text-secondary' }}"></i>
            <span class="small fw-bold mt-1" style="font-size: 10px;">Home</span>
        </a>
        
        <a href="{{ route('project') }}" 
           class="nav-item d-flex flex-column align-items-center justify-content-center rounded-3 text-decoration-none transition {{ request()->routeIs('project*') ? 'active bg-light text-primary' : 'text-secondary' }}" 
           style="width: 64px; height: 72px;"
           data-tooltip="true" data-tooltip-placement="right" title="Projects">
            <i class="bi bi-kanban fs-4 {{ request()->routeIs('project*') ? 'text-primary' : 'text-secondary' }}"></i>
            <span class="small fw-bold mt-1" style="font-size: 10px;">Project</span>
        </a>
        
        <a href="{{ route('team') }}" 
           class="nav-item d-flex flex-column align-items-center justify-content-center rounded-3 text-decoration-none transition {{ request()->routeIs('team*') ? 'active bg-light text-primary' : 'text-secondary' }}" 
           style="width: 64px; height: 72px;"
           data-tooltip="true" data-tooltip-placement="right" title="Team">
            <i class="bi bi-people-fill fs-4 {{ request()->routeIs('team*') ? 'text-primary' : 'text-secondary' }}"></i>
            <span class="small fw-bold mt-1" style="font-size: 10px;">Team</span>
        </a>    
    </nav>
    
    <div class="mt-auto pb-3">
        <a href="{{ route('login') }}" class="btn btn-link text-secondary p-0" data-tooltip="true" data-tooltip-placement="right" title="Logout">
            <i class="bi bi-box-arrow-right fs-4"></i>
        </a>
    </div>
</aside>
