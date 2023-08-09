<?php

namespace App\Services;

use App\Exceptions\ApiException;
use App\Exceptions\SoftException;
use App\Models\Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class  ApiService
{
    static function processApiRequest($rules, $requestData, $callback)
    {
        if (is_array($rules) && count($rules) > 0) {
            $validator = Validator::make($requestData, $rules);
            if ($validator->fails()) {
                $errors = $validator->errors();
                return defaultApiResponse(apiErrorMessage($errors->first(), $errors->all()));
            }
        }
        try {
            DB::beginTransaction();
            $response = $callback();
            DB::commit();
            return defaultApiResponse(apiSuccessMessage($response));
        } catch (ApiException $ex) {
            DB::rollBack();
            return defaultApiResponse(apiErrorMessage($ex->getMessage(), []));
        } catch (SoftException $ex) {
            DB::rollBack();
            return defaultApiResponse(apiErrorMessage($ex->getMessage(), []));
        }catch (ValidationException $ex) {
            DB::rollBack();
            throw new HttpResponseException(response()->json(apiErrorMessageValidation($ex->getMessage(), $ex->errors()), 422));
        } catch (\Exception $ex) {
            DB::rollBack();
            return defaultApiResponse(apiErrorMessage(Exception::register($ex), []));
        }
    }
}
