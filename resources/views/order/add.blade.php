<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form method="post" action="{{ route('order.store') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('post')
                    <div class="max-w-xl">
                        <div class="form-group row">
                            <label for="city_id" class="col-sm-4 col-form-label">Город</label>
                            <div class="col-sm-8">
                                <select name="city_id" class="form-control" id="city_id">
                                    @foreach($cities as $key => $city)
                                        <option value="{{ $key }}">
                                            {{ $city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="tow_id" class="col-sm-4 col-form-label">Вид работ</label>
                            <div class="col-sm-8">
                                <select name="tow_id" class="form-control" id="tow_id">
                                    @foreach($tows as $key => $tow)
                                        <option value="{{ $key }}">
                                            {{ $tow }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="phone" class="col-sm-4 col-form-label">Номер телефона заявки</label>
                            <div class="col-sm-8">
                                <input required type="tel" placeholder="Введите номер телефона в формате +7 9×× ××× ××××" pattern="+7 9[0-9]{2} [0-9]{3} [0-9]{4}"  class="form-control input-phone-field" id="phone" name="phone">
                            </div>
                        </div>

{{--                        <div class="form-group row">--}}
{{--                            <label for="viber" class="col-sm-4 col-form-label">Whatsapp / Viber</label>--}}
{{--                            <div class="col-sm-8">--}}
{{--                                <input type="tel" placeholder="Введите номер телефона в формате +7 9×× ××× ××××" pattern="+7 9[0-9]{2} [0-9]{3} [0-9]{4}" class="form-control" id="viber" name="viber">--}}
{{--                            </div>--}}
{{--                        </div>--}}


{{--                        <div class="form-group row">--}}
{{--                            <label for="emails" class="col-sm-4 col-form-label">Email</label>--}}
{{--                            <div class="col-sm-8">--}}
{{--                                <input type="email" class="form-control" id="emails" name="emails">--}}
{{--                            </div>--}}
{{--                        </div>--}}

                        <div class="form-group row">
                            <label for="info" class="col-sm-4 col-form-label">Дополнительная информация</label>
                            <div class="col-sm-8">
                                <textarea class="form-control" id="info" name="info"></textarea>
                            </div>
                        </div>

                    </div>

                    <hr />

                    <button class="btn btn-md btn-success" type="submit">Сохранить</button>

                    @if (session('status') === 'order-added')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-gray-600 float-right text-success"
                        >Сохранено</p>
                    @endif

                    <a class="btn btn-primary pull-right" href="{{url()->previous()}}"> Вернуться к списку заказов </a>

                </form>
                <hr />
            </div>

        </div>
    </div>
</x-app-layout>
