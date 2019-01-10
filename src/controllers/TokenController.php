<?php
namespace threedgroup\craftrest\controllers;

use Craft;
use threedgroup\craftrest\models\Token;

class TokenController extends \craft\web\Controller
{
    public function actionCreate(int $userId){
        $this->requirePermission('createToken');

        if($userId) {
            $token = new Token();
            $token->userId = $userId;
            $token->token = \Yii::$app->security->generateRandomString(64);
            if($token->save()){
                Craft::$app->getSession()->setNotice(Craft::t('craft-rest', 'Created Token Successfully'));
            }
            $this->redirect('users/' . $userId);
        } else {
            $this->redirect('admin/users');
        }
    }

    public function actionDelete(int $id){
        $this->requirePermission('deleteToken');

        if($id) {
            $token = Token::findOne(['id'=>$id]);
            if($token) {
                if ($token->delete()) {
                    Craft::$app->getSession()->setNotice(Craft::t('craft-rest', 'Deleted Token Successfully'));
                }
            } else {
                Craft::$app->getSession()->setNotice(Craft::t('craft-rest', 'No token found'));
            }
            $this->redirect('users/' . $token->userId);
        } else {
            $this->redirect('admin/users');
        }
    }

}
