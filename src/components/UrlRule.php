<?php
/**
 * Created by PhpStorm.
 * User: dean.sanderson
 * Date: 2018-12-13
 * Time: 15:29
 */

namespace threedgroup\craftrest\components;


class UrlRule extends \craft\app\web\UrlRule
{

    /**
     * The prefix to the action
     *
     * @var string
     */
    public $prepend;
    /**
     * @inheritdoc
     */
    protected function createRule($pattern, $prefix, $action)
    {
        if (!empty($this->prepend)) {
            $action = $this->prepend . '/' . $action;
        }

        return parent::createRule($pattern, $prefix, $action);
    }

}
