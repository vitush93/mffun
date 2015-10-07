<?php

namespace App\Model\Services;


use Latte\Object;
use Nette\Application\Responses\JsonResponse;

abstract class RestService extends Object
{
    /**
     * @param array $data
     * @return JsonResponse
     */
    protected function createJsonResponse(array $data)
    {
        return new JsonResponse($data);
    }
}