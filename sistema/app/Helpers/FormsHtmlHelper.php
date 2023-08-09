<?php


use Illuminate\View\View;

function clearInputExtraData($extraData): array
{
    $allowedKeys = [
        'translate',
        'labelExtraClass',
        'inputExtraClass',
        'textChooseOption',
        'formGroupExtraClass',
        'placeholder',
        'disabled',
        'readonly',
        'livewire',
        'attrs',
        'copyBtn',
        'links',
        'hasActions',
        'poppover_text',
        'field_type'
    ];
    foreach ($extraData as $key => $value) {
        if (!in_array($key, $allowedKeys)) {
            unset($extraData[$key]);
        }
    }
    return $extraData;
}

function htmlSelectInput($name, $label, $options, $extraData = null, $selectedVal = ''): View
{
    if (is_null($extraData)) {
        $extraData = [];
    }
    if (!isset($extraData['translate'])) {
        $extraData['translate'] = true;
    }
    if (!isset($extraData['disabled'])) {
        $extraData['disabled'] = false;
    }
    $extraData = clearInputExtraData($extraData);
    return view(sslConfigName() . '.layouts.forms.select', ['name' => $name, 'selectedVal' => $selectedVal, 'label' => $label, 'options' => $options, 'extraData' => $extraData]);
}


function htmlInput($name, $label, $type = 'text', $extraData = null, $currentVal = null): View
{
    if (is_null($extraData)) {
        $extraData = [];
    }
    if (!isset($extraData['translate'])) {
        $extraData['translate'] = true;
    }
    if (!isset($extraData['disabled'])) {
        $extraData['disabled'] = false;
    }
    if (!isset($extraData['placeholder'])) {
        $extraData['placeholder'] = null;
    }
    $extraData = clearInputExtraData($extraData);
    $data = ['name' => $name, 'currentVal' => $currentVal, 'label' => $label, 'type' => $type, "extraData" => $extraData];
    return view(sslConfigName() . '.layouts.forms.input', $data);
}


function htmlInputPopover($name, $label, $poppover_text = null): ?string
{
    $popoverIndex = $name . "_popover";
    $popover = transManual($popoverIndex);

    if (is_null($poppover_text) && !empty($popover) && $popover != $popoverIndex) {
        return popoverInfo(transManual($label), $popover);
    } else if (isset($poppover_text) && !empty($poppover_text)) {
        return popoverInfo(transManual($label), $poppover_text);
    }
    return null;
}

function formatOptionHtmlSelect($defaultText, $arrayData, $translate = true): array
{
    $options = [];
    $i = 0;
    foreach ($arrayData as $op) {
        if ($i == 0) {
            $options[""] = $defaultText;
        }
        if (isValidEmail($op['text']) || !$translate) {
            $options[$op['value']] = transManual($op['text']);
        } else {
            $options[$op['value']] = __($op['text']);
        }
        $i++;
    }

    return $options;
}

function htmlSelectInputArr(array $data)
{
    $name = $data['name'];
    $label = $data['label'];
    $options = $data['options'];
    $extraOptions = [];
    if (isset($data['extraOptions'])) {
        $extraOptions = $data['extraOptions'];
    }
    return htmlSelectInput($name, $label, $options, $extraOptions);
}

function htmlInputArr(array $data)
{
    $name = $data['name'];
    $label = $data['label'];
    $type = $data['type'];
    return htmlInput($name, $label, $type);
}

function replaceTextOnFile($file, $need, $replaceValue)
{
    $fileContent = file_get_contents($file);
    $fileContent = str_replace($need, $replaceValue, $fileContent);
    return file_put_contents($file, $fileContent);
}


function htmlInputWithActions($name, $label, $extraData, $copyBtn = false, $links = [], $currentVal = null): View
{

    $extraData['translate'] = true;
    $extraData['disabled'] = true;
    $extraData['readonly'] = false;

    $extraData['hasActions'] = true;
    $extraData['copyBtn'] = $copyBtn;
    $extraData['links'] = $links;
    $extraData['livewire'] = false;
    if (!isset($extraData['field_type'])) {
        $extraData['field_type'] = 'text';
    }
    $extraData = clearInputExtraData($extraData);
    return htmlInput($name, $label, $extraData['field_type'], $extraData, $currentVal);
}
