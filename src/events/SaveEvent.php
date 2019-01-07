<?php
/**
 * Created by PhpStorm.
 * User: dean.sanderson
 * Date: 2018-12-28
 * Time: 15:33
 */

namespace threedgroup\craftrest\events;

use craft\events\CancelableEvent;


class SaveEvent extends CancelableEvent
{
    public $element;
    public $config;
}
