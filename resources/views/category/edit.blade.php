<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form method="post" action="{{ route('category.update', $category->id ) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('put')
                    <div class="max-w-xl">

                        <div class="form-group row">
                            <label for="url" class="col-sm-2 col-form-label">URL</label>
                            <div class="col-sm-10">
                                <input type="text" name="url" class="form-control" id="url" value="{{ old('url', $category->url) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="city_id" class="col-sm-2 col-form-label">Город</label>
                            <div class="col-sm-10">
                                <select name="city_id" class="form-control" id="city_id">
                                    <option>Выберите из списка</option>
                                    @foreach($cities as $key => $city)
                                        <option value="{{ $key }}"
                                            @if ($key == old('city_id', $category->city_id))
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
                                <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $category->address) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone1" class="col-sm-2 col-form-label">Телефон</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="phone1" name="phone1" value="{{ old('phone1', $category->phone1) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone2" class="col-sm-2 col-form-label">Телефон 2</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="phone2" name="phone2"  value="{{ old('phone2', $category->phone2) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="email" name="email" value="{{ old('email', $category->email) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email2" class="col-sm-2 col-form-label">Email 2</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="email2" name="email2" value="{{ old('email2', $category->email2) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mail_domain" class="col-sm-2 col-form-label">Mail Domain</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="mail_domain" name="mail_domain"  value="{{ old('mail_domain', $category->mail_domain) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="YmetricaId" class="col-sm-2 col-form-label">Yandex Metrica ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="YmetricaId" name="YmetricaId"  value="{{ old('YmetricaId', $category->YmetricaId) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="VENYOOId" class="col-sm-2 col-form-label">VENYOOId</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="VENYOOId" name="VENYOOId"  value="{{ old('VENYOOId', $category->VENYOOId) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="GMiframe1" class="col-sm-2 col-form-label">GMiframe1</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="GMiframe1" name="GMiframe1" value="{{ old('GMiframe1', $category->GMiframe1) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="GMiframe2" class="col-sm-2 col-form-label">GMiframe2</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="GMiframe2" name="GMiframe2"  value="{{ old('GMiframe2', $category->GMiframe2) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="crm" class="col-sm-2 col-form-label">crm</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="crm" name="crm" value="{{ old('crm', $category->crm) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="crm_pass" class="col-sm-2 col-form-label">crm_pass</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="crm_pass" name="crm_pass"  value="{{ old('crm_pass', $category->crm_pass) }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="crm_u" class="col-sm-2 col-form-label">crm_u</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="crm_u" name="crm_u"  value="{{ old('crm_u', $category->crm_u) }}">
                            </div>
                        </div>
                    </div>

                    <hr />

                    <button class="btn btn-md btn-success" type="submit">Сохранить</button>

                    @if (session('status') === 'category-updated')
                        <p
                            x-data="{ show: true }"
                            x-show="show"
                            x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-gray-600 float-right text-success"
                        >Сохранено</p>
                    @endif

                    <a class="btn btn-primary pull-right" href="{{url()->previous()}}"> Вернуться к списку категорий </a>

                </form>
                <hr />
            </div>

        </div>
    </div>
</x-app-layout>
