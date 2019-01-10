<?php
namespace threedgroup\craftrest\models;

use Craft;
use craft\db\ActiveRecord;
use craft\records\User;

class Token extends ActiveRecord
{
    public static function getByToken($tokenId=false){
        if ($tokenId) {
            return Token::find()->where(['token' => $tokenId])->one();
        }
        return false;
    }
    public static function getByUserId($userId=false){
        if ($userId) {
            return Token::find()->where(['userId' => $userId])->all();
        }
        return false;
    }

    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    /**
     * @return string The associated database table name
     */
    public static function tableName(): string
    {
        return '{{%craftrestapi_token}}';
    }

}
