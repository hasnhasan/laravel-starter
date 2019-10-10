@extends('backend::layouts.app')

@section('content')
{!!Form::open()->fill($article)!!}
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('article.list')}}">İçerikler</a></li>
                        @if($article->id)
                            <li class="breadcrumb-item active" aria-current="page">{{__('Detay')}}</li>
                        @else
                            <li class="breadcrumb-item active" aria-current="page">{{__('Yeni İçerik')}}</li>
                        @endif
                    </ol>
                </nav>
                @if($article->id)
                    <h4 class="mg-b-0 tx-spacing--1">{{__('":baslik" içeriğini düzenle',['baslik'=>$article->title])}}</h4>
                @else
                    <h4 class="mg-b-0 tx-spacing--1">{{__('Yeni içerik ekle')}}</h4>
                @endif

            </div>
            <div class="d-none d-md-block">
                <a href="{{route('article.list')}}" class="btn btn-link btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i data-feather="chevrons-left" class="wd-10 mg-r-5"></i> Geri</a>
                <button class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5" type="reset"> Sıfırla</button>
                <button type="submit" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="save" class="wd-10 mg-r-5"></i> Kaydet</button>
            </div>
        </div>

        <div class="row">
            <div class="col-md-9">
                <fieldset class="bg-gray-50 form-fieldset h-100">
                    <legend>{{__('İçerik Bilgileri')}}</legend>
                    {!!Form::text('title', __('Başlık'))->attrs(['data-slug'=>str_slug($article->title)])!!}
                    {!!Form::textarea('summary', __('Özet'))->placeholder(__('Meta Açıklaması giriniz..'))!!}
                    {!!Form::textarea('content', __('İçerik'))->placeholder(__('Meta Açıklaması giriniz..'))->attrs(['class'=>'ckeditor'])!!}
                </fieldset>
            </div>
            <div class="col-md-3">
                <fieldset class="bg-gray-50 form-fieldset">
                    <legend>{{__('Yayınlama Bilgileri')}}</legend>
                    {!!Form::select('status', __('Durum'))->options(['Active'  => __('Aktif'),'Passive' => __('Pasif'),'Draft'   => __('Taslak'),])!!}
                    {!!Form::text('published_date', __('Yayın Tarihi'))!!}
                    {!!Form::text('expiry_date', __('Bitiş Tarihi'))!!}
                </fieldset>

                @include('backend::media-manager.select-box',['row'=>$article])

            </div>
            <div class="col-md-12">
                <fieldset class="bg-gray-50 form-fieldset mt-3">
                    <legend>{{__('Seo Bilgileri')}}</legend>
                    <div class="row">
                        <div class="col-md-6">
                            {!!Form::text('route[title]', __('Başlık'))->placeholder(__('Sayfanın tarayıcıda gözükecek başlığını giriniz..'))->value($article->route->title)!!}
                        </div>
                        <div class="col-md-6">
                            {!!Form::text('route[keywords]', __('Anahtar Kelimeler'))->placeholder(__('Meta Anahtar kelimeleri giriniz..'))->attrs(['data-role'=>'tagsinput'])->value($article->route->keywords)!!}
                        </div>
                        <div class="col-md-12 mt-2">
                            {!!Form::text('route[slug]', __('Link'))->placeholder(__('slug..'))->value($article->route->slug)!!}
                            {!!Form::textarea('route[description]', __('Açıklama'))->placeholder(__('Meta Açıklaması giriniz..'))->value($article->route->description)!!}
                        </div>
                    </div>

                </fieldset>
            </div>
        </div>
    </div>
{!!Form::close()!!}
@endsection
@push('css')
    <link rel="stylesheet" href="{{asset('assets/css/dashforge.profile.css')}}">
@endpush
