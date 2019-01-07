<?php
/**
 * craft-rest module for Craft CMS 3.x
 *
 * REST API
 *
 * @link      https://github.com/3dgroup
 * @copyright Copyright (c) 2018 3D Group
 */

namespace threedgroup\craftrest\controllers;

use craftrest\components\CustomeFieldsBehaviour;
use craftrest\events\SaveEvent;

use Craft;
use threedgroup\craftrest\Plugin;
use threedgroup\geekeyaftercare\elements\Question;

use yii\base\ErrorException;

/**
 * @author    3D Group
 * @package   Module
 * @since     1
 */
class CustomElementController extends Controller
{


    /**
     * @event SaveEvent The event that is triggered before a guest entry is saved.
     */
    const EVENT_BEFORE_SAVE_ENTRY = 'beforeSaveEntry';

    // Protected Properties
    // =========================================================================
    /**
     * @var class $model Inline class saved here and used within the controller
     */
    protected $model;
    protected $config;


    // Public Methods
    // =========================================================================

    /**
     * @var string
     */
    public $modelClass;


    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $module = Plugin::getInstance();
        $configId = Craft::$app->request->getSegment(2);

        $this->config = isset($module->settings->customElements[$configId]) ? $module->settings->customElements[$configId] : [];
        $this->modelClass = isset($this->config['class']) ? $this->config['class'] : null;
        $this->accessName =  $this->modelClass;


        if(!$this->modelClass || !class_exists($this->modelClass)){
            throw new ErrorException('Incorrect config, REST class not found');
        }

        /* Create Dynamic Class to Play with the Fields */
        class_alias($this->modelClass, 'threedgroup\craftrest\controllers\parentClass');

        $this->model = new class() extends parentClass {

            private $_fields;
            private $_extraFields;
            private $_removeFields;

            public function init(){
                $module = Plugin::getInstance();
                $configId = Craft::$app->request->getSegment(2);


                $config = isset($module->settings->customElements[$configId]) ? $module->settings->customElements[$configId] : [];

                $this->_fields = isset($config['fields']) ? $config['fields'] : [];
                $this->_extraFields = isset($config['extraFields']) ? $config['extraFields'] : [];
                $this->_removeFields = isset($config['removeFields']) ? $config['removeFields'] : [];


                return parent::init();
            }

            public function extraFields(){
                return array_merge(parent::extraFields(),$this->_extraFields);
            }

            public function fields(){
                /* Return defined fields */
                if(isset($this->_fields)){
                    return $this->_fields;
                }

                $fields = parent::fields();

                if($this->_removeFields){
                // remove fields that contain sensitive information
                    foreach($this->_removeFields AS $key) {
                        unset($fields[$key]);
                    }
                }
                /* Return Standard Fields Removing Unset Fields */
                return $fields;
            }
        };


        parent::init();
    }

    /**
     * @return array
     */
    public function actions()
    {
        $actions = parent::actions();

        unset($actions['view']);
        unset($actions['create']);

        if(isset($this->config['prepareDataProvider'])){
            $actions['index']['prepareDataProvider'] = function() {
                return $this->config['prepareDataProvider']($this->model);
            };
        };

        return $actions;

    }

    public function actionView($id)
    {
        $this->checkAccess('view',$this->accessName);

        return $this->model::findOne($id);
    }

    public function actionCreate()
    {
        $this->checkAccess('create',$this->accessName);
        $request = Craft::$app->getRequest();

        /** @var Question $element */
        $element = $this->model;
        $element->setFieldValuesFromRequest('fields');

        /* get custom fields that maybe set inside the custom element */
        if(isset($this->config['customElementFields'])){
            foreach($this->config['customElementFields'] AS $key => $value){
                $element->{$key} = $request->getBodyParam($value);
            }
        }

        $event = new SaveEvent(['element' => $element, 'config'=>$this->config]);

        $this->trigger(self::EVENT_BEFORE_SAVE_ENTRY, $event);

        if(!Craft::$app->getElements()->saveElement($element)){
            return [
                'success' => false,
                'errors' => $element->getErrors(),
            ];
        } else {
            return $element;
        }
    }

}
