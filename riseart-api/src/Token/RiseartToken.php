<?php
/**
 * RiseartToken.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api\Token {

    use Riseart\Api\Exception\RiseartException;

    /**
     * Class RiseartToken
     * @package Riseart\Api\Token
     */
    class RiseartToken
    {
        /**
         * @var string
         */
        public $token;

        /**
         * @var array
         */
        public $payload;

        /**
         * RiseartToken constructor.
         * @param $token
         */
        public function __construct($token)
        {
            $this->setToken($token);
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return $this->getToken();
        }

        /**
         * @param null $token
         * @return string
         * @throws RiseartException
         */
        public function validate($token = null)
        {
            if (!$token) {
                $token = $this->getToken();
            }
            if (substr_count($token, '.') !== 2) {
                throw RiseartException::invalidJWTTokenFormat($token);
            }
            $tokenData = explode('.', $token);
            $payload = base64_decode($tokenData[1]);
            if (!$payload) {
                throw RiseartException::invalidJWTTokenPayload($payload);
            }
            $payloadObject = json_decode($payload);

            if (!isset($payloadObject) || !isset($payloadObject->acl_role) || !isset($payloadObject->exp) || !isset($payloadObject->application_name)) {
                throw RiseartException::invalidJWTTokenPayload($payload);
            }

            if($this->isExpired($payloadObject->exp)){
                throw RiseartException::JWTTokenWasExpired();
            }

            $this->payload = $payloadObject;
            return $token;
        }

        /**
         * @param int|null $expiration
         * @return bool
         */
        public function isExpired($expiration = null){
            $expiration = ($expiration) ? $expiration : $this->getExpiration();
            if ($expiration - time() < 0) {
                return true;
            }
            return false;
        }

        /**
         * @return string
         */
        public function getExpiration()
        {
            return $this->payload->exp;
        }

        /**
         * @return string
         */
        public function getToken()
        {
            return $this->token;
        }

        /**
         * @param string $token
         */
        public function setToken($token)
        {
            $this->token = $token;
            $this->token = $this->validate($token);
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
