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

                <div class="p-6 text-gray-900 dashboard">
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
</style>


@include('modals.rent-site-modal')
@include('modals.site-get-30-days-orders-modal')

<script>
    let date_options = {
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        timezone: 'UTC'
    };

    $('.rent-site-modal-button').click(function(event){
        event.preventDefault();
        let url = $(this).parent().attr('href');
        let xhr = new XMLHttpRequest();
        xhr.open("GET", url);
        xhr.send();
        xhr.onload = () => {
            const data = JSON.parse(xhr.responseText);
            $("#rent-site-modal").find('#site_id').val(data.site.id);
            $("#rent-site-modal").find('#city_id').val(data.location.id);
            $("#rent-site-modal").find('#rent_id').val(data.rent.id);
            $("#rent-site-modal").find('.site_name').html("<a href='http://"+ data.site.url +"' target='_blank'>" + data.site.url + "</a>");
            $("#rent-site-modal").find('.city_name').text(data.location.city);
            $("#rent-site-modal").find('.rental_price').text(data.location.rental_price_per_month);
            $("#rent-site-modal").find('.period_date').text(data.period_date);
            $("#rent-site-modal").modal('show');
        };
    });


    $('.rent_p30').parent('td').css({
        'width':'190px',
    });
    $('.rent_p30').append('  <button type="button" class="btn btn-sm btn-outline-danger show-p30">Посмотреть заявки</button>');


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

</script>
