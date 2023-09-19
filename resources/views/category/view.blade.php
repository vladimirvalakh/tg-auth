<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    ID: <b>{{$category->id}}</b><br />
                    URL: <b><a href="{{$category->url}}" target="_blank">{{$category->url}}</a></b><br />
                    Город: <b>{{$category->location->city }}</b><br />
                    Город локатив: <b>{{$category->location->locative}}</b><br />
                    Адрес: <b>{{$category->address}}</b><br />
                    Телефон: <b>{{$category->phone1}}</b><br />
                    Email: <b>{{$category->email}}</b><br />
                    Email 2: <b>{{$category->email2}}</b><br />
                    Koeff: <b>{{$category->koeff}}</b><br />
                    mail domain: <b>{{$category->mail_domain}}</b><br />
                    Yandex Metrica ID: <b>{{$category->YmetricaId}}</b><br />
                    VENYOOId: <b>{{$category->VENYOOId}}</b><br />
                    tgchatid: <b>{{$category->tgchatid}}</b><br />
                    GMiframe1: <b>{{$category->GMiframe1}}</b><br />
                    GMiframe2: <b>{{$category->GMiframe2}}</b><br />
                    areas: <b>{{$category->areas}}</b><br />
                    crm: <b>{{$category->crm}}</b><br />
                    crm_pass: <b>{{$category->crm_pass}}</b><br />
                    crm_u: <b>{{$category->crm_u}}</b><br />
                </div>

                <hr />
                <a class="btn btn-primary text-center" href="{{url()->previous()}}"> Вернуться к списку категорий </a>
            </div>

        </div>
    </div>
</x-app-layout>
