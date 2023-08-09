@if($crud->hasAccess('create'))
    <a href="{{ url($crud->route.'/create') }}" class="btn btn-primary text-capitalize" data-style="zoom-in"><span
            class="ladda-label"><i class="la la-plus"></i> <?= trans('New ' . $crud->entity_name) ?></span></a>
@endif
