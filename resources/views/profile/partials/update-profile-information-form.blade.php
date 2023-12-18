<?php
$bankCards = json_decode($user->bank_cards, true);
?>

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
            <x-input-label for="id" value="ID пользователя в системе" />
            <x-text-input id="id" name="id" type="text" class="form-control mt-1 block w-full" :value="old('id', $user->id)" disabled  />
        </div>

        <div class="form-group">
            <x-input-label for="name" value="Имя" />
            <x-text-input id="name" name="name" type="text" class="form-control mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="form-group">
            <x-input-label for="full_name" value="Ф.И.O" />
            <x-text-input id="full_name" name="full_name" type="text" class="form-control mt-1 block w-full" :value="old('full_name', $user->full_name)" autofocus autocomplete="full_name" />
            <x-input-error class="mt-2" :messages="$errors->get('full_name')" />
        </div>

        <div class="form-group">
            <x-input-label for="phone" value="Телефон (в формате +7 9×× ××× ××××)" />
            <x-text-input id="phone" name="phone" pattern="+7 9[0-9]{2} [0-9]{3} [0-9]{4}" type="text" class="form-control mt-1 block w-full input-phone-field" :value="old('phone', $user->phone)" autofocus autocomplete="phone" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <hr />
        <h4>Банковские карты</h4>
        <div class="form-row">
            <div class="col">
                <x-input-label for="bank1" value="Наименование банка" />
                <input type="text" class="form-control" name="bank1" @if(!empty($bankCards[0]['bank'])) value="{{$bankCards[0]['bank']}}"@endif>
            </div>
            <div class="col">
                <x-input-label for="card1" value="Номер карты в формате XXXX XXXX XXXX XXXX" />
                <input type="text" pattern="[0-9]{4}\s[0-9]{4}\s[0-9]{4}\s[0-9]{4}" class="form-control" name="card1" @if(!empty($bankCards[0]['card_number'])) value="{{$bankCards[0]['card_number']}}"@endif>
            </div>
        </div>
        <div class="form-row">
            <div class="col">
                <x-input-label for="bank2" value="Наименование банка" />
                <input type="text" class="form-control" name="bank2" @if(!empty($bankCards[1]['bank'])) value="{{$bankCards[1]['bank']}}"@endif>
            </div>
            <div class="col">
                <x-input-label for="card2" value="Номер карты в формате XXXX XXXX XXXX XXXX" />
                <input type="text" pattern="[0-9]{4}\s[0-9]{4}\s[0-9]{4}\s[0-9]{4}" class="form-control" name="card2" @if(!empty($bankCards[1]['card_number'])) value="{{$bankCards[1]['card_number']}}"@endif>
            </div>
        </div>
        <div class="form-row">
            <div class="col">
                <x-input-label for="bank3" value="Наименование банка" />
                <input type="text" class="form-control" name="bank3" @if(!empty($bankCards[2]['bank'])) value="{{$bankCards[2]['bank']}}"@endif>
            </div>
            <div class="col">
                <x-input-label for="card3" value="Номер карты в формате XXXX XXXX XXXX XXXX" />
                <input type="text" pattern="[0-9]{4}\s[0-9]{4}\s[0-9]{4}\s[0-9]{4}" class="form-control" name="card3" @if(!empty($bankCards[2]['card_number'])) value="{{$bankCards[2]['card_number']}}"@endif>
            </div>
        </div>
        <hr />
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
