@php
    $name = '_media';
    if (isset($inputName)) $name = $inputName;
    if (!isset($row)) $row = NULL;

@endphp
@foreach ($useImage as $type => $mediaName)
    @if($row && $row->hasMedia($type))
        @php
            $media = $row->firstMedia($type);
        @endphp
        <fieldset class="bg-gray-50 form-fieldset media-browser p-2 mt-2">
            <legend class="ml-2">{{ __($mediaName) }}</legend>
            <div class="img-select">
                <figure class="pos-relative mg-b-0">
                    <img src="{{ $media->getUrl() }}" class="w-100">
                </figure>
                <footer>
                    <input type="hidden" name="{{$name}}[{{$type}}][id]" value="{{ $media->pivot->media_id}}" id="media-input-{{$type}}">
                    <input type="text" name="{{$name}}[{{$type}}][alt]" value="{{ $media->pivot->alt }}" class="mr-t-5 rounded-0 form-control alt-input" placeholder="{{__('Alt etiketi yazınız..')}}">
                    <button type="button" class="btn rounded-0 btn-primary btn-sm btn-block btn-select" style="display: none;" onclick="window.fileBrowser(this);" data-media="media-input-{{$type}}">{{ __('Resim Seç') }}</button>
                    <button type="button" class="btn rounded-0 btn-danger  btn-sm btn-block btn-reset"  data-media-input="media-input-{{$type}}" onclick="window.resetMedia('#media-input-{{$type}}');">{{ __('Kaldır') }}</button>
                </footer>
            </div>
        </fieldset>
    @else
        <fieldset class="bg-gray-50 form-fieldset media-browser p-2 mt-2">
            <legend class="ml-2">{{ __($mediaName) }}</legend>
            <div class="img-select">
                <figure class="pos-relative mg-b-0">
                    <img src="https://fakeimg.pl/350x275/?text={{__('Seçiniz')}}" class="w-100">
                </figure>
                <footer>
                    <input type="hidden" name="{{$name}}[{{$type}}][id]" value="" id="media-input-{{$type}}">
                    <input type="text" name="{{$name}}[{{$type}}][alt]" class="mr-t-5 rounded-0 form-control alt-input" style="display: none;" placeholder="{{__('Alt etiketi yazınız..')}}">
                    <button type="button" class="btn-block btn rounded-0 btn-primary btn-sm btn-block btn-select" onclick="window.fileBrowser(this);" data-media="media-input-{{$type}}">{{ __('Resim Seç') }}</button>
                    <button type="button" class="btn-block btn rounded-0 btn-danger  btn-sm btn-block btn-reset" style="display: none;" data-media-input="" onclick="window.resetMedia('#media-input-{{$type}}');">{{ __('Kaldır') }}</button>
                </footer>
            </div>
        </fieldset>
    @endif
@endforeach
