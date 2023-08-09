@if ($crud->hasAccess('show'))
	@if (!$crud->model->translationEnabled())

        @if($crud->model instanceof  \App\Models\Order)
            <?=  customHtmlLink(url($crud->route.'/'.$entry->getKey().'/show') , 'la la-cog text-warning', __("manage"),['class'=>'btn btn-sm btn-link text-success text-capitalize'])?>
        @else
            {{-- Single edit button --}}
            <a href="{{ url($crud->route.'/'.$entry->getKey().'/show') }}" class="btn btn-sm btn-link"><i class="la la-eye"></i> {{ trans('backpack::crud.preview') }}</a>

        @endif

	@else

	{{-- Edit button group --}}
	<div class="btn-group">
	  <a href="{{ url($crud->route.'/'.$entry->getKey().'/show') }}" class="btn btn-sm btn-link pr-0"><i class="la la-eye"></i> {{ trans('backpack::crud.preview') }}</a>
	  <a class="btn btn-sm btn-link dropdown-toggle text-primary pl-1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	    <span class="caret"></span>
	  </a>
	  <ul class="dropdown-menu dropdown-menu-right">
  	    <li class="dropdown-header">{{ trans('backpack::crud.preview') }}:</li>
	  	@foreach ($crud->model->getAvailableLocales() as $key => $locale)
		  	<a class="dropdown-item" href="{{ url($crud->route.'/'.$entry->getKey().'/show') }}?_locale={{ $key }}">{{ $locale }}</a>
	  	@endforeach
	  </ul>
	</div>

	@endif
@endif
