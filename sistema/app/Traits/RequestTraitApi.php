<?php
namespace App\Traits;

use App\Models\User;
use App\Rules\CheckOrderOwnerApi;

trait RequestTraitApi
{
    private $apiUser;

    public function authorize()
    {
        return true;
    }

    public function getUserApi(): ?User
    {
        if (is_null($this->apiUser)) {
            return getApiUser();
        } else {
            return $this->apiUser;
        }
    }

    function defaultOrderUuidValidation(User $user)
    {
        return ['required', 'exists:orders,uuid', new CheckOrderOwnerApi($user)];
    }

    public function defaultProductIdValidation(User $user)
    {
        $productService = getProductServiceInstance();
        return 'required|integer|exists:products,id|in:' . implode(",", $productService->getActiveProductsIdsByUser($user));

    }

    private function removeOptionalRequiredFields($rules, $rulesStep1, $rulesStep2, $rulesStep3)
    {
        $rules = array_merge($rules, removeRequiredValidation($rulesStep1));
        $rules = array_merge($rules, removeRequiredValidation($rulesStep2));
        return array_merge($rules, removeRequiredValidation($rulesStep3));

    }

}
