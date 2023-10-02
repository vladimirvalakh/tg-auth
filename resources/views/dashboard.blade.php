<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
    })
</script>
