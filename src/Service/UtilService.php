<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UtilHelper
 * @package App\Helper
 */
class UtilService
{
    const ERROR_RESPONSE_TYPE = 'error';
    const SUCCESS_RESPONSE_TYPE = 'success';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * UtilHelper constructor.
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param array $data
     * @param array $fields
     * @return array|bool
     */
    public static function checkRequiredFieldsByRequestedData(array $data, array $fields)
    {
        $valuesArray = [];
        foreach ($fields as $field) {
            $value = isset($data[$field]) ? $data[$field] : null;
            if ($value === null) {
                return false;
            }
            $valuesArray[$field] = $value;
        }
        return $valuesArray;
    }

    /**
     * @param $statusCode
     * @param null $message
     * @param null $data
     * @param $type
     * @return JsonResponse
     */
    public function makeResponse($statusCode, $message = null, $data = null, $type = self::ERROR_RESPONSE_TYPE)
    {
        $response['data'] = is_array($data) ? $data : null;

        $response['code'] = $statusCode;

        $response['message'] = !empty($message) ? $this->translator->trans($message) :
            self::SUCCESS_RESPONSE_TYPE;

        $response['status'] = $type;

        return new JsonResponse($response, $statusCode);
    }

    /**
     * @param $fields
     * @param $data
     * @return array
     */
    public function isRequiredFieldsExist($fields, $data)
    {
        $missingFields = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $missingFields[] = $field;
            }
        }

        return $missingFields;
    }
}
