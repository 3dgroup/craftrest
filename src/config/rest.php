<?php

return [
    'components' => [
        'user' => function() {
            $stateKeyPrefix = md5('Craft.'.\craft\web\User::class.'.'.Craft::$app->id);
            return Craft::createObject([
                'class' => \craft\web\User::class,
                'identityClass' => \threedgroup\craftrest\components\restUserIdentity::class,
                'enableAutoLogin' => false,
                'autoRenewCookie' => false,
                'enableSession' => false,
                'identityCookie' => Craft::cookieConfig(['name' => $stateKeyPrefix.'_identity']),
                'usernameCookie' => Craft::cookieConfig(['name' => $stateKeyPrefix.'_username']),
                'loginUrl' => null,
                'idParam' => $stateKeyPrefix.'__id',
                'authTimeoutParam' => $stateKeyPrefix.'__expire',
                'absoluteAuthTimeoutParam' => $stateKeyPrefix.'__absoluteExpire',
                'returnUrlParam' => $stateKeyPrefix.'__returnUrl'
            ]);
        },
        'urlManager' => function() {
                $generalConfig = Craft::$app->getConfig()->getGeneral();
                return Craft::createObject([
                    'class'=> \threedgroup\craftrest\components\UrlManagerREST::class,
                    'routeParam' => $generalConfig->pathParam,
                    'enablePrettyUrl' => true,
                    'enableStrictParsing' => true,
                    'showScriptName' => false
                ]);
        },
        'request' => function() {
                $generalConfig = Craft::$app->getConfig()->getGeneral();
                return Craft::createObject([
                    'class'=> \threedgroup\craftrest\components\Request::class,
                    'enableCookieValidation' => true,
                    'cookieValidationKey' => $generalConfig->securityKey,
                    'enableCsrfValidation' => false,
                    'parsers' => [
                        'application/json' => \threedgroup\craftrest\components\JsonParser::class
                    ]
                ]);
        },

        'errorHandler' => [
            'class' => yii\web\ErrorHandler::class,
        ]
    ],
    'modules' => [
        'debug' => null
    ],
    'bootstrap' => []
];
