# Craft REST API plugin for Craft CMS 3.x

* Add's Craft REST API
* Works with standard entries
* Config file gives ability to get custom elements
* Ability to add custom search within the prepareDataProvider option
* Removes some Craft specific endpoints that are not required (cp)
As
* Uses Yii2 REST features.


## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require threedgroup/craft-rest-api

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Craft REST API.

## Craft REST API Overview

Gives a headles entry to craft CMS

## Configuring Craft REST API

1. Create a new endpoint for craft by creating a new web directory called api. (In your route crate a new folder called api, that will sit next to the web folder). Then copy everything from the web folder into the api folder (cpresources not required).

2. inside the index.php file add the following lines under the existing constants:

``` php
define('REST', true);
```

Then in your app.php config file should look like below:

``` php
$config = [
    /** all exisintg config  EG: */
    'modules' => [
        'my-module' => \modules\Module::class,
    ]
];

if(!defined('REST')) {
    return $config;
}

return \craft\helpers\ArrayHelper::merge($config,
    require(CRAFT_VENDOR_PATH . '/threedgroup/craft-rest-api/src/config/rest.php'));
```


3. Create craft-rest-api.php settings file in your projects config folder

``` php
return [
    'sections' => [
        'testEntry' => [
            'fields' => [
                'id'
            ],
            'extraFields' => [
                'test'
            ],
            'prepareDataProvider' => function($model) {
                /** @var $query \craft\elements\Entry */
                $query = $model::find();

                return new \yii\data\ActiveDataProvider([
                    'query' => $query,
                ]);
            }
        ]
    ],
    'customElements' => [
        'question' => [
            'class' => \threedgroup\geekeyaftercare\elements\Question::class,
            /* fields removed the fields option is not used */
            'removeFields' => [
                '_statusData'
            ],
            /* Fields that will be displayed on the query */
            'fields' => [
                'id',
                'comment'=>function($model){
                    return (string) $model->comment;
                },
                'sku' => function($model){
                    $data=[];
                    foreach($model->skunew AS $variant){
                        $data[$variant->id] = $variant->sku;
                    }
                    return $data;
                }
            ],
            /* Required for saving data without these the custom fields will not be sent on the element */
            'customElementFields' => [
                'questionStatus' => 'questionStatus'
            ],
            /* Expandable fields for relations */
            'extraFields' => [
                'products'=> 'skunew'
            ],
            /* Ability to change the data provider of the query */
            'prepareDataProvider' => function($model) {
                /** @var $query Question */
                $query = $model::find();

                $productVariant = isset(\Yii::$app->request->queryParams['productVariant']) ? \Yii::$app->request->queryParams['productVariant'] : null;
                $searchString = isset(\Yii::$app->request->queryParams['search']) ? \Yii::$app->request->queryParams['search'] : null;

                if($productVariant) {
                    $query->productVariant($productVariant);
                }

                if($searchString) {
                    $query->search($searchString);
                }

                $query->with('SKUnew');

                return new \yii\data\ActiveDataProvider([
                    'query' => $query,
                ]);
            }
        ]
    ]
];
```

## Using Craft REST API

Use REST Application to test you data.

## Craft REST API Roadmap

Some things to do, and ideas for potential features:

* Add token model so that system can login via token in REST request.


Brought to you by [3D Group](https://github.com/3dgroup)
