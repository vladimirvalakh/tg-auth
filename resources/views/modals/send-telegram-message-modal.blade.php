<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="send-telegram-message-modal" tabindex="-1" role="dialog" aria-labelledby="send-telegram-message-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <form method="post" action="{{ route('telegram.send', ) }}">
                @csrf
                @method('post')

                <input type="hidden" name="user_id" id="user_id" value="">

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Отправить сообщение в Telegram</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <div class="form-group">
                        <x-input-label for="message" value="Текст сообщения" />
                        <x-text-input id="message" name="message" type="text" size="50" class="form-control mt-1 block w-full" />
                    </div>

                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </div>
            </form>
        </div>
    </div>
</div>
