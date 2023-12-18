<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form method="post" action="{{ route('site.store') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('post')
                    <div class="max-w-xl">

                        <div class="form-group row">
                            <label for="cat_id" class="col-sm-2 col-form-label">Категория</label>
                            <div class="col-sm-10">
                                <select required name="cat_id" class="form-control" id="cat_id">
                                    @foreach($categories as $key => $name)
                                        <option value="{{ $key }}" >
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="url" class="col-sm-2 col-form-label">URL</label>
                            <div class="col-sm-10">
                                <input type="text" name="url" class="form-control" id="url" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="city_id" class="col-sm-2 col-form-label">Город</label>
                            <div class="col-sm-10">
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
                            <label for="address" class="col-sm-2 col-form-label">Адрес</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="address" name="address">
                            </div>
                        </div>



                        <div class="form-group row">
                            <label for="last_month_orders_count" class="col-sm-2 col-form-label">Количество заявок за последний месяц</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="last_month_orders_count" required name="last_month_orders_count" placeholder="">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone1" class="col-sm-2 col-form-label">Телефон</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="phone1" name="phone1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone2" class="col-sm-2 col-form-label">Телефон 2</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="phone2" name="phone2">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="email" name="email">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email2" class="col-sm-2 col-form-label">Email 2</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="email2" name="email2">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="comment" class="col-sm-2 col-form-label">Комментарий</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="comment" name="comment" placeholder="" >
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="koeff" class="col-sm-2 col-form-label">Koeff</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="koeff" name="koeff">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="mail_domain" class="col-sm-2 col-form-label">Mail Domain</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="mail_domain" name="mail_domain">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="YmetricaId" class="col-sm-2 col-form-label">Yandex Metrica ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="YmetricaId" name="YmetricaId">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="VENYOOId" class="col-sm-2 col-form-label">VENYOOId</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="VENYOOId" name="VENYOOId">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="tgchatid" class="col-sm-2 col-form-label">tgchatid</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="tgchatid" name="tgchatid">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="GMiframe1" class="col-sm-2 col-form-label">GMiframe1</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="GMiframe1" name="GMiframe1">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="GMiframe2" class="col-sm-2 col-form-label">GMiframe2</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="GMiframe2" name="GMiframe2">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="areas" class="col-sm-2 col-form-label">areas</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="areas" name="areas">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="crm" class="col-sm-2 col-form-label">crm</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="crm" name="crm">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="crm_pass" class="col-sm-2 col-form-label">crm_pass</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="crm_pass" name="crm_pass">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="crm_u" class="col-sm-2 col-form-label">crm_u</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="crm_u" name="crm_u">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="prf" class="col-sm-2 col-form-label">prf</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="prf" name="prf">
                            </div>
                        </div>
                    </div>

                    <hr />

                    <button class="btn btn-md btn-success" type="submit">Добавить</button>

                    <a class="btn btn-primary pull-right" href="{{url()->previous()}}"> Вернуться к списку сайтов </a>

                </form>
                <hr />
            </div>

        </div>
    </div>
</x-app-layout>
