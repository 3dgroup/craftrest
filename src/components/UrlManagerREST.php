<?php
namespace threedgroup\craftrest\components;

use Craft;
use craft\events\RegisterUrlRulesEvent;
/**
 * @inheritdoc
 *
 * @author Flipbox Factory <hello@flipboxfactory.com>
 * @since 1.0.0
 */

class UrlManagerREST extends \yii\web\UrlManager
{
    /**
     * The event name when registering RESTful Url Rules
     */
    const EVENT_REGISTER_REST_URL_RULES = 'registerRestUrlRules';
    /**
     * @var array the default configuration of URL rules. Individual rule configurations
     * specified via [[rules]] will take precedence when the same property of the rule is configured.
     */
    public $ruleConfig = ['class' => UrlRule::class];
    /**
     * @inheritdoc
     */
    public function __construct(array $config = [])
    {
        $config['rules'] = $this->getRules();
        parent::__construct($config);
    }
    /**
     * @return array
     */
    private function getRules()
    {
        $event = new RegisterUrlRulesEvent();
        $this->trigger(
            self::EVENT_REGISTER_REST_URL_RULES,
            $event
        );

        return array_filter($event->rules);
    }
    /**
     * @inheritdoc
     */
    public function parseRequest($request)
    {
        if (!$result = parent::parseRequest($request)) {
            Craft::warning("Unable to parse request: " . $request->getUrl());
        }

        return $result;
    }
}
