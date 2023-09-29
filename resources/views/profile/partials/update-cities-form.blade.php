<section>
    <div class="container">
        <div class="container-fluid">
            <div class="column">
                <header>
                    <h4 class="font-medium text-gray-900">
                        Выберите города
                    </h4>
                </header>

                <form method="post" action="{{ route('profile.city.update') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('patch')

                    <div>
                        <x-input-label for="cities" value="Выберите из списка город (или несколько)" />

                        <div class="form-group">
                            <select name="cities[]" class="form-control" multiple="multiple" id="cities">
                                @foreach($cities as $key => $city)
                                    @if (!auth()->user()->cities)
                                        <option value="{{ $key }}">{{ $city }}</option>
                                    @else
                                        <option value="{{ $key }}"
                                            @if (in_array($key, json_decode(auth()->user()->cities, true)) ) selected @endif
                                        >{{ $city }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button class="btn btn-md btn-primary" type="submit">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
