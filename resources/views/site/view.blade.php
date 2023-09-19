<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    ID: <b>{{$site->id}}</b><br />
                    Категория: <b>{{$site->category->name}}</b><br />
                    URL: <b><a href="{{$site->url}}" target="_blank">{{$site->url}}</a></b><br />
                    Город: <b>{{$site->location->city }}</b><br />
                    Город локатив: <b>{{$site->location->locative}}</b><br />
                    Адрес: <b>{{$site->address}}</b><br />
                    Телефон: <b>{{$site->phone1}}</b><br />
                    Email: <b>{{$site->email}}</b><br />
                    Email 2: <b>{{$site->email2}}</b><br />
                    Koeff: <b>{{$site->koeff}}</b><br />
                    mail domain: <b>{{$site->mail_domain}}</b><br />
                    Yandex Metrica ID: <b>{{$site->YmetricaId}}</b><br />
                    VENYOOId: <b>{{$site->VENYOOId}}</b><br />
                    tgchatid: <b>{{$site->tgchatid}}</b><br />
                    GMiframe1: <b>{{$site->GMiframe1}}</b><br />
                    GMiframe2: <b>{{$site->GMiframe2}}</b><br />
                    areas: <b>{{$site->areas}}</b><br />
                    crm: <b>{{$site->crm}}</b><br />
                    crm_pass: <b>{{$site->crm_pass}}</b><br />
                    crm_u: <b>{{$site->crm_u}}</b><br />
                    prf: <b>{{$site->prf}}</b><br />
                </div>

                <hr />
                <a class="btn btn-primary text-center" href="{{url()->previous()}}"> Вернуться к списку сайтов </a>
            </div>

        </div>
    </div>
</x-app-layout>
