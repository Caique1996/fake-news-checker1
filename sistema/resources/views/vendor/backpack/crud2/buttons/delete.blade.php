@if ($crud->hasAccess('delete'))
        <?php
        $data = [
            'textConfirm' =>trans('backpack::crud.delete_confirm'),
            'postDataBtn'=>json_encode([]),
            'actionRoute'=>url($crud->route.'/'.$entry->getKey()),
            'actionReqType'=>'DELETE',
            'class'=>'delete text-danger',
            'actionNameIcon'=>'la-trash',
            'title'=>trans('backpack::crud.delete')
        ];

        ?>
        @include("vendor.backpack.crud.html.custom_html_btn_ajax",$data)

@endif
