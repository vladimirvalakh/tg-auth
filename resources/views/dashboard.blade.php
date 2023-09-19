<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {!! grid_view($gridData) !!}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    $('select').select2({
        "language": {
            "noResults": function(){
                return "Ничего не найдено";
            }
        },
        escapeMarkup: function (markup) {
            return markup;
        }
    });

    $('select').on('select2:select', function () {
        $('#grid_view_search_button').click();
    });
</script>
