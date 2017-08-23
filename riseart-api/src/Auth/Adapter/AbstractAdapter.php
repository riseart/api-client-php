<?php
/**
 * AbstractAdapter.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api\Auth\Adapter {

    /**
     * Class AbstractAdapter
     * @package Riseart\Api\Auth\Adapter
     */
    abstract class AbstractAdapter
        implements InterfaceAdapter
    {
        /**
         * @var string $apiKey
         */
        protected $apiKey;

        /**
         * @var array $payload
         */
        protected $payload;

        /**
         * @param $apiKey
         * @return string
         */
        public function setApiKey($apiKey)
        {
            $this->apiKey = $apiKey;
        }

        /**
         * @return string
         */
        public function getApiKey()
        {
            return $this->apiKey;
        }

        /**
         * @return array
         */
        public function getPayload()
        {
            return $this->payload;
        }

    }

}