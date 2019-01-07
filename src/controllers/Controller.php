<?php
/**
 * Created by PhpStorm.
 * User: dean.sanderson
 * Date: 2018-12-13
 * Time: 15:06
 */

namespace threedgroup\craftrest\controllers;

use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\rest\ActiveController;
use yii\web\Response;
/**
 * Controller is the base class for RESTful API controller classes.
 *
 * Controller implements the following steps in a RESTful API request handling cycle:
 *
 * 1. Resolving response format (see [[ContentNegotiator]]);
 * 2. Validating request method (see [[verbs()]]).
 * 3. Authenticating user (see [[\yii\filters\auth\AuthInterface]]);
 * 4. Rate limiting (see [[RateLimiter]]);
 *
 */

class Controller extends ActiveController
{
    /**
     * @var the model name used for the permissons
     */
    public $accessName;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    //'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'list'=>['GET']
                ],
            ],
            'authenticator' => [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    HttpBearerAuth::className()
                ]
            ],
            'rateLimiter' => [
                'class' => RateLimiter::class,
            ]
        ];
    }

    public function actions()
    {
        $actions = parent::actions();

        $actions['index']['checkAccess'] = function($action) {
            return $this->checkAccess($action,$this->accessName);
        };

        return $actions;
    }

    public function checkAccess($action, $handle = null, $params = [])
    {
        if(!\Craft::$app->user->checkPermission('api-'.$handle.'-'.$action)){
            throw new \yii\web\ForbiddenHttpException('You do not have the permission to proform this action');
        }
    }


}
