<?php
/**
 * InterfaceAdapter.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api\Auth\Adapter {

    use Riseart\Api\Token\RiseartToken;

    /**
     * Interface InterfaceAdapter
     * @package Riseart\Api\Auth\Adapter
     */
    interface InterfaceAdapter
    {
        /**
         * @return RiseartToken
         */
        public function authenticate();

        /**
         * @return array
         */
        public function getPayload(): array;

        /**
         * @return string
         */
        public function getApiKey(): string;

        /**
         * @return string
         */
        public function getAuthGateway();
    }

}
