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

use craft\elements\Entry;

use Craft;
use threedgroup\craftrest\Plugin;
use threedgroup\geekeyaftercare\elements\Question;
use threedgroup\geekeyaftercare\GeekeyAftercare;


/**
 * @author    3D Group
 * @package   Module
 * @since     1
 */
class EntryController extends Controller
{

    // Protected Properties
    // =========================================================================

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     *         The actions must be in 'kebab-case'
     * @access protected
     */
    protected $allowAnonymous = ['index', 'do-something', '*'];
    private $_sectionhandleId;

    public $modelClass = Entry::class;
    public $model;
    // Public Methods
    // =========================================================================

    public function init()
    {
        $this->accessName = 'section-' .  Craft::$app->request->getSegment(3);
        $this->_sectionhandleId = Craft::$app->sections->getSectionByHandle(Craft::$app->request->getSegment(3));

        /* Create Dynamic Class to Play with the Fields */
        class_alias($this->modelClass, 'threedgroup\craftrest\controllers\parentClass');

        $this->model = new class() extends parentClass {
            private $_fields;
            private $_extraFields;
            private $_removeFields;
            private $_sectionhandleId;

            public function init(){
                $plugin = Plugin::getInstance();
                $this->_sectionhandleId = Craft::$app->request->getSegment(3);

                $config = isset($plugin->settings->sections[$this->_sectionhandleId]) ? $plugin->settings->sections[$this->_sectionhandleId] : [];

                $this->_fields = isset($config['fields']) ? $config['fields'] : [];
                $this->_extraFields = isset($config['extraFields']) ? $config['extraFields'] : null;
                $this->_removeFields = isset($config['removeFields']) ? $config['removeFields'] : null;

            }
            public function extraFields(){
                return array_merge(parent::extraFields(),$this->_extraFields);
            }
            public function fields(){
                /* Return defined fields */
                if($this->_fields){
                    return $this->_fields;
                }

                //$fields = parent::fields();


                //var_dump(array_keys($fields));

                $fields = [
                    'id',
                    'tempId',
                    'title',
                    'postDate',
                    'expiryDate',
                    'dateCreated',
                    'dateUpdated',
                    'enabled',
                    'sectionId',
                    'typeId',
                    'authorId',
                    'newParentId',
                    'revisionCreatorId',
                    'revisionNotes'
                ];


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

        $actions['index']['prepareDataProvider'] = function() {
            /** @var $query \craft\elements\Entry */
            $query = $this->model::find()->andWhere(['sectionId'=>$this->_sectionhandleId->id]);

            return new \yii\data\ActiveDataProvider([
                'query' => $query,
            ]);
        };

        return $actions;

    }

    public function actionView($id)
    {
        $this->checkAccess('view',$this->accessName);

        return $this->model::findOne($id);
    }

}
