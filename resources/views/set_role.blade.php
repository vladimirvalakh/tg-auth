<x-app-layout>
    <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12 offset-lg-2 offset-md-1 float-md-center">
        <div class="jumbotron">
            <div class="container">
                <div class="container-fluid">
                    <div class="column">
                        <header>
                            <h4 class="font-medium text-gray-900">
                                Вам не назначена роль
                            </h4>
                        </header>

                        <form method="post" action="{{ route('role.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('patch')

                            <div>
                                <x-input-label for="role_id" value="Выберите из списка" />

                                <div class="form-group">
                                    <select name="role_id" class="form-control" id="role_id">
                                        @foreach($roles as $key => $role)
                                            <option value="{{ $key }}" >
                                                {{ $role }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <button class="btn btn-md btn-primary" type="submit">Сохранить</button>
                                @if (session('status') === 'role-updated')
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
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
