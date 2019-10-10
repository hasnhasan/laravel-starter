@extends('backend::layouts.auth')
@section('title', __('Unauthorized'))
@section('content')
    <div class="container ht-100p tx-center">
        <div class="ht-100p d-flex flex-column align-items-center justify-content-center">
            <div class="wd-70p wd-sm-250 wd-lg-300 mg-b-15">
                <img src="{{asset('assets/img/img22.png')}}" class="img-fluid" alt="{{__('Unauthorized')}}">
            </div>
            <h1 class="tx-color-01 tx-24 tx-sm-32 tx-lg-36 mg-xl-b-5">401 {{__('Unauthorized')}}</h1>
            <h5 class="tx-16 tx-sm-18 tx-lg-20 tx-normal mg-b-20">Oopps. The page you were looking for doesn't
                exist.</h5>
            <p class="tx-color-03 mg-b-30">You may have mistyped the address or the page may have moved. Try searching
                below.</p>
        </div>
    </div><!-- container -->
@endsection

