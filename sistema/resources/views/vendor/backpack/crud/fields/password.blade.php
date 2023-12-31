{{-- password --}}

@php
    // autocomplete off, if not otherwise specified
    if (!isset($field['attributes']['autocomplete'])) {
        $field['attributes']['autocomplete'] = "off";
    }
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! __($field['label']) !!} <?= showTooltipCrud($crud, $field['name'], $crud->getOperation()) ?></label>
    @include('crud::fields.inc.translatable_icon')
    <input
    	type="password"
    	name="{{ $field['name'] }}"
        value="{{ $field['value'] ?? $field['default'] ?? '' }}"
        @include('crud::fields.inc.attributes')
    	>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')
