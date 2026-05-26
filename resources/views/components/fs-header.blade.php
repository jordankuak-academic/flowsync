<header class="app-header">

    <a href="{{ url('/dashboard') }}" class="logo-text">
        FlowSync
    </a>

    <div class="profile-section">

        <div class="profile-info">

            <div class="profile-name">
                {{ $userName ?? 'Andrew' }}
            </div>

            <div class="profile-role">
                {{ $userRole ?? 'Project Supervisor' }}
            </div>

        </div>

        <div class="profile-circle">
            {{ strtoupper(substr($userName ?? 'A', 0, 1)) }}
        </div>

    </div>

</header>