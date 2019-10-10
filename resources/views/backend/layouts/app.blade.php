<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title',config('app.name', 'Laravel'))</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/img/favicon.png')}}">

    <!-- vendor css -->
    <link href="{{asset('assets/lib/@fortawesome/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/lib/ionicons/css/ionicons.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/lib/jqvmap/jqvmap.min.css')}}" rel="stylesheet">

    <!-- DashForge CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/dashforge.css')}}">
    <link href="{{asset('assets/lib/bootstrap-tagsinput/bootstrap-tagsinput.css')}}" rel="stylesheet">
    @stack('css')
</head>
<body>

<aside class="aside aside-fixed">
    <div class="aside-header">
        <a href="index.html" class="aside-logo">dash<span>forge</span></a>
        <a href="" class="aside-menu-link">
            <i data-feather="menu"></i>
            <i data-feather="x"></i>
        </a>
    </div>
    <div class="aside-body">
        @include('backend.partials.user-menu')
        @include('backend.partials.menu')
    </div>
</aside>

<div class="content ht-100v pd-0">
    @include('backend.partials.top-bar')

    <div class="content-body @stack('content-body')">
        @yield('content')
    </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="dynamic-modal">
    <div class="modal-dialog modal-lg modal-dialog-centered wd-100p" role="document">
        <div class="modal-content">
            <iframe class="modal-body" style="height: calc(100vh - 70px);" frameborder="0"></iframe>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<script src="{{asset('assets/lib/jquery/jquery.min.js')}}"></script>
