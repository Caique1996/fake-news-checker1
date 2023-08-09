<?php
    $labelHtml='';
    if($label){
        $labelHtml='<label>'.__($label).'</label>';
    }
?><div class="row "> <div class="col"><?=$labelHtml?><input type="text" class="form-control" value="<?=$value?>" name="<?=$name?>" id="<?=$name?>" disabled></div><span  class="col-auto "><button onclick="copyInputText(this)" style="margin-left: -20px" onclick="" data-bs-placement="top" data-bs-toggle="tooltip" data-input-id="<?=$name?>" title="@lng("Click here to copy")"  data-text-copied-title="@lng("Success!")" data-text-copied="@lng("Copied!")" class="btn btn-primary text-white p-2 d-flex align-items-center justify-content-center d-print-none"> <i class="la la-copy"></i> </button> </span></div>
