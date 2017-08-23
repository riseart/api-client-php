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
            $this->setVisitorId(Validator::validateRequiredParameter((isset($config['visitorId'])) ? $config['visitorId'] : null, 'VISITOR ID'));
        }

        /**
         * @return array
         */
        public function getPayload()
        {
            return [
                'api_key' => $this->getApiKey(),
                'auth_module' => self::AUTH_MODULE_NAME,
                'visitor_id' => $this->getVisitorId()
            ];
        }

        /**
         * @return int
         */
        public function getVisitorId()
        {
            return $this->visitorId;
        }

        /**
         * @param int $visitorId
         */
        public function setVisitorId($visitorId)
        {
            $this->visitorId = (int)$visitorId;
        }

    }

}