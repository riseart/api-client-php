<?php
/**
 * Application.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api\Auth\Adapter {

    use Riseart\Api\Validator\Validator;

    /**
     * Class Visitor
     * @property string apiKey
     * @package Riseart\Api\Auth\Adapter
     */
    class Application
        extends AbstractAdapter
    {
        const AUTH_MODULE_NAME = 'application';

        /**
         * Application constructor.
         * @param array $config
         */
        public function __construct(array $config)
        {
            $this->setApiKey(Validator::validateRequiredParameter((isset($config['apiKey'])) ? $config['apiKey'] : null, 'API KEY'));
        }

        /**
         * @return array
         */
        public function getPayload()
        {
            return [
                'api_key' => $this->getApiKey(),
                'auth_module' => self::AUTH_MODULE_NAME,
            ];
        }
    }

}