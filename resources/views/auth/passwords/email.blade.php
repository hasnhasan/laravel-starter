@extends('backend.layouts.auth')

@section('content')
    <div class="container d-flex justify-content-center ht-100p">
        <div class="mx-wd-300 wd-sm-450 ht-100p d-flex flex-column align-items-center justify-content-center mg-b-40">
            <div class="wd-80p wd-sm-300 mg-b-15"><img src="{{asset('assets/img/img18.png')}}" class="img-fluid"
                                                       alt="{{__('Şifrenizi sıfırlayın')}}"></div>
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <h4 class="tx-20 tx-sm-24">{{__('Şifrenizi sıfırlayın')}}</h4>
            <p class="tx-color-03 mg-b-30 tx-center">{{__('Kullanıcı adınızı veya e-posta adresinizi girin, şifrenizi sıfırlamanız için size bir link gönderelim.')}}</p>
            <form method="POST" action="{{ route('password.email') }}"
                  class="wd-100p d-flex flex-column flex-sm-row">
                @csrf
                <input type="email" name="email"
                       class="form-control wd-sm-250 flex-fill @error('email') is-invalid @enderror"
                       value="{{old('email')}}" placeholder="{{__('E-posta adresinizi giriniz')}}" required
                       autocomplete="email" autofocus>

                <button class="btn btn-brand-02 mg-sm-l-10 mg-t-10 mg-sm-t-0"
                        type="submit">{{__('Şifreyi yenile')}}</button>
            </form>
            @error('email')
            <p class="invalid-feedback d-block" role="alert">
                <strong>{{ $message }}</strong>
            </p>
            @enderror

        </div>
    </div><!-- container -->
@endsection
