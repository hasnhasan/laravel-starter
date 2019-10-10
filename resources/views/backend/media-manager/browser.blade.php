@section('content')
    <div class="container-fluid pd-x-0">

        <div class="dx-viewport demo-container">
            <div id="file-manager"></div>
        </div>

    </div>
@endsection
@push('content-body','p-0')
@push('css')
    <link rel="stylesheet" href="https://cdn3.devexpress.com/jslib/19.1.6/css/dx.common.css">
    <link rel="stylesheet" href="https://cdn3.devexpress.com/jslib/19.1.6/css/dx.light.css">
    <style>
        .dx-filemanager {border: none;}
        .dx-filemanager * {border-color: rgba(72, 94, 144, 0.16) !important;}
    </style>
@endpush
@push('js')
    <script src="https://cdn3.devexpress.com/jslib/19.1.6/js/dx.all.js"></script>
    <script src="https://cdn3.devexpress.com/jslib/19.1.6/js/localization/dx.messages.tr.js"></script>
    <script type="text/javascript" charset="utf-8" async defer>
        function sendFileManeger(mediaUrl, mediaId) {
                @if(isset($input) && $input)
            var parentElement = '#{{$input}}';
            /*if(document.getElementById('multipleSelect').checked){
                window.parent.addMultiMedia(mediaUrl,mediaId);
                return;
            }*/
            window.parent.addMedia(mediaUrl, mediaId, parentElement);
            window.parent.closeModal(parentElement);
            @endif

            @if(isset($CKEditorFuncNum) && $CKEditorFuncNum !== false)
            window.opener.CKEDITOR.tools.callFunction({{$CKEditorFuncNum}}, mediaUrl);
            window.close();
            @endif
        }
    </script>
    <script>
        $(function () {
            DevExpress.localization.locale(navigator.language || navigator.browserLanguage);
            window.filemanager = $("#file-manager").dxFileManager({
                name: "fileManager",
                fileProvider: new DevExpress.FileProviders.WebApi({
                    endpointUrl: "{{route('media-manager.action')}}@if(request('popup'))?popup=1 @endif"
                }),
                @if(request('popup') ||request('type'))
                itemView: {
                    mode: "thumbnails"
                },
                @endif
                permissions: {
                    create: true,
                    move: true,
                    remove: true,
                    rename: true,
                    upload: true
                },
                height: window.innerHeight - 60,
                onSelectedFileOpened: function (e) {
                    sendFileManeger(e.fileItem.dataItem.url, e.fileItem.dataItem.mediaId);
                },
            });
        });
    </script>
@endpush
