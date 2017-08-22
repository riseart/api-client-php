<?php
/**
 * Visitor.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api\Auth\Adapter {

    use Riseart\Api\Validator\Validator;

    /**
     * Class Visitor
     * @package Riseart\Api\Auth\Adapter
     */
    class Visitor extends AbstractAdapter
    {
        const AUTH_MODULE_NAME = 'website/visitor';

        /**
         * @var int
         */
        protected $visitorId;

        /**
         * Visitor constructor.
         * @param array $config
         */
        public function __construct(array $config)
        {
            $this->setApiKey(Validator::validateRequiredParameter((isset($config['apiKey'])) ? $config['apiKey'] : null, 'API KEY'));
            $this->visitorId = Validator::validateRequiredParameter((isset($config['visitorId'])) ? $config['visitorId'] : null, 'VISITOR ID');
            $verifySSL = (isset($config['verifySSL'])) ? $config['verifySSL'] : true;
            $authGateway = (isset($config['authGateway'])) ? $config['authGateway'] : self::AUTH_GATEWAY;

            parent::__construct($authGateway, $verifySSL);
        }

        /**
         * @return array
         */
        public function getPayload(): array
        {
            return [
                'api_key' => $this->apiKey,
                'auth_module' => self::AUTH_MODULE_NAME,
                'visitor_id' => $this->visitorId
            ];
        }
    }
}