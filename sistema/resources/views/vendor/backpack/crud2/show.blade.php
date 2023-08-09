@extends(backpack_view('blank'))

@php
    $defaultBreadcrumbs = [
      trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
      $crud->entity_name_plural => url($crud->route),
      trans('backpack::crud.preview') => false,
    ];

    // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
    $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('header')
    <section class="container-fluid d-print-none">
        <a href="javascript: window.print();" class="btn float-right"><i class="la la-print"></i></a>
        <h2>
            <span class="text-capitalize">{!! $crud->getHeading() ?? __($crud->entity_name_plural) !!}</span>
            <small>{!! $crud->getSubheading() ?? mb_ucfirst(trans("show ".$crud->entity_name)) !!}
              </small>
            @if ($crud->hasAccess('list'))
                <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm text-capitalize"><i
                            class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i>{{__("back")}}</a></small>
            @endif
        </h2>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="{{ $crud->getShowContentClass() }}">

            {{-- Default box --}}
            <div class="">
                @if ($crud->model->translationEnabled())
                    <div class="row">
                        <div class="col-md-12 mb-2">
                            {{-- Change translation button group --}}
                            <div class="btn-group float-right">
                                <button type="button" class="btn btn-sm btn-primary dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{trans('backpack::crud.language')}}
                                    : {{ $crud->model->getAvailableLocales()[request()->input('_locale')?request()->input('_locale'):App::getLocale()] }}
                                    &nbsp; <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    @foreach ($crud->model->getAvailableLocales() as $key => $locale)
                                        <a class="dropdown-item"
                                           href="{{ url($crud->route.'/'.$entry->getKey().'/show') }}?_locale={{ $key }}">{{ $locale }}</a>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="card no-padding no-border">
                    <table class="table table-striped mb-0">
                        <tbody>
                        @if ($crud->buttons()->where('stack', 'line')->count())
                            <tr class="d-print-none">
                                <td><strong>{{ trans('backpack::crud.actions') }}</strong></td>
                                <td>
                                    @include('crud::inc.button_stack', ['stack' => 'line'])
                                </td>
                            </tr>
                        @endif
                        @foreach ($crud->columns() as $column)
                            <tr>
                                <td>
                                    <strong>{!! __($column['label']) !!} <?= showTooltipCrud($crud, $column['name'], $crud->getOperation()) ?>:</strong>
                                </td>
                                <td>
                                    @php
                                        // create a list of paths to column blade views
                                        // including the configured view_namespaces
                                        $columnPaths = array_map(function($item) use ($column) {
                                            return $item.'.'.$column['type'];
                                        }, \Backpack\CRUD\ViewNamespaces::getFor('columns'));

                                        // but always fall back to the stock 'text' column
                                        // if a view doesn't exist
                                        if (!in_array('crud::columns.text', $columnPaths)) {
                                            $columnPaths[] = 'crud::columns.text';
                                        }
                                    @endphp
                                    @includeFirst($columnPaths)
                                </td>
                            </tr>
                        @endforeach

                        </tbody>
                    </table>
                </div>{{-- /.box-body --}}
            </div>{{-- /.box --}}

        </div>
    </div>
@endsection
