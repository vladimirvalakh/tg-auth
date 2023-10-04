<?php
use App\Models\City;

$cities = City::citiesList();
?>

<section>
    <div class="container-fluid">
        <div class="column">
            <form method="post" action="{{ route('profile.city.update') }}" id="select-cities-form">
                @csrf
                @method('patch')
                <div class="row justify-content-md-center">
                    <div class="col col-md-3 align-middle">
                        <label for="select-cities-top-form" class="form-label align-text-middle">Выберите из списка город (или несколько) </label>
                    </div>
                    <div class="col col-md-8">
                        <select name="cities[]" class="form-control flex-fill form-control mb-2 mr-sm-2" multiple="multiple" id="select-cities-top-form">
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
                    <div class="col col-md-1 pull-right">
                        <button type="submit" class="btn btn-primary mb-2 submit-button float-right">Сохранить</button>
                    </div>
                </div>
            </form>



        </div>
    </div>
</section>
