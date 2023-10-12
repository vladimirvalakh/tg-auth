<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="first-screen-modal" tabindex="-1" role="dialog" aria-labelledby="first-screen-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <form method="post" action="{{ route('profile.first.screen.update') }}" class="mt-6 space-y-6">
                @csrf
                @method('patch')
                <input type="hidden" name="first_screen" value="0">
                <div class="modal-body">
                    <p>ЛидМаркет - сервис аренды сайтов по остеклению. Источник лидов: Лэндинги и многостраничные сайты. Источник траффика: СЕО и контекстная реклама...</p>
                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-success">Понятно</button>
                </div>
            </form>
        </div>
    </div>
</div>
