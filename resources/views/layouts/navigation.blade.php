<?php
use App\Models\Role;
?>

@php
    $currentRoleSlug = (auth()->user() && auth()->user()->role && auth()->user()->role->slug) ? auth()->user()->role->slug : null;
@endphp


@if($currentRoleSlug === Role::ARENDATOR_SLUG)
    <nav class="navbar navbar-light bg-light  navbar-expand-md d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3  border-bottom shadow-sm">
        <a class="navbar-brand" href="/">
            <img src="{{ asset("/images/lidmarket_logo.png") }}" height="50" class="d-inline-block align-top" alt="">
        </a>
        <div class="collapse navbar-collapse">
            <div class="mr-auto header-center-text" >
                <div class="text-center secondary-text">Здесь вы можете арендовать сайты по остеклению в вашем регионе</div>
            </div>
            {{--        <ul class="nav mr-auto list-inline  justify-content-center text-center">--}}
            {{--            <li class="container ">--}}
            {{--                <a class="nav-link active" href="#">Active</a>--}}
            {{--            </li>--}}
            {{--        </ul>--}}


            <ul class="navbar-nav">

                @if($currentRoleSlug === Role::ARENDATOR_SLUG)
                    <li class="nav-item">
                        <a type="button" class="btn mt-1 mr-2" href="#">Баланс:<br /> <b>0</b> р.</a>

                    </li>

                    <li class="nav-item">
                        <a type="button" class="btn mt-1 mr-2" href="#">Заблокировано:<br /> <b>0</b> р.</a>
                    </li>

                    <li class="nav-item">
                        <a type="button" class="btn btn-primary mt-3 mr-2" href="#">Пополнить счёт</a>
                    </li>
                @endif


                @auth
                    <li class="nav-item dropdown" id="account_navbar_menu">
                        <a class="nav-link dropdown-toggle"
                           href="#" id="navbarDropdown"
                           role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true"
                           aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        @if (auth()->user()->role)
                            <div class="account_role">{{auth()->user()->role->name}}</div>
                        @endif

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{route('profile.edit')}}">Профиль</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{route('orders')}}">Личный кабинет (Заявки)</a>
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

    <nav class="navbar navbar-light navbar-expand-md d-flex flex-column flex-md-row p-2 px-md-1 mb-3 pl-2 border-bottom shadow-sm arendator-menu">
        <div class="collapse navbar-collapse arendator-menu-container">
            <ul class="navbar-nav mr-auto p-2 container-fluid col-12">
                <div class="col-12">
                    @if($currentRoleSlug === Role::ARENDATOR_SLUG)
                        <a type="button" class="btn @if (request()->routeIs('sites')) btn-primary @endif" href="{{route('sites')}}">Все сайты</a>
                        <a type="button" class="btn @if (request()->routeIs('orders')) btn-primary @endif" href="{{route('orders')}}">Мои сайты</a>
                        <a type="button" class="btn @if (request()->routeIs('leads')) btn-primary @endif" href="{{route('leads')}}">Мои лиды</a>
                        <a type="button" class="btn btn-primary float-right" href="{{route('support')}}">Техподдержка</a>
                    @endif

                    @if($currentRoleSlug === Role::MODERATOR_SLUG)
                        <a type="button" class="btn @if (request()->routeIs('orders')) btn-primary @endif" href="{{route('orders')}}">Заявки</a>
                        <a type="button" class="btn @if (request()->routeIs('sites')) btn-primary @endif" href="{{route('sites')}}">Сайты</a>
                    @endif

                    @if($currentRoleSlug === Role::OWNER_SLUG || $currentRoleSlug === Role::ADMINISTRATOR_SLUG)
                        <a type="button" class="btn @if (request()->routeIs('sites')) btn-primary @endif" href="{{route('sites')}}">Сайты</a>
                    @endif

                    @if($currentRoleSlug === Role::ADMINISTRATOR_SLUG)
                            <a type="button" class="btn @if (request()->routeIs('categories')) btn-primary @endif" href="{{route('categories')}}">Категории</a>
                            <a type="button" class="btn @if (request()->routeIs('profiles')) btn-primary @endif" href="{{route('profiles')}}">Пользователи</a>
                            <a type="button" class="btn @if (request()->routeIs('roles')) btn-primary @endif" href="{{route('roles')}}">Роли</a>
                    @endif
                </div>
            </ul>
        </div>
    </nav>
    <p class="p-3 text-secondary arendator-welcome">
        <strong>Источник заявок:</strong> Лэндинги и многостраничные сайты<br/>
        <strong>Траффик:</strong> из поисковой выдачи Яндекса и контекстной рекламы
    </p>
@else
    <nav class="navbar navbar-light bg-light  navbar-expand-md d-flex flex-column flex-md-row align-items-center p-3 px-md-4 mb-3  border-bottom shadow-sm">
        <a class="navbar-brand" href="/">
            <img src="{{ asset("/images/lidmarket_logo.png") }}" height="50" class="d-inline-block align-top" alt="">
        </a>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                @if($currentRoleSlug === Role::MODERATOR_SLUG)
                    <li class="nav-item @if (request()->routeIs('orders')) active @endif">
                        <a class="nav-link" href="{{route('orders')}}">Заявки</a>
                    </li>
                    <li class="nav-item @if (request()->routeIs('sites')) active @endif">
                        <a class="nav-link" href="{{route('sites')}}">Сайты</a>
                    </li>
                @endif

                @if($currentRoleSlug === Role::ADMINISTRATOR_SLUG)
                    <li class="nav-item @if (request()->routeIs('sites')) active @endif">
                        <a class="nav-link" href="{{route('sites')}}">Сайты</a>
                    </li>
                @endif

                @if($currentRoleSlug === Role::ADMINISTRATOR_SLUG)
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

            <ul class="navbar-nav">
                @if($currentRoleSlug === Role::OWNER_SLUG)
                    <li class="nav-item">
                        <a type="button" class="btn mt-1 mr-3" href="#">Баланс:<br /> <b>0</b> р.</a>

                    </li>

                    <li class="nav-item">
                        <a type="button" class="btn mt-1 mr-3" href="#">Холд:<br /> <b>0</b> р.</a>
                    </li>

                    <li class="nav-item">
                        <a type="button" class="btn btn-primary mt-3 mr-3" href="#">Вывести деньги</a>
                    </li>
                @endif


                @auth
                    <li class="nav-item dropdown" id="account_navbar_menu">
                        <a class="nav-link dropdown-toggle"
                           href="#" id="navbarDropdown"
                           role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true"
                           aria-expanded="false">
                            {{ Auth::user()->name }}
                        </a>
                        @if (auth()->user()->role)
                            <div class="account_role">{{auth()->user()->role->name}}</div>
                        @endif

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{route('profile.edit')}}">Профиль</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{route('orders')}}">Личный кабинет (Заявки)</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{route('profile.settings')}}">Настройки аккаунта</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="{{route('profile.info.api_details')}}">Настройка API</a>
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
@endif


