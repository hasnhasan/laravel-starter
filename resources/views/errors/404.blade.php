@extends('backend::layouts.auth')
@section('title', __('Not Found'))
@section('content')
    <div class="container ht-100p tx-center">
        <div class="ht-100p d-flex flex-column align-items-center justify-content-center">
            <div class="wd-70p wd-sm-250 wd-lg-300 mg-b-15">
                <img src="{{asset('assets/img/img19.png')}}" class="img-fluid" alt="{{__('Not Found')}}">
            </div>
            <h1 class="tx-color-01 tx-24 tx-sm-32 tx-lg-36 mg-xl-b-5">404 {{__('Not Found')}}</h1>
            <h5 class="tx-16 tx-sm-18 tx-lg-20 tx-normal mg-b-20">{{__($exception->getMessage())}}</h5>
        </div>
    </div><!-- container -->
@endsection
