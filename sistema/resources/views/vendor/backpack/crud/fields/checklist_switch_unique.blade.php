{{-- checklist --}}
@php
    $key_attribute = (new $field['model'])->getKeyName();
    $field['attribute'] = $field['attribute'] ?? (new $field['model'])->identifiableAttribute();
    $field['number_of_columns'] = $field['number_of_columns'] ?? 3;

    // calculate the checklist options
    if (!isset($field['options'])) {
                    $fieldModel=$field['model'];
                   $field['options'] = $fieldModel::all()->pluck($field['attribute'], $key_attribute)->toArray();
    } else {
        $field['options'] = call_user_func($field['options'], $field['model']::query());
    }

    // calculate the value of the hidden input
    $field['value'] = old_empty_or_null($field['name'], []) ??  $field['value'] ?? $field['default'] ?? [];
    if(!empty($field['value'])) {
        if (is_a($field['value'], \Illuminate\Support\Collection::class)) {
            $field['value'] = ($field['value'])->pluck($key_attribute)->toArray();
        } elseif (is_string($field['value'])){
          $field['value'] = json_decode($field['value']);
        }
    }

    // define the init-function on the wrapper
    $field['wrapper']['data-init-function'] =  $field['wrapper']['data-init-function'] ?? 'bpFieldInitChecklist';
@endphp

@include('crud::fields.inc.wrapper_start')
<label>{!! __($field['label']) !!} <?= showTooltipCrud($crud, $field['name'], $crud->getOperation()) ?></label>
@include('crud::fields.inc.translatable_icon')

<input type="hidden" value='@json($field['value'])' name="{{ $field['name'] }}">

<div class="row">
    @php

            $field['onLabel'] = $field['onLabel'] ?? '';
            $field['offLabel'] = $field['offLabel'] ?? '';
            $field['color'] = $field['color'] ?? 'primary';
    @endphp
    @foreach ($field['options'] as $key => $option)
        <div class="col-sm-{{ intval(12/$field['number_of_columns']) }}">
            {{-- Switch --}}
            <label class="switch switch-sm switch-label switch-pill switch-{{ $field['color'] }} mb-0"
                   style="--bg-color: {{ $field['color'] }};">
                <input
                    type="hidden"
                    name="{{ $field['name'].'_'.rand(0,99999999) }}"
                    value="{{ (int) $key }}" data-id="{{ (int) $key }}"/>
                <input name="<?=$field['name']?>"
                       type="radio"
                       data-init-function="bpFieldInitSwitch"
                       value="{{ (int) $key }}" data-id="{{ (int) $key }}"
                       class="switch-input"/>
                <span
                    class="switch-slider"
                    data-checked="{{ $field['onLabel'] ?? '' }}"
                    data-unchecked="{{ $field['offLabel'] ?? '' }}">
            </span>
            </label>

            {{-- Label --}}
                <?php
                $optionTrans = __($option);
                $charLimit = 30;
                $len = strlen($optionTrans);
                if ($len > $charLimit) {
                    $lb = substr($optionTrans, 0, $charLimit) . '...';
                } else {
                    $lb = $optionTrans;
                }
                ?>
            <label title="{{$optionTrans}}" class="font-weight-normal mb-0 ml-2"> {{$lb}}</label>
        </div>
    @endforeach
</div>

{{-- HINT --}}
@if (isset($field['hint']))
    <p class="help-block">{!! $field['hint'] !!}</p>
@endif
@include('crud::fields.inc.wrapper_end')


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
{{-- FIELD JS - will be loaded in the after_scripts section --}}
@push('crud_fields_scripts')
    @loadOnce('bpFieldInitChecklist')
    <script>
        function bpFieldInitChecklist(element) {
            var hidden_input = element.find('input[type=hidden]');
            var selected_options = JSON.parse(hidden_input.val() || '[]');
            var checkboxes = element.find('input[type=checkbox]');
            var container = element.find('.row');

            console.log(checkboxes);
            // set the default checked/unchecked states on checklist options
            checkboxes.each(function (key, option) {
                var id = $(this).val();
                if (selected_options.map(String).includes(id)) {
                    $(this).prop('checked', 'checked');
                } else {
                    $(this).prop('checked', false);
                }
            });

            // when a checkbox is clicked
            // set the correct value on the hidden input
            checkboxes.click(function () {
                var newValue = [];

                checkboxes.each(function () {
                    if ($(this).is(':checked')) {
                        console.log($(this));
                        var id = $(this).attr('data-id');
                        newValue.push(id);
                    }
                });

                hidden_input.val(JSON.stringify(newValue)).trigger('change');

            });

            hidden_input.on('CrudField:disable', function (e) {
                checkboxes.attr('disabled', 'disabled');
            });

            hidden_input.on('CrudField:enable', function (e) {
                checkboxes.removeAttr('disabled');
            });

        }
    </script>

    @endLoadOnce
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
