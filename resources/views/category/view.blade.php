<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    ID: <b>{{$category->id}}</b><br />
                    Название категории: <b><a href="{{$category->name}}" target="_blank">{{$category->name}}</a></b><br />

                    @if($category->parent)
                        Родительская категория: <b><a href="{{route('category.view', $category->parent->id)}}" target="_blank">{{$category->parent->name}}</a></b><br />
                    @endif
                </div>
                <hr />
                <a class="btn btn-primary text-center" href="{{url()->previous()}}"> Вернуться к списку категорий </a>
            </div>

        </div>
    </div>
</x-app-layout>
