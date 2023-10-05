<?php
use App\Models\City;

$cities = City::citiesList();
$regions = City::regionsList();
?>

<section>
    <div class="container-fluid mb-4">
        <div class="column">
            <form method="post" action="{{ route('profile.city.update') }}" id="select-cities-form">
                @csrf
                @method('patch')
                <div class="row justify-content-md-center">
                    <div class="col col-md-5">
                        <label for="select-region-top-form" class="form-label align-text-middle">Выберите регион</label>
                        <select style="height:1.8em;" name="regions[]" class="form-control flex-fill form-control mb-2 mr-sm-2" id="select-region-top-form">
                            @foreach($regions as $key => $region)
                                @if (!auth()->user()->cities)
                                    <option value="{{ $key }}">{{ $region }}</option>
                                @else
                                    <option value="{{ $key }}"
                                            @if (in_array($key, json_decode(auth()->user()->cities, true)) ) selected @endif
                                    >{{ $region }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col col-md-6">
                        <label for="select-cities-top-form" class="form-label align-text-middle">Выберите из списка город (или несколько) </label>
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

<script>
    $(function(){
        $('select option').filter(function(){
            return ($(this).val().trim()=="" && $(this).text().trim()=="");
        }).remove();
    });

    $('#select-region-top-form').on('select2:select', function () {
        let region = $(this).val();

        $('#select-cities-top-form').children().remove().end();

        let xhr = new XMLHttpRequest();
        xhr.open("GET", '/region/'+ region +'/cities');
        xhr.send();
        xhr.onload = () => {
            const data = JSON.parse(xhr.responseText);

            let cities = ''
            $.each(data, function(i, item) {
                cities += '<option value="'+i+'">'+item+'</option>';
            });
            $('#select-cities-top-form').html(cities);
        };
    });
</script>
