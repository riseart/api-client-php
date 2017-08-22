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
            $this->username = Validator::validateRequiredParameter((isset($config['username'])) ? $config['username'] : null, 'USERNAME');
            $this->password = Validator::validateRequiredParameter((isset($config['password'])) ? $config['password'] : null, 'PASSWORD');
            $verifySSL = (isset($config['verifySSL'])) ? $config['verifySSL'] : true;
            $authGateway = (isset($config['authGateway'])) ? $config['authGateway'] : self::AUTH_GATEWAY;

            parent::__construct($authGateway, $verifySSL);
        }

        /**
         * @return array
         */
        public function getPayload(): array
        {

            $payLoad = [
                'api_key' => $this->apiKey,
                'auth_module' => self::AUTH_MODULE_NAME,
                'username' => $this->username,
                'password' => $this->password
            ];
            $this->password = '';

            return $payLoad;
        }

        /**
         * @param $username
         */
        public function setUsername($username)
        {
            $this->username = $username;
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