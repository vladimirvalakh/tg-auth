@php
    $currentRoleSlug = (auth()->user() && auth()->user()->role && auth()->user()->role->slug) ? auth()->user()->role->slug : null;
@endphp


<nav class="navbar navbar-light bg-light  navbar-expand-md d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3  border-bottom shadow-sm">
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item @if (request()->routeIs('sites')) active @endif">
                <a class="nav-link" href="{{route('sites')}}">Сайты</a>
            </li>

            @if($currentRoleSlug === 'moderator')
                <li class="nav-item @if (request()->routeIs('categories')) active @endif">
                    <a class="nav-link" href="{{route('categories')}}">Категории</a>
                </li>

                <li class="nav-item @if (request()->routeIs('profiles')) active @endif">
                    <a class="nav-link" href="{{route('profiles')}}">Пользователи</a>
                </li>

                <li class="nav-item @if (request()->routeIs('roles')) active @endif">
                    <a class="nav-link" href="{{route('roles')}}">Роли</a>
                </li>
            @endif
        </ul>

        <ul class="navbar-nav  ">
            @auth
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle"
                   href="#" id="navbarDropdown"
                   role="button"
                   data-toggle="dropdown"
                   aria-haspopup="true"
                   aria-expanded="false">
                     {{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{route('profile.edit')}}">Профиль</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="{{route('profile.settings')}}">Настройки аккаунта</a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{route('logout')}}">
                        @csrf
                        <a class="dropdown-item" href="{{route('logout')}}" onclick="event.preventDefault();
                                            this.closest('form').submit();">Выйти</a>
                    </form>
                </div>
            </li>
            @endauth
        </ul>
    </div>
</nav>
