<!-- Modal -->
<div class="modal fade bd-example-modal-lg" id="decline-order-modal" tabindex="-1" role="dialog" aria-labelledby="decline-order-modal-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-lg">
            <form method="post" action="" id="decline-modal-form-url">
                @csrf
                @method('post')

                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Укажите причину отклонения</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <select name="decline-reason" class="form-control" required>
                        <option value="Оплата не поступила">Оплата не поступила</option>
                        <option value="Сайт уже арендован">Сайт уже арендован</option>
                        <option value="Указаны неправильные контактные данные">Указаны неправильные контактные данные</option>
                    </select>
                </div>
                <div class="modal-footer text-center">
                    <button type="submit" class="btn btn-primary">Отклонить</button>
                </div>
            </form>
        </div>
    </div>
</div>
