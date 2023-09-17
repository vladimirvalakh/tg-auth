<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form method="post" action="{{ route('site.update', $site->id ) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('put')
                    <div class="max-w-xl">

                        <div class="form-group row">
                            <label for="cat_id" class="col-sm-2 col-form-label">Категория</label>
                            <div class="col-sm-10">
                                <select name="cat_id" class="form-control" id="cat_id">
                                    <option>Выберите из списка</option>
                                    @foreach($categories as $key => $name)
                                        <option value="{{ $key }}"
                                                @if ($key == old('cat_id', $site->cat_id))
                                                selected="selected"
                                            @endif
                                        >
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="url" class="col-sm-2 col-form-label">URL</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="url" placeholder="URL" value="{{ old('url', $site->url) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="city_id" class="col-sm-2 col-form-label">Город</label>
                            <div class="col-sm-10">
                                <select name="city_id" class="form-control" id="city_id">
                                    <option>Выберите из списка</option>
                                    @foreach($cities as $key => $city)
                                        <option value="{{ $key }}"
                                            @if ($key == old('city_id', $site->city_id))
                                                selected="selected"
                                            @endif
                                        >
                                            {{ $city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address" class="col-sm-2 col-form-label">Адрес</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="address" placeholder="Адрес" value="{{ old('address', $site->address) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone1" class="col-sm-2 col-form-label">Телефон</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="phone1" placeholder="Телефон" value="{{ old('phone1', $site->phone1) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone2" class="col-sm-2 col-form-label">Телефон 2</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="phone2" placeholder="Телефон 2" value="{{ old('phone2', $site->phone2) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="email" placeholder="Email" value="{{ old('email', $site->email) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email2" class="col-sm-2 col-form-label">Email 2</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="email2" placeholder="Email 2" value="{{ old('email2', $site->email2) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="koeff" class="col-sm-2 col-form-label">Koeff</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="koeff" placeholder="Koeff" value="{{ old('koeff', $site->koeff) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mail_domain" class="col-sm-2 col-form-label">Mail Domain</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="mail_domain" placeholder="Mail Domain" value="{{ old('mail_domain', $site->mail_domain) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="YmetricaId" class="col-sm-2 col-form-label">Yandex Metrica ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="YmetricaId" placeholder="Yandex Metrica ID" value="{{ old('YmetricaId', $site->YmetricaId) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="VENYOOId" class="col-sm-2 col-form-label">VENYOOId</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="VENYOOId" placeholder="VENYOOId" value="{{ old('VENYOOId', $site->VENYOOId) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="tgchatid" class="col-sm-2 col-form-label">tgchatid</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="tgchatid" placeholder="tgchatid" value="{{ old('tgchatid', $site->tgchatid) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="GMiframe1" class="col-sm-2 col-form-label">GMiframe1</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="GMiframe1" placeholder="GMiframe1" value="{{ old('GMiframe1', $site->GMiframe1) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="GMiframe2" class="col-sm-2 col-form-label">GMiframe2</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="GMiframe2" placeholder="GMiframe2" value="{{ old('GMiframe2', $site->GMiframe2) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="areas" class="col-sm-2 col-form-label">areas</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="areas" placeholder="areas" value="{{ old('areas', $site->areas) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="crm" class="col-sm-2 col-form-label">crm</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="crm" placeholder="crm" value="{{ old('crm', $site->crm) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="crm_pass" class="col-sm-2 col-form-label">crm_pass</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="crm_pass" placeholder="crm_pass" value="{{ old('crm_pass', $site->crm_pass) }}">
                            </div>
                        </div>

                        crm_u: <b>{{$site->crm_u}}</b><br />
                        prf: <b>{{$site->prf}}</b><br />
                    </div>

                    <hr />

                    <button class="btn btn-md btn-success" type="submit">Сохранить</button>

                    @if (session('status') === 'profile-updated')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-gray-600 float-right text-success"
                        >Сохранено</p>
                    @endif

                    <a class="btn btn-primary pull-right" href="{{url()->previous()}}"> Вернуться к списку сайтов </a>

                </form>
                <hr />
            </div>

        </div>
    </div>
</x-app-layout>
