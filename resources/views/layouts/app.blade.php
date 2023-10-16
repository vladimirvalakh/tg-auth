<?php
use App\Models\Role;
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src='https://cdnjs.cloudflare.com/ajax/libs/cleave.js/1.6.0/addons/cleave-phone.us.js'></script>

        <link rel="stylesheet" href="{{ asset("/css/app.css") }}">

    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main role="main">
                @include('flash-message')

                @if (auth()->user()->role && auth()->user()->role->slug === Role::ARENDATOR_SLUG)
                    @if (request()->routeIs('sites') || request()->routeIs('orders'))
                        @include('profile.partials.update-cities-form')
                    @endif
                @endif

                {{ $slot }}
            </main>
        </div>
    </body>

    @include('modals.first-screen-modal')

    <script>
{{--        @if (auth()->user()->role && auth()->user()->role->slug === Role::ARENDATOR_SLUG && auth()->user()->first_screen)--}}
{{--        $("#first-screen-modal").modal('show');--}}
{{--        @endif--}}

        $('select').select2({
            "language": {
                "noResults": function(){
                    return "Ничего не найдено";
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        $('select:not(#select-cities-top-form,#select-region-top-form)').on('select2:select', function () {
            $('#grid_view_search_button').click();
        });

        $('select:not(#select-cities-top-form,#select-region-top-form)').on('select2:close', function () {
            $('#grid_view_search_button').click();
        });

        $('#grid_view_reset_button').hide();
    </script>

</html>
