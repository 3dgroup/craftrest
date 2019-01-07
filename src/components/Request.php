<?php
/**
 * Created by PhpStorm.
 * User: dean.sanderson
 * Date: 2018-12-13
 * Time: 15:27
 */

namespace threedgroup\craftrest\components;


class Request extends \craft\web\Request
{
    /**
     * @inheritdoc
     */
    public function getIsRestRequest(): bool
    {
        return true;
    }
    /**
     * @inheritdoc
     */
    public function getIsSiteRequest(): bool
    {
        return false;
    }


}
