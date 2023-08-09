@if(isset($operationName))
    @if ($crud->hasAccess($operationName))
        @include("vendor")

@endif
@endif
{{-- Button Javascript --}}
{{-- - used right away in AJAX operations (ex: List) --}}
{{-- - pushed to the end of the page, after jQuery is loaded, for non-AJAX operations (ex: Show) --}}
@push('after_scripts') @if (request()->ajax())
    @endpush
@endif

@if (!request()->ajax())
    @endpush
@endif
