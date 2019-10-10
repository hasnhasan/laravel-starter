@extends('backend::layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                    <li class="breadcrumb-item"><a href="{{route('home')}}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{__('İçerikler')}}</li>
                </ol>
            </nav>
            <h4 class="mg-b-0 tx-spacing--1">{{__('İçerik Listesi')}}</h4>

        </div>
        <div class="d-none d-md-block">
            <a href="{{route('article.create',['module'=>$module])}}" class="btn btn-outline-success btn-sm pd-x-15 btn-uppercase mg-l-5">
                <i data-feather="plus" class="wd-10 mg-r-5"></i> {{__('YENI İÇERİK EKLE')}}
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-2">
            <fieldset class="bg-gray-50 form-fieldset h-100 px-2">
                <legend class="ml-2">{{__('Modüller')}}</legend>
                <ul class="nav nav-aside">
                    @foreach($modules as $module => $moduleText)
                        <li class="nav-item @if(request('module') == $module) active @endif">
                            <a href="{{route('article.list',$module)}}" class="nav-link"><i data-feather="shopping-bag"></i> <span>{{$moduleText}}</span></a>
                        </li>
                    @endforeach
                </ul>
            </fieldset>
        </div>
        <div class="col-md-10">
            <div id="gridContainer"></div>
        </div>
    </div>
</div>
@endsection
@push('css')
    <style>
       /* .dx-datagrid .dx-column-lines > td,
        .dx-datagrid-borders > .dx-datagrid-pager, .dx-datagrid-borders > .dx-datagrid-headers, .dx-datagrid-borders > .dx-datagrid-filter-panel,
        .dx-datagrid-borders > .dx-datagrid-headers, .dx-datagrid-borders > .dx-datagrid-rowsview, .dx-datagrid-borders > .dx-datagrid-total-footer{
            border:none!important;
        }
        .dx-datagrid-content .dx-datagrid-table .dx-row .dx-editor-cell .dx-texteditor, .dx-datagrid-content .dx-datagrid-table .dx-row .dx-editor-cell .dx-texteditor-container {
            border:1px solid;
        }*/
    </style>
@endpush
@push('js')
    {!! $dataGrid !!}
@endpush
