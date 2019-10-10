@extends('backend::layouts.auth')

@section('content')
    <div class="container">
        <div class="media align-items-stretch justify-content-center ht-100p pos-relative">
            <div class="media-body align-items-center d-none d-lg-flex">
                <div class="mx-wd-600">
                    <img src="{{asset('assets/img/img15.png')}}" class="img-fluid" alt="{{__('Oturum aç')}}">
                </div>
            </div><!-- media-body -->
            <div class="sign-wrapper mg-lg-l-50 mg-xl-l-60">
                <div class="wd-100p">
                    <h3 class="tx-color-01 mg-b-5">{{__('Oturum aç')}}</h3>
                    <p class="tx-color-03 tx-16 mg-b-40">{{__('Tekrar hoşgeldiniz! Devam etmek için giriş yapın.')}}</p>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        {!!Form::text('email', __('E-posta Adresi'))->type('email')->placeholder(__('isminiz@ornek.com'))->required()!!}


                        <div class="form-group">
                            <div class="d-flex justify-content-between mg-b-5">
                                <label class="mg-b-0-f">{{__('Şifreniz')}}</label>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}"
                                       class="tx-13">{{__('Şifremi Unuttum?')}}</a>
                                @endif
                            </div>
                            <input id="password" type="password" placeholder="{{__('Şifrenizi girin')}}"
                                   class="form-control @error('password') is-invalid @enderror" name="password" required
                                   autocomplete="current-password">
                        </div>
                        {!!Form::checkbox('remember', __('Beni Hatırla'))->checked()!!}
                        <button class="btn btn-brand-02 btn-block mt-2" type="submit">{{__('Oturum Aç')}}</button>
                    </form>
                    {{--<div class="divider-text">or</div>
                    <button class="btn btn-outline-facebook btn-block">Sign In With Facebook</button>
                    <button class="btn btn-outline-twitter btn-block">Sign In With Twitter</button>--}}
                    @if (Route::has('register'))
                        <div class="tx-13 mg-t-20 tx-center">{{__('Hesabınız yok mu?')}} <a
                                href="{{route('register')}}">{{__('Hesap oluştur')}}</a></div>
                    @endif
                </div>
            </div><!-- sign-wrapper -->
        </div><!-- media -->
    </div><!-- container -->
@endsection
