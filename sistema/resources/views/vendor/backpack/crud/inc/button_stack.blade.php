@if ($crud->buttons()->where('stack', $stack)->count())
    @foreach ($crud->buttons()->where('stack', $stack) as $button)
        @if(canAccessGroupBtn($crud, $button->name))
            {!! $button->getHtml($entry ?? null) !!}
        @endif

    @endforeach
@endif
