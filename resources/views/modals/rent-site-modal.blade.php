<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="rent-site-modal" tabindex="-1" role="dialog" aria-labelledby="rent-site-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <form method="post" action="{{ route('order.store') }}">
                @csrf
                @method('post')

                <input type="hidden" name="site_id" id="site_id" value="">
                <input type="hidden" name="city_id" id="city_id" value="">
                <input type="hidden" name="rent_id" id="rent_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Инструкция</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <p>1. Вы выбрали аренду сайта <b><span class="site_name"></span></b> в городе <b><span class="city_name"></span></b> на 1 месяц до <b><span class="period_date"></span></b>.</p>
                    <p>2. Переведите <b><span class="rental_price"></span> руб</b> на карту по номеру телефона <br /><span id="administrator-phone-number"></span><br />получатель <span id="administrator-full_name"></span>, <span id="administrator-bank_cards_text"></span></p>


                    <div class="form-group">
                        <x-input-label for="phone" value="3. Введите телефон на сайт в формате +7 9×× ××× ××××" />
                        <x-text-input id="phone" name="phone" type="tel" placeholder="Введите номер телефона в формате +7 9×× ××× ××××" pattern="+7 9[0-9]{2} [0-9]{3} [0-9]{4}"  required class="form-control mt-1 block w-full input-phone-field" />
                    </div>

                    <div class="form-group">
                        <x-input-label for="viber" value="Вацап/вайбер для получения заявок в формате +7 9×× ××× ××××" />
                        <x-text-input id="viber" name="viber" type="tel" placeholder="Введите номер телефона в формате +7 9×× ××× ××××" pattern="+7 9[0-9]{2} [0-9]{3} [0-9]{4}" class="form-control mt-1 block w-full input-phone-field" />
                    </div>

                    <div class="form-group">
                        <x-input-label for="emails" value="e-mail для получения заявок" />
                        <x-text-input id="emails" name="emails" type="email" class="form-control mt-1 block w-full" />
                    </div>

                    <p>4. Когда деньги переведены, то нажмите кнопку "Отправить на модерацию".</p>

                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-primary">Отправить на модерацию</button>
                </div>
            </form>
        </div>
    </div>
</div>
