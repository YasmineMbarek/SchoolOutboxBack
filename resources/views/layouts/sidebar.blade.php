<style>
    .active{
        color:#fff;

    }
</style>
<aside class="left-sidebar bg-sidebar">
    <div id="sidebar" class="sidebar sidebar-with-footer">
        <div class="app-brand">
            <a href="" title="Sleek Dashboard">
                <svg
                    class="brand-icon"
                    xmlns="http://www.w3.org/2000/svg"
                    preserveAspectRatio="xMidYMid"
                    width="30"
                    height="33"
                    viewBox="0 0 30 33">
                    <g fill="none" fill-rule="evenodd">
                        <path class="logo-fill-blue" fill="#7DBCFF" d="M0 4v25l8 4V0zM22 4v25l8 4V0z"/>
                        <path class="logo-fill-white" fill="#FFF" d="M11 4v25l8 4V0z"/>
                    </g>
                </svg>

                <span class="brand-name text-truncate">School Out Box</span>
            </a>
        </div>

        @if(Auth::user()->role->name==\App\Models\Role::ROLE_SUPERADMIN)
        <div class="sideBar" data-simplebar style="height: 100%;">
            <!-- sidebar menu -->
            <ul class="nav sidebar-inner" id="sidebar-menu">

                <li >
                    <a class="sidenav-item-link" href="{{route ('index')}}">
                        <i class="mdi mdi-trending-up"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>


                <li>
                    <a class="sidenav-item-link" href="{{ route ('categories') }}">
                        <i class="mdi mdi-book-multiple"></i>
                        <span class="nav-text">Categories</span>
                    </a>


                </li>

                <li>
                    <a class="sidenav-item-link" href="{{ route ('regions') }}">
                        <i class="mdi mdi-map-marker"></i>
                        <span class="nav-text">Regions</span>
                    </a>
                </li>

                <li>
                    <a class="sidenav-item-link" href="{{ route ('admins') }}">
                        <i class="mdi mdi-account-star"></i>
                        <span class="nav-text">Admins</span>
                    </a>
                </li>

                <li>
                    <a class="sidenav-item-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">

                        <i class="mdi mdi-logout"></i>
                        <span class="nav-text">Logout</span>

                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>

            </ul>
        </div>
        @elseif(Auth::user()->role->name==\App\Models\Role::ROLE_ADMIN)

        <div class="sideBar" data-simplebar style="height: 100%;">
            <!-- sidebar menu -->
            <ul class="nav sidebar-inner" id="sidebar-menu">

              <li  >
                    <a class="sidenav-item-link menu" href="{{route('dashboardAdmin')}}" >
                        <i class="mdi mdi-trending-up"></i>
                        <span class="nav-text ">Dashboard</span>
                    </a>
                </li>


                <li >
                    <a class="sidenav-item-link" href="{{route ('articles')}}">
                        <i class="mdi mdi-book-multiple"></i>
                        <span class="nav-text">Articles </span>
                    </a>


                </li>

                <!--  <li>
                    <a class="sidenav-item-link" href="{{route('demands')}}">
                        <i class="mdi mdi-map-marker"></i>
                        <span class="nav-text">Demands</span>
                    </a>
                </li>-->
                <li>
                    <a class="sidenav-item-link" href="{{route('profile')}}">
                        <span class="mdi mdi-account-circle"></span>
                        <span class="nav-text" style="margin-left: 10%">Edit profile</span>
                    </a>
                </li>

                <li>
                    <a class="sidenav-item-link" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">

                        <i class="mdi mdi-logout"></i>
                        <span class="nav-text">Logout</span>

                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
        @endif
    </div>
</aside>
<script type="text/javascript">


    $(".sideBar li").each(function() {
        var navItem = $(this);
        if( $(navItem).find("a").attr("href") == location.href ) {
            navItem.addClass("active");
        }
    });



</script>