<script src="{{asset('assets/lib/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/lib/feather-icons/feather.min.js')}}"></script>
<script src="{{asset('assets/lib/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function (event) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    window.mediaManagerUrl = "{{route('media-manager.popup')}}";
</script>
<script src="{{asset('assets/js/dashforge.js')}}"></script>
<script src="{{asset('assets/js/dashforge.aside.js')}}"></script>
<script src="{{asset('assets/lib/bootstrap-tagsinput/bootstrap-tagsinput.min.js')}}"></script>
<script src="{{asset('assets/lib/typeahead.js/typeahead.bundle.min.js')}}"></script>
<!-- append theme customizer -->
<script src="{{asset('assets/lib/js-cookie/js.cookie.js')}}"></script>
<script src="{{asset('assets/lib/ckeditor/ckeditor.js')}}"></script>
<script>
    var slugifyText = function (text) {
        text = text.toString().toLowerCase().trim();

        const sets = [
            {to: 'a', from: '[ÀÁÂÃÄÅÆĀĂĄẠẢẤẦẨẪẬẮẰẲẴẶ]'},
            {to: 'c', from: '[ÇĆĈČ]'},
            {to: 'd', from: '[ÐĎĐÞ]'},
            {to: 'e', from: '[ÈÉÊËĒĔĖĘĚẸẺẼẾỀỂỄỆ]'},
            {to: 'g', from: '[ĜĞĢǴ]'},
            {to: 'h', from: '[ĤḦ]'},
            {to: 'i', from: '[ÌÍÎÏĨĪĮİỈỊ]'},
            {to: 'j', from: '[Ĵ]'},
            {to: 'ij', from: '[Ĳ]'},
            {to: 'k', from: '[Ķ]'},
            {to: 'l', from: '[ĹĻĽŁ]'},
            {to: 'm', from: '[Ḿ]'},
            {to: 'n', from: '[ÑŃŅŇ]'},
            {to: 'o', from: '[ÒÓÔÕÖØŌŎŐỌỎỐỒỔỖỘỚỜỞỠỢǪǬƠ]'},
            {to: 'oe', from: '[Œ]'},
            {to: 'p', from: '[ṕ]'},
            {to: 'r', from: '[ŔŖŘ]'},
            {to: 's', from: '[ßŚŜŞŠ]'},
            {to: 't', from: '[ŢŤ]'},
            {to: 'u', from: '[ÙÚÛÜŨŪŬŮŰŲỤỦỨỪỬỮỰƯ]'},
            {to: 'w', from: '[ẂŴẀẄ]'},
            {to: 'x', from: '[ẍ]'},
            {to: 'y', from: '[ÝŶŸỲỴỶỸ]'},
            {to: 'z', from: '[ŹŻŽ]'},
            {to: '-', from: '[·/_,:;\']'}
        ];

        sets.forEach(set => {
            text = text.replace(new RegExp(set.from,'gi'), set.to);
        });

        text = text.toString().toLowerCase()
            .replace(/\s+/g, '-')         // Replace spaces with -
            .replace(/&/g, '-and-')       // Replace & with 'and'
            .replace(/[^\w\-]+/g, '')     // Remove all non-word chars
            .replace(/\--+/g, '-')        // Replace multiple - with single -
            .replace(/^-+/, '')           // Trim - from start of text
            .replace(/-+$/, '');          // Trim - from end of text

        if ((typeof separator !== 'undefined') && (separator !== '-')) {
            text = text.replace(/-/g, separator);
        }

        return text;
    }

    $(document).find('[data-slug]').on('input', function () {
        var slug = $(this).attr('data-slug');
        var slugField = $('[name="route[slug]"]');
        if(slug == slugField.val()) {
            var newSlug = slugifyText($(this).val());
            slugField.val(newSlug);
            $(this).attr('data-slug',newSlug);
        }
    });
    $(document).find('[name="route[slug]"]').on('change', function () {
        $(this).val(slugifyText($(this).val()));
    });


    $(document).on('click', '[data-role="file-browser"]', function () {
        window.fileBrowser(this);
    });

    window.fileBrowser = function (element) {
        var type = 'TYPE_IMAGE';
        if ($(element).data('media-type')) {
            type = $(element).data('media-type');
        }
        var mediaUrl = '{{route('media-manager.popup')}}?popup=1type=' + type + '&input=' + $(element).data('media') + '&contentTitle=' + $('[data-toggle="slug"]').eq(0).val();
        $("#dynamic-modal .modal-body").attr('src', mediaUrl);
        $("#dynamic-modal").modal("show");
    };

    window.closeModal = function (element) {
        $('#dynamic-modal').modal('hide');
        $(element).change();
    };

    window.resetMedia = function (element) {
        var parentElement = $(element).parents('.img-select');
        if (parentElement.length > 0) {
            $(element).val('');
            parentElement.find('.alt-input').val('').fadeOut();
            parentElement.find('.btn-reset').fadeOut();
            parentElement.find('.btn-select').fadeIn();
            parentElement.find('figure img').attr('src', 'https://placehold.it/195x145/?text=+');
        } else {
            var parentElement = $(element).closest('.form-group');
            var mediaInput = '#' + parentElement.find('.btn-select').data('media');

            parentElement.find('.alt-input').val('').fadeOut();
            parentElement.find('.btn-reset').fadeOut();
            parentElement.find('.btn-select').attr('data-media-input', mediaInput).fadeIn();
            $(element).fadeOut();
        }
    };

    window.addMedia = function (mediaUrl, mediaId, element) {
        $(element).val(mediaId);
        var parentElement = $(element).parents('.img-select');
        if (parentElement.length > 0) {
            var mediaInput = '#' + parentElement.find('.btn-select').data('media');

            parentElement.find('figure img').attr('src', mediaUrl);
            parentElement.find('footer .btn-select').fadeOut();
            parentElement.find('footer .btn-reset').attr('data-media-input', mediaInput).fadeIn();
            parentElement.find('.alt-input').fadeIn();
            $(element).fadeOut();
        } else {
            var parentElement = $(element).closest('.form-group');
            var mediaInput = '#' + parentElement.find('.btn-select').data('media');

            parentElement.find('.alt-input').fadeIn();
            parentElement.find('.btn-select').fadeOut();
            parentElement.find('.btn-reset').attr('data-media-input', mediaInput).fadeIn();
            $(element).fadeOut();
        }
    }
</script>
@stack('js')
</body>
</html>
