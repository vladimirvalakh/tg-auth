<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Данные о пользователе
        </h2>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="form-group">
            <x-input-label for="name" value="Имя" />
            <x-text-input id="name" name="name" type="text" class="form-control mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        @if ($user->role)
        <div class="form-group">
            <x-input-label for="role" value="Роль" />
            <x-text-input id="role" name="role" type="text" disabled class="form-control mt-1 block w-full" :value="old('role', $user->role->name)" />
        </div>
        @endif



        <div class="form-group flex items-center gap-4">
            <button class="btn btn-md btn-primary" type="submit">Сохранить</button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 float-right text-success"
                >Сохранено</p>
            @endif
        </div>
    </form>
</section>
