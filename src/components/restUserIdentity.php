<?php
/**
 * Created by PhpStorm.
 * User: dean.sanderson
 * Date: 2018-12-14
 * Time: 16:59
 */

namespace threedgroup\craftrest\components;

use craft;
use yii\web\IdentityInterface;

class restUserIdentity extends craft\elements\User
{

    static function findIdentityByAccessToken($token, $type = null)
    {
        $user = Craft::$app->getUsers()->getUserByUsernameOrEmail($token);
        return $user;
    }
}
