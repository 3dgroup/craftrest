<?php
/**
 * craft-rest module for Craft CMS 3.x
 *
 * REST API
 *
 * @link      https://github.com/3dgroup
 * @copyright Copyright (c) 2018 3D Group
 */

namespace threedgroup\craftrest;

use craft\commerce\elements\Variant;
use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;

use threedgroup\craftrest\components\UrlManagerREST;
use threedgroup\craftrest\components\UrlMangerREST;
use threedgroup\craftrest\controllers\CustomElementController;
use threedgroup\craftrest\events\SaveEvent;

use modules\craftrestmodule\assetbundles\craftrestmodule\CraftrestModuleAsset;

use Craft;
use craft\events\RegisterUrlRulesEvent;

use threedgroup\geekeyaftercare\elements\Question;
use threedgroup\geekeyaftercare\GeekeyAftercare;
use yii\base\Event;
use craft\base\Plugin AS BasePlugin;

/**
 * Class CraftrestModule
 *
 * @author    3D Group
 * @package   CraftrestModule
 * @since     1
 *
 */
class Plugin extends BasePlugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1';


    /**
     * @var CraftrestModule
     */
    public static $instance;


    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        //$this->controllerNamespace = 'craftrest\controllers';

        // Set this as the global instance of this module class
        //static::setInstance($this);


        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$instance = $this;

        /* Use SKU instead of Product ID */
        Event::on(
            CustomElementController::class,
            CustomElementController::EVENT_BEFORE_SAVE_ENTRY,
                    function(SaveEvent $event){
                        if($event->element instanceof Question) {

                            $request = Craft::$app->getRequest();

                            if ($request->getBodyParam('sku')) {
                                $variants = [];
                                foreach ($request->getBodyParam('sku') AS $sku) {
                                    $product = Variant::find()->sku($sku)->one();
                                    if ($product) {
                                        $variants[] = $product->id;
                                    }

                                }
                                $event->element->{GeekeyAftercare::FIELD_NAME_SKU} = $variants;
                            }
                        }
                    }
                );


        if(defined('REST')) {
            Event::on(
                UrlManagerREST::class,
                UrlManagerREST::EVENT_REGISTER_REST_URL_RULES,
                function (RegisterUrlRulesEvent $event) {
                    $entry = $this->settings->entryPath;
                    $event->rules[$entry . '/entry/<handle>'] = 'craft-rest/entry/index';
                    $event->rules[$entry . '/entry/<id\d+>'] = 'craft-rest/entry/view';
                    $event->rules[$entry . '/entry/create'] = 'craft-rest/entry/create';
                    $event->rules[$entry . '/<element>'] = 'craft-rest/custom-element/index';
                    $event->rules[$entry . '/<element>/<id\d+>'] = 'craft-rest/custom-element/view';
                    $event->rules[$entry . '/<element>/create'] = 'craft-rest/custom-element/create';
                }
            );
        }

        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS,
            function(RegisterUserPermissionsEvent $event) {
                if($this->settings->customElements){
                    foreach($this->settings->customElements AS $key => $elementType){
                        $elements['api-'.$key] = [
                            'label' => ucfirst($key),
                            'nested' => [
                                'api-'.$elementType['class'].'-index' => ['label' => 'List'],
                                'api-'.$elementType['class'].'-view' => ['label' => 'View'],
                                'api-'.$elementType['class'].'-create' => ['label' => 'Create'],
                                'api-'.$elementType['class'].'-update' => ['label' => 'Update'],
                            ]
                        ];
                    }

                    $sections = Craft::$app->sections->getAllSections();

                    foreach($sections AS $section){
                        $elements['api-sections-'.$section['handle']] = [
                            'label' => 'Section: '.ucwords($section['name']),
                            'nested' => [
                                'api-section-'.$section['handle'].'-index' => ['label' => 'List'],
                                'api-section-'.$section['handle'].'-view' => ['label' => 'View'],
                                'api-section-'.$section['handle'].'-create' => ['label' => 'Create'],
                                'api-section-'.$section['handle'].'-update' => ['label' => 'Update'],
                            ]
                        ];
                    }
                    $event->permissions['REST API'] = [
                        'CustomElements' => [
                            'label' => 'Custom Elements',
                            'nested' => $elements
                            ]
                    ];
                }
               // 'api-'.$model.'-'.$action
            }
        );


        /*
        Craft::info(
            Craft::t(
                'craft-rest',
                '{name} module loaded',
                ['name' => 'craft-rest']
            ),
            __METHOD__
        );*/
    }


    // Protected Methods
    // =========================================================================
    protected function createSettingsModel()
    {
        return new \threedgroup\craftrest\models\Settings();
    }
}
