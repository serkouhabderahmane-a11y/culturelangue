<a href="{{ route('student.dashboard') }}" class="admin-nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
<a href="{{ route('student.programs') }}" class="admin-nav-item {{ request()->routeIs('student.programs') ? 'active' : '' }}"><i class="fas fa-book"></i> Mes programmes</a>
<a href="{{ route('student.payments') }}" class="admin-nav-item {{ request()->routeIs('student.payments') ? 'active' : '' }}"><i class="fas fa-credit-card"></i> Paiements</a>
<a href="{{ route('student.profile') }}" class="admin-nav-item {{ request()->routeIs('student.profile') ? 'active' : '' }}"><i class="fas fa-user"></i> Profil</a>
<a href="{{ route('student.calendar') }}" class="admin-nav-item {{ request()->routeIs('student.calendar') ? 'active' : '' }}"><i class="fas fa-calendar"></i> Calendrier</a>
<a href="{{ route('student.level-tests') }}" class="admin-nav-item {{ request()->routeIs('student.level-tests') ? 'active' : '' }}"><i class="fas fa-tasks"></i> Tests de niveau</a>
<a href="{{ route('student.support') }}" class="admin-nav-item {{ request()->routeIs('student.support') ? 'active' : '' }}"><i class="fas fa-headset"></i> Support</a>
