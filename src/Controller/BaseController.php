<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use App\Exception\ExceptionInterface;

/**
 * Class BaseController
 * @package App\Controller
 */
abstract class BaseController
{

    /**
     * @param \Exception $exception
     * @return JsonResponse
     */
    public function errorResponse(\Exception $exception)
    {
        if ($exception instanceof ExceptionInterface) {
            return new JsonResponse($this->buildResponse($exception), $exception->getStatusCode());
        } else {
            return new JsonResponse($this->buildResponse($exception), 500);
        }
    }

    /**
     * @param $request
     * @param $keysValues
     * @return array
     */
    protected function getRequestData($request, $keysValues)
    {
        $postData = (array)json_decode($request->getContent(), true);
        $request->request->replace($postData);

        $data = [];

        foreach ($keysValues as $key => $value) {
            $data[$key] = $request->request->get($value);
        }

        return $data;
    }

    /**
     * @param $exception
     * @return mixed $data
     */
    private function buildResponse($exception)
    {
        $data = array(
            'message' => $exception->getMessage(),
        );

        if (method_exists($exception, 'getFields') && !empty($exception->getFields())) {
            foreach ($exception->getFields() as $field) {
                if (method_exists($field, 'getPropertyPath') && method_exists($field, 'getMessage')) {
                    $data['fields'][] = [
                        'name' => $field->getPropertyPath(),
                        'message' => $field->getMessage(),
                    ];
                } else {
                    $data['fields'][] = [
                        'name' => $field['name'],
                        'message' => $field['message'],
                        'extra' => $field['extra'],
                    ];
                }
            }
        }

        return $data;
    }
}
