<?php
use App\Models\Role;
?>

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if (auth()->user()->role && auth()->user()->role->slug === Role::OWNER_SLUG)
                    <div class="container-fluid mt-3 mb-3">
                        <div class="col-12 col-xl-4 pull-right">
                            <a type="button" class="btn btn-primary" href="{{route('site.add')}}">Добавить сайт</a>
                        </div>
                    </div>

                @endif

                @if(request()->route()->named('categories'))
                    @if (auth()->user()->role && auth()->user()->role->slug === Role::ADMINISTRATOR_SLUG)
                        <div class="container-fluid mt-3 mb-3">
                            <div class="col-12 col-xl-4 pull-right">
                                <a type="button" class="btn btn-primary" href="{{route('category.add')}}">Добавить категорию</a>
                            </div>
                        </div>
                    @endif
                @endif

                <div class="p-6 text-gray-900 dashboard grid-dashboard">
                    {!! grid_view($gridData) !!}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    .dashboard table {
        overflow: scroll;
    }
    .info-html {
        width: 160px!important;
    }
</style>


@include('modals.rent-site-modal')
@include('modals.send-telegram-message-modal')
@include('modals.site-get-30-days-orders-modal')
@include('modals.delete-order-modal')
@include('modals.let-me-know-modal')
@include('modals.decline-order-modal')

<script>
    let date_options = {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        timezone: 'UTC'
    };

    let countRecords = $('.grid-dashboard').find('table').find('tr').length - 3;

    if (countRecords < 1) {
        $('.grid-dashboard').html('<div class="col-lg-8 col-md-10 col-sm-12 col-xs-12 offset-lg-2 offset-md-1 float-md-center"><div class="jumbotron text-center">Нет доступных для аренды сайтов</div></div>');
    }

    $('.rent-site-modal-button').click(function(event){
        event.preventDefault();
        let url = $(this).parent().attr('href');
        let xhr = new XMLHttpRequest();
        xhr.open("GET", url);
        xhr.send();
        xhr.onload = () => {
            const data = JSON.parse(xhr.responseText);
            $("#rent-site-modal").find('#site_id').val(data.site.id);
            $("#rent-site-modal").find('#administrator-phone-number').html(data.administrator_data.phone);
            $("#rent-site-modal").find('#administrator-full_name').html(data.administrator_data.full_name);
            $("#rent-site-modal").find('#administrator-bank_cards_text').html(data.bank_cards_text);
            $("#rent-site-modal").find('#city_id').val(data.location.id);
            $("#rent-site-modal").find('#rent_id').val(data.rent.id);
            $("#rent-site-modal").find('.site_name').html("<a href='http://"+ data.site.url +"' target='_blank'>" + data.site.url + "</a>");
            $("#rent-site-modal").find('.city_name').text(data.location.city);
            $("#rent-site-modal").find('.rental_price').text(data.location.rental_price_per_month);
            $("#rent-site-modal").find('.period_date').text(data.period_date);
            $("#rent-site-modal").modal('show');
        };
    });

    $('.decline-order-button').click(function(event){
        event.preventDefault();
        let url = $(this).parent().attr('href');
        $("#decline-order-modal").find('#decline-modal-form-url').attr("action", url);
        $("#decline-order-modal").modal('show');
    });

    $('.send-telegram-message-modal-button').click(function(event){
        event.preventDefault();
        let url = $(this).parent().attr('href');
        let xhr = new XMLHttpRequest();
        xhr.open("GET", url);
        xhr.send();
        xhr.onload = () => {
            const data = JSON.parse(xhr.responseText);
            $("#send-telegram-message-modal").find('#user_id').val(data.user.id);
            $("#send-telegram-message-modal").modal('show');
        };
    });


    $('.rent_p30').parent('td').css({
        'width':'190px',
    });
    $('.rent_p30').append('  <button type="button" class="btn btn-sm btn-outline-danger show-p30 float-right mr-2">Посмотреть заявки</button>');

    $('.last_10_orders').append('  <button type="button" class="btn btn-block btn-sm btn-outline-danger show-last-10-orders">Показать</button>');


    $('.show-p30').click(function(event){
        event.preventDefault();
        let site_id = $(this).parent('.rent_p30').data('site-id');

        let url = '/site/'+site_id+'/get_30days_orders';
        let xhr = new XMLHttpRequest();
        xhr.open("GET", url);
        xhr.send();
        xhr.onload = () => {
            const data = JSON.parse(xhr.responseText);
            $("#site-get-30-days-orders-modal-content").find('.modal-content').html(data);
            $("#site-get-30-days-orders-modal-content").modal('show');
        };
    });

    $('.show-last-10-orders').click(function(event){
        event.preventDefault();
        let site_id = $(this).parent('.last_10_orders').data('site-id');

        let url = '/site/'+site_id+'/show-last-10-orders';
        let xhr = new XMLHttpRequest();
        xhr.open("GET", url);
        xhr.send();
        xhr.onload = () => {
            const data = JSON.parse(xhr.responseText);
            $("#site-get-30-days-orders-modal-content").find('.modal-content').html(data);
            $("#site-get-30-days-orders-modal-content").modal('show');
        };
    });

    $('.delete-order').click(function(event){
        event.preventDefault();
        let url = $(this).parent().attr('href');
        $("#delete-order-modal").find('.delete-form').attr('action', url);
        $("#delete-order-modal").modal('show');
    });

    $('.let-me-know').click(function(event){
        event.preventDefault();
        $("#let-me-know-modal").modal('show');
    });

    $(".input-phone-field").on("input", function(e) {
        console.log('ada');
        let cursorPos = e.target.selectionStart
        let formatInput = autoFormatPhoneNumber(e.target)
        e.target.value = String(formatInput)
        let isBackspace = (e?.data==null) ? true: false
        let nextCusPos = nextDigit(formatInput, cursorPos, isBackspace)

        this.setSelectionRange(nextCusPos+1, nextCusPos+1);
    });

    function autoFormatPhoneNumber(ref) {
        try {
            let phoneNumberString = ref.value
            let cleaned = ("" + phoneNumberString).replace(/\D/g, "");
            let match = cleaned.match(/^(\d{0,3})?(\d{0,3})?(\d{0,4})?/);
            return [
                match[1],
                match[2] ? " " : "",
                match[2],
               match[3] ? " " : "",
                match[3]].join("")

        } catch(err) {
            return "";
        }
    }

    function nextDigit(input, cursorpos, isBackspace) {
        if (isBackspace){
            for (let i = cursorpos-1; i > 0; i--) {
                if(/\d/.test(input[i])){
                    return i
                }
            }
        } else {
            for (let i = cursorpos-1; i < input.length; i++) {
                if(/\d/.test(input[i])){
                    return i
                }
            }
        }

        return cursorpos
    }

</script>
