<?php
if (!isset($currentVal)) {
    $currentVal = null;
}
$placeholder = "";
$translate = true;
$formGroupExtraClass = "";
$labelExtraClass = "";
$inputExtraClass = "";
$disabled = false;
$readonly = false;
$copyBtn = false;
$links = [];
$hasActions = false;
$poppover_text = null;

$attrs = ['id' => $name];

$livewire = true;
if (!isset($extraData)) {
    $extraData = [];
}
extract($extraData);
if ($copyBtn) {
    $attrs = ['id' => $name . '_' . rand(0, 999999999999)];
}
$labelText = __($label);
if (!$translate) {
    $labelText = $label;
}

$htmlPopOver = htmlInputPopover($name, $label);
$labelContent = "<b>" . $labelText . "</b>" . $htmlPopOver;
?>
<div class="form-group <?=$formGroupExtraClass?>">
    {!! Form::labelWithoutFor($name,$labelContent,['class'=>'form-control-label '.$labelExtraClass,'for'=>''],false) !!}
    <?php
    $inputAttrs = [
        'class' => "form-control $inputExtraClass",
        'placeholder' => $placeholder,
        'disabled' => $disabled,
        'readonly' => $readonly
    ];
    if ($livewire) {
        $inputAttrs['wire:model'] = $name;
    }
    $selectAttrs = $inputAttrs + $attrs;
    if ($type == 'password') {
        $htmlInput = Form::password($name, $currentVal, $selectAttrs);
    } elseif ($type == 'email') {
        $htmlInput = Form::email($name, $currentVal, $selectAttrs);
    } elseif ($type == 'textarea') {
        $htmlInput = Form::textarea($name, $currentVal, $selectAttrs);
    } elseif ($type == 'textarea_enabled') {
        $selectAttrs['disabled'] = false;
        $htmlInput = Form::textarea($name, $currentVal, $selectAttrs);
    } else {
        $htmlInput = Form::text($name, $currentVal, $selectAttrs);
    }
    ?>
    @if($hasActions)
        <div class="row ">
            <div class="col">
                {!! $htmlInput !!}
            </div>
            @if($copyBtn)
                <span class="col-auto btn-copy-dcv">
                <button onclick="return copyInputText(this,'<?=$attrs['id']?>')" data-bs-placement="top"
                        data-bs-toggle="tooltip"
                        title="{{__("Click here to copy")}}"
                        data-bs-original-title="{{__("Click here to copy")}}" data-text-copied="{{__("Copied!")}}"
                        class="btn btn-primary text-white p-2 d-flex align-items-center justify-content-center">
                    <i class="fe fe-copy"></i>
                </button>
            </span>
            @endif
            @foreach($links as $link)
                <a href="<?=$link['link']?>" target='_blank' class="col-auto <?=$link['class']?>">
                    <button data-bs-toggle="tooltip" title="{{__($link['text'])}}"
                            class="btn btn-blue text-white p-2 d-flex align-items-center justify-content-center">
                        <i class="<?=$link['icon']?>"></i>
                    </button>
                </a>
            @endforeach


        </div>
    @else
        {!! $htmlInput !!}
    @endif


    @error($name)
    {!!showInputError($message)!!}
    @enderror
</div>
