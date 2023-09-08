<form method="post" action="{{ route('profile.destroy') }}" class="p-6">
    @csrf
    @method('delete')
    <div class="mt-6 row justify-content-center">
        <x-danger-button class="btn btn-danger btn-sm control-form ml-3" data-toggle="modal" data-target="#delete-account-modal">
            Удалить аккаунт и выйти
        </x-danger-button>
    </div>
</form>
