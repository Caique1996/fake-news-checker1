@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
      $crud->entity_name_plural => url($crud->route),
      trans('backpack::crud.add') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;

@endphp

@section('header')
    <section class="container-fluid">
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? __($crud->entity_name_plural) !!}</span>
            <small>{!! $crud->getSubheading() ?? trans('New '.$crud->entity_name) !!}.</small>
            @if ($crud->hasAccess('list'))
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm text-capitalize"><i
                            class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i>{{__("back")}}</a></small>
            @endif
        </h2>
    </section>
@endsection

@section('content')

    <div class="row">
        <div class="{{ $crud->getCreateContentClass() }}">
            {{-- Default box --}}
            @include('crud::inc.grouped_errors')
            <form method="post"
                  action="{{ url($crud->route) }}"
                  @if ($crud->hasUploadFields('create'))
                      enctype="multipart/form-data"
                @endif >

                {!! csrf_field() !!}
                {{-- load the view from the application if it exists, otherwise load the one in the package --}}
                @if(view()->exists('vendor.backpack.crud.form_content'))

                    @include('vendor.backpack.crud.form_content', [ 'fields' => addLabelToFields($crud->fields()), 'action' => 'create' ])
                @else

                    @include('crud::form_content', [ 'fields' => addLabelToFields($crud->fields()), 'action' => 'create' ])
                @endif
                {{-- This makes sure that all field assets are loaded. --}}
                <div class="d-none" id="parentLoadedAssets">{{ json_encode(Assets::loaded()) }}</div>
                @include('crud::inc.form_save_buttons')
            </form>
        </div>
    </div>

@endsection

@if(!is_null($crud->get("extra_view")))
        <?php
        $extraViewData = $crud->get("extra_view");
        ?>
    @push($extraViewData['position'])
        @include($extraViewData['name'],$extraViewData['data'])
    @endpush
@endif
@push('after_scripts')
    <script>
        $('select[name="user_id"]').on('select2:select', function (e) {
            var data = e.params.data;
            var url = location.protocol + '//' + location.host + location.pathname;
            location.href = url + "?user_id=" + data.id;
        });
    </script>
@endpush
