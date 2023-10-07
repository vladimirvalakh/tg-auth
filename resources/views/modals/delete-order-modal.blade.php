<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="delete-order-modal" tabindex="-1" role="dialog" aria-labelledby="delete-order-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <form method="get" action="" class="delete-form">
                @csrf
                @method('get')
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Внимание!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Если вы отменяете аренду, то уплаченная сумма не возвращается.</p>
                </div>
                <div class="modal-footer text-center">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Нет</button>
                    <button type="submit" class="btn btn-success">Да</button>
                </div>
            </form>
        </div>
    </div>
</div>
