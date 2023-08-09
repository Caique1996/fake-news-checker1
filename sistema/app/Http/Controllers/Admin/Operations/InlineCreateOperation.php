<?php

namespace App\Http\Controllers\Admin\Operations;

use Backpack\CRUD\app\Exceptions\BackpackProRequiredException;

if (! backpack_pro()) {
    trait InlineCreateOperation
    {
        public function setupInlineCreateOperationDefaults()
        {
            throw new BackpackProRequiredException('InlineCreateOperation');
        }
    }
} else {
    trait InlineCreateOperation
    {
        use \Backpack\Pro\Http\Controllers\Operations\InlineCreateOperation;
    }
}
