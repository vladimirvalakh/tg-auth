<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <form method="post" action="{{ route('category.store') }}" class="mt-6 space-y-6">
                    @csrf
                    @method('post')
                    <div class="max-w-xl">
                        <div class="form-group row">
                            <label for="url" class="col-sm-2 col-form-label">Название категории</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" id="name">
                            </div>
                        </div>


                        <div class="form-group row">
                            <label for="select-parent" class="col-sm-2 col-form-label">Родительская категория</label>
                            <div class="col-sm-10">
                                <select name="parent_id" class="form-control flex-fill form-control mb-2 mr-sm-2" id="select-parent">
                                    <option value="0">Без категории</option>
                                    @foreach($categories as $key => $parent)
                                        <option value="{{ $key }}">{{ $parent }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    <hr />

                    <button class="btn btn-md btn-success" type="submit">Добавить</button>

                    <a class="btn btn-primary pull-right" href="{{url()->previous()}}"> Вернуться к списку категорий </a>
                </div>
                </form>
                <hr />
            </div>

        </div>
    </div>
</x-app-layout>
