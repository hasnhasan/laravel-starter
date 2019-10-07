@extends('backend.layouts.auth')

@section('content')
    <div class="container">
        <div class="media align-items-stretch justify-content-center ht-100p">
            <div class="sign-wrapper mg-lg-r-50 mg-xl-r-60">
                <div class="pd-t-20 wd-100p">
                    <h4 class="tx-color-01 mg-b-5">{{__('Yeni hesap oluştur')}}</h4>
                    <p class="tx-color-03 tx-16 mg-b-40">{{__('Kayıt olmak ücretsizdir ve sadece bir dakika sürer.')}}</p>
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        {!!Form::text('name', __('Adınız'))->value(old('name'))->placeholder(__('Adınızı Giriniz'))->required()!!}

                        {!!Form::text('email', __('E-posta Adresi'))->type('email')->value(old('email'))->autocomplete('email')->placeholder(__('E-posta adresinizi giriniz'))->required()!!}
                        {!!Form::text('password', __('Şifre'))->type('password')->autocomplete('new-password')->placeholder(__('Şifrenizi giriniz'))->required()!!}
                        {!!Form::text('password_confirmation', __('Şifre Onayı'))->type('password')->autocomplete('new-password')->placeholder(__('Tekrar Şifrenizi giriniz'))->required()!!}

                        <div class="form-group tx-12">
                            {!!__('Aşağıdaki <strong>Hesap Oluştur</strong>\'u tıklayarak, hizmet şartlarımızı ve gizlilik bildirimimizi kabul etmiş olursunuz.')!!}
                        </div><!-- form-group -->

                        <button class="btn btn-brand-02 btn-block" type="submit">{{__('Hesap Oluştur')}}</button>
                    </form>
                    {{--<div class="divider-text">or</div>
                    <button class="btn btn-outline-facebook btn-block">Sign Up With Facebook</button>
                    <button class="btn btn-outline-twitter btn-block">Sign Up With Twitter</button>--}}
                    <div class="tx-13 mg-t-20 tx-center">{{__('Zaten hesabınız var mı?')}} <a
                            href="{{route('login')}}">{{__('Oturum Aç')}}</a></div>
                </div>
            </div><!-- sign-wrapper -->
            <div class="media-body pd-y-30 pd-lg-x-50 pd-xl-x-60 align-items-center d-none d-lg-flex pos-relative">
                <div class="mx-lg-wd-500 mx-xl-wd-550">
                    <img src="{{asset('assets/img/img16.png')}}" class="img-fluid" alt="{{__('Yeni hesap oluştur')}}">
                </div>
            </div><!-- media-body -->
        </div><!-- media -->
    </div><!-- container -->
@endsection
