{{-- checklist --}}
@php
    $uniqueSelection=false;
        if(isset($field['unique_selection']) && $field['unique_selection']){
$uniqueSelection=true;
        }
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


@php
    $field['onLabel'] = $field['onLabel'] ?? '';
    $field['offLabel'] = $field['offLabel'] ?? '';
    $field['color'] = $field['color'] ?? 'primary';
        $inputName='';
        $inputType='checkbox';
        $onclickFn="";
        if($uniqueSelection){
            $inputName=$field['name'].'_'.rand(0,99999999);
            $inputType='radio';
            $onclickFn="bpUpdateRadioFiel(this)";
        }
@endphp

<input type="hidden" value='@json($field['value'])' name="{{ $field['name'] }}" data-input-type="<?= $inputType ?>">

<div class="row">


    @foreach ($field['options'] as $key => $option)
        <div class="col-sm-{{ intval(12/$field['number_of_columns']) }}">
            {{-- Switch --}}
            <label class="switch switch-sm switch-label switch-pill switch-{{ $field['color'] }} mb-0"
                   style="--bg-color: {{ $field['color'] }};" data-id="{{ (int) $key }}"
                   data-group="<?=$field['name']?>" onclick="<?=$onclickFn?>">
                <input
                    type="hidden"
                    name="{{ $field['name'].'_'.rand(0,99999999) }}"
                    value="{{ (int) $key }}" data-id="{{ (int) $key }}"
                    data-group="<?=$field['name']?>" onclick="<?=$onclickFn?>"/>
                <input @if(!empty($inputName)) name="{{$inputName}}" @endif
                type="{{$inputType}}"
                       data-init-function="bpFieldInitSwitch"
                       value="{{ (int) $key }}"
                       class="switch-input" data-id="{{ (int) $key }}"
                       data-group="<?=$field['name']?>" onclick="<?=$onclickFn?>"/>

                <span
                    class="switch-slider"
                    data-checked="{{ $field['onLabel'] ?? '' }}"
                    data-unchecked="{{ $field['offLabel'] ?? '' }}" data-id="{{ (int) $key }}"
                    data-group="<?=$field['name']?>" onclick="<?=$onclickFn?>">
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
            <label title="{{$optionTrans}}" class="font-weight-normal mb-0 ml-2"
                   data-id="{{ (int) $key }}"
                   data-group="<?=$field['name']?>" onclick="<?=$onclickFn?>"> {{$lb}}</label>
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


        function bpUpdateRadioFiel(e) {
            let element = $(e);
            let groupName = element.attr("data-group");
            let id = element.attr("data-id");

            var hidden_input = $("input[name=" + groupName + "]")
            let newValue = [];
            newValue.push(id);


            hidden_input.val(JSON.stringify(newValue)).trigger('change');

        }

        function bpFieldInitChecklist(element) {
            var hidden_input = element.find('input[type=hidden]');
            var inputType = hidden_input.attr("data-input-type");
            var selected_options = JSON.parse(hidden_input.val() || '[]');

            var checkboxes = element.find('input[type=' + inputType + ']');
            var container = element.find('.row');

            // set the default checked/unchecked states on checklist options
            checkboxes.each(function (key, option) {
                console.log(this);
                let el = $(this);

                let id = el.val();
                let groupName = el.attr("data-group");
                console.log(id + " " + inputType + " " + groupName)
                if (selected_options.map(String).includes(id)) {
                    el.prop('checked', 'checked');
                } else {
                    el.prop('checked', false);
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

    @loadOnce('bpFieldInitSwitchScript')
    <script>
        function bpFieldInitSwitch($element) {
            let element = $element[0];
            let hiddenElement = element.previousElementSibling;
            let id = `switch_${hiddenElement.name}_${Math.random() * 1e18}`;

            // set unique IDs so that labels are correlated with inputs
            element.setAttribute('id', id);
            element.parentElement.nextElementSibling.setAttribute('for', id);

            // set the default checked/unchecked state
            // if the field has been loaded with javascript
            hiddenElement.value !== '0'
                ? element.setAttribute('checked', true)
                : element.removeAttribute('checked');

            // JS Field API
            $(hiddenElement).on('CrudField:disable', () => element.setAttribute('disabled', true));
            $(hiddenElement).on('CrudField:enable', () => element.removeAttribute('disabled'));

            // when the checkbox is clicked
            // set the correct value on the hidden input
            $element.on('change', () => {
                hiddenElement.value = element.checked ? 1 : 0;
                hiddenElement.dispatchEvent(new Event('change'));
            });
        }
    </script>
    @endLoadOnce

@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
