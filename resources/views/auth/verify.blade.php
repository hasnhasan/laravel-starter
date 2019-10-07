@extends('backend.layouts.app')

@section('content')
    <div class="container ht-100p">
        <div class="ht-100p d-flex flex-column align-items-center justify-content-center">
            <div class="wd-150 wd-sm-250 mg-b-30">
                <img src="{{asset('assets/img/img17.png')}}" class="img-fluid"
                     alt="{{__('Eposta adresinizi doğrulayın')}}">
            </div>
            <h4 class="tx-20 tx-sm-24">{{__('Eposta adresinizi doğrulayın')}}</h4>
            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('E-posta adresinize yeni bir doğrulama bağlantısı gönderildi.') }}
                </div>
            @endif
            <p class="tx-color-03 mg-b-40">{{__('Devam etmeden önce lütfen bir doğrulama bağlantısı için e-postanızı kontrol edin.')}}</p>
            <p class="tx-color-03 mg-b-40">{{__('E-postayı almadıysanız')}},</p>
            <div class="tx-13 tx-lg-14 mg-b-40">
                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit"
                            class="btn btn-brand-02 d-inline-flex align-items-center">{{ __('tekrar e-posta gönder') }}</button>
                    .
                </form>
            </div>
        </div>
    </div><!-- container -->
@endsection
