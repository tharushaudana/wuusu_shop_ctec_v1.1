<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
        <div class="sidebar-brand-icon rotate-n-0">
            <img height="50" width="50" style="border-radius: 50px;" src="{{ asset('dashboard/img/mainlogo.jpg') }}"/>
        </div>
        <div class="sidebar-brand-text mx-auto">Wuusu <sup>Admin</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('web.pages.home') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('web.pages.home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Actions
    </div>

    @if (Auth::user()->hasPrivilege(config('userprivis.MANAGE_USERS.SHOW_USERS')))
        <!-- Nav Item - Users -->
        <li class="nav-item {{ request()->routeIs('web.pages.users') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('web.pages.users') }}">
                <i class="fas fa-fw fa-user-alt"></i>
                <span>Users</span></a>
        </li>
    @endif
    
</ul>