<?php
namespace App\Traits;

trait ExceptionTrait
{
    private $extraData = [];

    public function setExtraData(array $data)
    {
        $this->extraData = $data;
    }

    public function getExtraData()
    {
        return $this->extraData;
    }

    public function getExtraDataAsJson()
    {
        return json_encode($this->getExtraData());
    }

    public static function formatApiException($message, $method, $endpoint, $getData, $postData)
    {
        $exception = new  self(__($message));
        $extraData = [
            'endpoint' => $endpoint,
            'getData' => $getData,
            'postData' => $postData,
            'method' => $method
        ];
        $exception->setExtraData($extraData);
        return $exception;
    }
}
