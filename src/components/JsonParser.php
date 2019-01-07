<?php
/**
 * Created by PhpStorm.
 * User: dean.sanderson
 * Date: 2018-12-13
 * Time: 16:13
 */

namespace threedgroup\craftrest\components;


class JsonParser extends \yii\web\JsonParser
{
    /**
     * @inheritdoc
     */
    public function parse($rawBody, $contentType)
    {
        $bodyParams = parent::parse($rawBody, $contentType);
        $bodyParams = $this->filterNullValuesFromArray($bodyParams);
        return $bodyParams;
    }
    /**
     * Filters null values from an array.
     * @param array $arr
     * @return array
     */
    public function filterNullValuesFromArray(array $arr): array
    {
        foreach ($arr as $key => $value) {
            if ($value === null) {
                unset($arr[$key]);
            }
            if (is_array($value)) {
                $arr[$key] = $this->filterNullValuesFromArray($value);
            }
        }
        return $arr;
    }
}
