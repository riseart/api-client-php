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
    class ApplicationUser
        extends AbstractAdapter
    {
        /**
         *
         */
        const AUTH_MODULE_NAME = 'application/user';

        /**
         * @var int
         */
        protected $userId;

        /**
         * @var string|null
         */
        protected $aclRole = null;

        /**
         * ApplicationUser constructor.
         * @param array $config
         */
        public function __construct(array $config)
        {
            $this->setApiKey(Validator::validateRequiredParameter((isset($config['apiKey'])) ? $config['apiKey'] : null, 'API KEY'));
            $this->setUserId(Validator::validateRequiredParameter((isset($config['userId'])) ? $config['userId'] : null, 'USER ID'));
            $this->setAclRole(isset($config['aclRole'])) ? $config['aclRole'] : null;

            $verifySSL = (isset($config['verifySSL'])) ? $config['verifySSL'] : true;
            $authGateway = (isset($config['authGateway'])) ? $config['authGateway'] : self::AUTH_GATEWAY;

            parent::__construct($authGateway, $verifySSL);
        }

        /**
         * @return array
         */
        public function getPayload(): array
        {
            $payload = [
                'api_key' => $this->apiKey,
                'auth_module' => self::AUTH_MODULE_NAME,
                'user_id' => $this->getUserId()
            ];
            if ($this->getAclRole()) {
                $payload['acl_role'] = $this->getAclRole();
            }
            return $payload;
        }

        /**
         * @return int
         */
        public function getUserId()
        {
            return $this->userId;
        }

        /**
         * @param int $userId
         */
        protected function setUserId(int $userId)
        {
            $this->userId = $userId;
        }

        /**
         * @return string
         */
        public function getAclRole()
        {
            return $this->aclRole;
        }

        /**
         * @param string $aclRole
         * @return string
         */
        protected function setAclRole(string $aclRole)
        {
            $this->aclRole = $aclRole;
        }

    }

}