@extends('backend::layouts.app')

@section('content')
<div class="container-fluid">
    <div class="media d-block d-lg-flex pt-0">
        <div class="profile-sidebar profile-sidebar-two pd-lg-r-15 border-right">

            <div class="row">
                @foreach($modulCategories as $module => $categories)
                <div class="col-sm-6 col-md-5 col-lg">
                    <label class="tx-sans tx-10 tx-semibold tx-uppercase tx-color-01 tx-spacing-1 mg-b-15">{{$module}}</label>
                    <ul class="p-0">
                        @foreach($categories as $category)
                        <li>
                            <a href="">{{$category->title}}</a>
                            @if($category->childrens)
                                <ul class="">
                                    @foreach($category->childrens as $category)
                                        <li><a href="">{{$category->title}}</a></li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div><!-- col -->
                @endforeach

            </div><!-- row -->

        </div>
        <div class="media-body mg-t-40 mg-lg-t-0 pd-lg-x-10">
            <form action="">
                @csrf
                {!!Form::text('title', __('Kategori Adı'))->placeholder(__('Kategori adı yazınız.'))->required()!!}
                {!!Form::textarea('content', __('İçerik'))->placeholder(__('İçerik giriniz.'))!!}
                <fieldset class="form-fieldset">
                    <legend>{{__('Seo Bilgileri')}}</legend>
                    {!!Form::text('title', __('Kategori Adı'))->placeholder(__('Kategori adı yazınız.'))->required()!!}
                </fieldset>
            </form>


        </div><!-- media-body -->
    </div><!-- media -->
</div>
@endsection
@push('css')
    <link rel="stylesheet" href="{{asset('assets/css/dashforge.profile.css')}}">
@endpush
