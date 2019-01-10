<?php
/**
 * Created by PhpStorm.
 * User: dean.sanderson
 * Date: 2018-12-14
 * Time: 16:59
 */

namespace threedgroup\craftrest\components;

use craft;
use threedgroup\craftrest\models\Token;
use yii\web\IdentityInterface;

class restUserIdentity extends craft\elements\User
{

    static function findIdentityByAccessToken($token, $type = null)
    {
        $token = Token::getByToken($token);

        if($token) {
            $user = Craft::$app->getUsers()->getUserById($token->userId);
            return $user;
        } else {
            return null;
        }
    }
}
