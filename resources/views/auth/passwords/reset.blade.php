@extends('backend::layouts.auth')

@section('content')
    <div class="container">
        <div class="media align-items-stretch justify-content-center ht-100p pos-relative">
            <div class="media-body align-items-center d-none d-lg-flex">
                <div class="mx-wd-600">
                    <img src="{{asset('assets/img/img18.png')}}" class="img-fluid" alt="{{__('Şifrenizi sıfırlayın')}}">
                </div>
            </div><!-- media-body -->
            <div class="sign-wrapper mg-lg-l-50 mg-xl-l-60">
                <div class="wd-100p">
                    <h3 class="tx-color-01 mg-b-5">{{__('Şifrenizi sıfırlayın')}}</h3>
                    <p class="tx-color-03 tx-16 mg-b-40">{{__('Şifrenizi sıfırlamanıza son bir adım kaldı.')}}</p>
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        @error('token')
                        <p class="invalid-feedback d-block" role="alert">
                            <strong>{{ $message }}</strong>
                        </p>
                        @enderror

                        {!!Form::text('email', __('E-posta Adresi'))->type('email')->placeholder(__('isminiz@ornek.com'))->required()!!}

                        {!!Form::text('password', __('Şifre'))->type('password')->autocomplete('new-password')->placeholder(__('Şifrenizi giriniz'))->required()!!}
                        {!!Form::text('password_confirmation', __('Şifre Onayı'))->type('password')->autocomplete('new-password')->placeholder(__('Tekrar Şifrenizi giriniz'))->required()!!}

                        <button class="btn btn-brand-02 btn-block mt-2" type="submit">{{__('Şifreyi yenile')}}</button>
                    </form>
                    {{--<div class="divider-text">or</div>
                    <button class="btn btn-outline-facebook btn-block">Sign In With Facebook</button>
                    <button class="btn btn-outline-twitter btn-block">Sign In With Twitter</button>--}}
                </div>
            </div><!-- sign-wrapper -->
        </div><!-- media -->
    </div><!-- container -->
@endsection
