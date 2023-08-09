<?php
if (!isset($placeholder)) {
    $placeholder = "";
}
?>

<div class="form-group ">
    <label class="form-label" for="separated-input"><b>@lng($label)</b>
        <?php
        if (!empty($poppover_text) && !is_null($poppover_text)) {
            echo popoverInfo(transManual($label), $poppover_text);
        }
        $inputId = $name . '_' . rand(0, 999);
        ?>
    </label>
    <div class="row ">
        <div class="col">
            <input type="text" class="form-control"
                   placeholder="{{$placeholder}}" value="<?=$inputValue?>" name="<?=$name?>" id="<?=$inputId?>"
                   @if($disabled) disabled @endif>
        </div>
        @if($hasCopyBtn)
            <span class="col-auto btn-copy-dcv">
                <button onclick="copyInputText(this,'<?=$inputId?>')" data-bs-placement="top"
                        data-bs-toggle="tooltip"
                        title="{{__("Click here to copy")}}"
                        data-bs-original-title="{{__("Click here to copy")}}" data-text-copied="{{__("Copied!")}}"
                        class="btn btn-primary text-white p-2 d-flex align-items-center justify-content-center">
                    <i class="fe fe-copy"></i>
                </button>
            </span>
        @endif
        @if(!empty($downloadLink))
            <a href="<?=$downloadLink?>" target='_blank' class="col-auto btn-copy-dcv">
                <button data-bs-toggle="tooltip" title="{{__("Download validation file")}}"
                        class="btn btn-blue text-white p-2 d-flex align-items-center justify-content-center">
                    <i class="fe fe-download"></i>
                </button>
            </a>
        @endif
    </div>
</div>
