<x-app-layout>
    <div class="py-12">
        <section>
            <header>
                <h2 class="text-lg font-medium text-gray-900 text-center mb-4">
                    Напишите нам
                </h2>
            </header>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white">
                <div class="container">
                    <div class="row">
                        <div class="col-8">
                            <form method="POST" action="{{route('support.send')}}">
                                @csrf
                                <div class="form-row">
                                    <div class="col">
                                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                            <input required name="name" type="text" class="form-control" id="name" aria-describedby="name" placeholder="Ваше имя">
                                            <span class="text-danger">{{ $errors->first('name') }}</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                            <input required name="phone" type="phone" class="form-control" id="phone"
                                                   placeholder="Номер телефона">
                                            <span class="text-danger">{{ $errors->first('phone') }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <input required name="subject" type="text" class="form-control" id="subject"
                                           placeholder="Тема вопроса?">
                                    <span class="text-danger">{{ $errors->first('subject') }}</span>
                                </div>

                                <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                                    <textarea required name="message" class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="Ваше сообщение"></textarea>
                                    <span class="text-danger">{{ $errors->first('message') }}</span>
                                </div>
                                <button type="submit" class="btn btn-primary float-right">Отправить</button>
                            </form>
                        </div>
                        <div class="col-4">
                            <div><b>Контакты:</b></div>
                            <div><i class="far fa-envelope"></i> <a href="mailto:support@lead-mart.ru">support@lead-mart.ru</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </section>
    </div>
</x-app-layout>
