<?php
if (!isset($selectedVal)) {
    $selectedVal = "";
}
$translate = true;
$labelExtraClass = "";
$inputExtraClass = "";
$textChooseOption = __("Choose an option");
$disabled = false;
$readonly = false;
$livewire = true;
$poppover_text = null;

$attrs = ['id' => $name];
if (!isset($extraData)) {
    $extraData = [];
}
extract($extraData);
$labelText = __($label);
if (!$translate) {
    $labelText = $label;
}
$htmlPopOver = htmlInputPopover($name, $label);
$labelContent = "<b>" . $labelText . "</b>" . $htmlPopOver;
?>
{!! Form::labelWithoutFor($name,$labelContent,['class'=>'form-control-label '.$labelExtraClass],false) !!}
<?php
$selectAttrs = [
    'class' => "form-select $inputExtraClass",
    'disabled' => $disabled,
    'readonly' => $readonly
];
if ($livewire) {
    $selectAttrs['wire:model'] = $name;
}
$selectAttrs = $selectAttrs + $attrs;
echo Form::select($name, formatOptionHtmlSelect($textChooseOption, $options), $selectedVal, $selectAttrs);
?>
@error($name)
{!!showInputError($message)!!}
@enderror
