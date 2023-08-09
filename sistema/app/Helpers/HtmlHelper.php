<?php

use Collective\Html\FormBuilder;
use App\Enums\BoolStatus;
use App\Enums\ReviewCheckStatus;

FormBuilder::macro('labelWithoutFor', function ($name, $labelContent, $extraData = [], $showHtml = false) {
    $htmlTagFor = 'for="' . $name . '"';
    return str_replace($htmlTagFor, '', Form::label($name, $labelContent, $extraData, $showHtml));
});
function html_button($label, $class)
{
    return Form::button(__($label), array('class' => $class, 'type' => 'button'), true);
}

function html_button_with_icon($label, $class)
{
    return Form::button(__($label), array('class' => $class, 'type' => 'button'), true);
}

function formatCssColorStylesArr($words, $class, $icon = null)
{
    return [
        'words' => $words,
        'class' => $class,
        'icon' => $icon
    ];
}

function getCssColorByValue($str)
{
    $str = strtolower($str);
    $arrClass = [];
    $words = [
        strtolower(BoolStatus::Active),
        strtolower(ReviewCheckStatus::Real)
    ];
    $arrClass[] = formatCssColorStylesArr($words, 'success', 'la-check-circle');

    $words = [
        strtolower(ReviewCheckStatus::RealBut),
        strtolower(ReviewCheckStatus::ItsIsTooEarly),
        strtolower(ReviewCheckStatus::UnderMonitoring),
        strtolower(ReviewCheckStatus::Exaggerated),
    ];

    $arrClass[] = formatCssColorStylesArr($words, 'warning', 'la-exclamation-circle');

    $words = [
        strtolower(BoolStatus::Inactive),
        strtolower(ReviewCheckStatus::Fake),
        strtolower(ReviewCheckStatus::Contradictory),
        strtolower(ReviewCheckStatus::Unsustainable),
        strtolower(ReviewCheckStatus::Underrated),
    ];
    $arrClass[] = formatCssColorStylesArr($words, 'danger', 'la-times-circle');

    foreach ($arrClass as $class) {
        if (in_array($str, $class['words'])) {
            $matchClass = ['class' => $class['class'], 'icon' => $class['icon']];
        }
    }
    if (!isset($matchClass)) {
        $matchClass = ['class' => 'primary', 'icon' => ''];
    }

    return $matchClass;
}

function html_tag($name, $content, $params, $startChar = '', $endChar = '')
{

    $html = $startChar . "<$name ";
    foreach ($params as $atrr => $value) {
        $html .= $atrr . '="' . $value . '" ';
    }
    return $html . '>' . $content . "</$name>" . $endChar;
}

function html_ahref($label, $link, $targetBlank = false, $params = [], $startChar = '', $endChar = '')
{
    $params['href'] = $link;
    if ($targetBlank) {
        $params['target'] = '_blank';
    }

    return html_tag('a', $label, $params, $startChar, $endChar);
}

function html_icon($icon)
{
    $params['class'] = $icon;
    return html_tag("i", "", $params);
}

function html_button_styled($label)
{
    $data = getCssColorByValue($label);
    $htmlIcon = "";
    if (!empty($data['icon'])) {
        $htmlIcon = html_icon('la ' . $data['icon']);
    }
    return Form::button($htmlIcon . " " . __($label), array('class' => 'text-capitalize btn btn-' . $data['class'], 'type' => 'button'), true);
}

function newInputWithCopyBtn($value, $name, $label = false)
{


    return view('layouts.forms.simple_copy_btn', ['name' => $name, 'value' => $value, 'label' => $label]);
}

function showTooltipCrud($crud, $name, $action)
{

    $commonNameIndex = [
        'orders_common_name_any_tooltip' => __("common_name_tooltip_default"),
        'orders_single_sans_any_tooltip' => __("sans_tooltip_index"),
        'orders_wildcard_sans_any_tooltip' => __("sans_wildcard_tooltip_index")
    ];
    $tooltipName = str_replace(" ", "_", $crud->entity_name_plural);
    $tooltipIndex = $tooltipName . '_' . $name . "_{$action}_tooltip";

    $title = transManual($tooltipIndex);

    if (is_null($title) || $title == $tooltipIndex) {
        $tooltipIndex = $tooltipName . '_' . $name . "_any_tooltip";
        if (isset($commonNameIndex[$tooltipIndex])) {
            $title = $commonNameIndex[$tooltipIndex];
        } else {
            $title = transManual($tooltipIndex);
        }
    }
    if (!is_null($title) && $title <> $tooltipIndex) {
        $params = [];
        $params['data-toggle'] = 'tooltip';
        $params['data-placement'] = 'top';
        $params['class'] = 'la la-question-circle text-info';
        $params['title'] = $title;
        return html_tag("i", "", $params);
    }
    return "";
}


function showTooltip($name)
{
    $tooltipIndex = $name . "_tooltip";
    $title = transManual($tooltipIndex);
    if (!is_null($title) && $title <> $tooltipIndex) {
        $params = [];
        $params['data-toggle'] = 'tooltip';
        $params['data-placement'] = 'top';
        $params['class'] = 'la la-question-circle text-info';
        $params['title'] = $title;
        return html_tag("i", "", $params);
    }
    return "";
}

function addLabelToFields($fields)
{

    foreach ($fields as $key => $field) {
        if (isset($field['label'])) {
            $toolTip = showTooltip($key);
            if (!empty($toolTip)) {
                $fields[$key]['label'] = $field['label'] . ' ' . $toolTip;
            }
        }
    }
    return $fields;
}

function customHtmlLink($url, $iconClass, $label, $params = [])
{
    $url = e($url);
    $content = html_icon("$iconClass") . " " . $label;

    return html_ahref($content, $url, false, $params);
}

function htmlBold($content)
{
    return html_tag('b', $content, []);
}
