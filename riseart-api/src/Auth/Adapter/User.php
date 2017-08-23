<?php
/**
 * User.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api\Auth\Adapter {

    use Riseart\Api\Validator\Validator;

    /**
     * Class Visitor
     * @package Riseart\Api\Auth\Adapter
     */
    class User extends AbstractAdapter
    {
        const AUTH_MODULE_NAME = 'website/user';

        /**
         * @var string
         */
        protected $username;

        /**
         * @var string
         */
        protected $password;

        /**
         * User constructor.
         * @param array $config
         */
        public function __construct(array $config)
        {
            $this->setApiKey(Validator::validateRequiredParameter((isset($config['apiKey'])) ? $config['apiKey'] : null, 'API KEY'));
            $this->setUsername(Validator::validateRequiredParameter((isset($config['username'])) ? $config['username'] : null, 'USERNAME'));
            $this->setPassword(Validator::validateRequiredParameter((isset($config['password'])) ? $config['password'] : null, 'PASSWORD'));
        }

        /**
         * @return array
         */
        public function getPayload()
        {
            $payLoad = [
                'api_key' => $this->apiKey,
                'auth_module' => self::AUTH_MODULE_NAME,
                'username' => $this->getUsername(),
                'password' => $this->getPassword()
            ];

            // Remove password after payload request
            $this->setPassword(null);

            return $payLoad;
        }

        /**
         * @return string
         */
        public function getUsername()
        {
            return $this->username;
        }

        /**
         * @param $username
         */
        public function setUsername($username)
        {
            $this->username = $username;
        }

        /**
         * @return string
         */
        public function getPassword()
        {
            return $this->password;
        }

        /**
         * @param $password
         */
        public function setPassword($password)
        {
            $this->password = $password;
        }

    }

}