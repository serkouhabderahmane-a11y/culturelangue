<a href="{{ route('teacher.dashboard') }}" class="admin-nav-item {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
<a href="{{ route('teacher.students') }}" class="admin-nav-item {{ request()->routeIs('teacher.students') ? 'active' : '' }}"><i class="fas fa-user-graduate"></i> Étudiants</a>
<a href="{{ route('teacher.schedule') }}" class="admin-nav-item {{ request()->routeIs('teacher.schedule') ? 'active' : '' }}"><i class="fas fa-calendar"></i> Emploi du temps</a>
<a href="{{ route('teacher.profile') }}" class="admin-nav-item {{ request()->routeIs('teacher.profile') ? 'active' : '' }}"><i class="fas fa-user"></i> Profil</a>
