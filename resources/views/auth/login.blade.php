<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="flex items-center justify-center mt-4">
            <script async src="https://telegram.org/js/telegram-widget.js?22" data-telegram-login="TgAuthVladimirTestBot" data-size="large" data-auth-url="https://sinclair.com4.ru/auth/telegram" data-request-access="write"></script>
        </div>
    </form>
</x-guest-layout>
