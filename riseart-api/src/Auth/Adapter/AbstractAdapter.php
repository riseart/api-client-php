<?php
/**
 * AbstractAdapter.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api\Auth\Adapter {

    use GuzzleHttp\Client as GuzzleClient;
    use GuzzleHttp\Exception\ClientException;
    use Riseart\Api\Token\RiseartToken;
    use Riseart\Api\Exception\RiseartException;

    /**
     * Class AbstractAdapter
     * @package Riseart\Api\Auth\Adapter
     */
    abstract class AbstractAdapter
        implements InterfaceAdapter
    {
        const AUTH_GATEWAY = 'https://api.riseart.com/auth';

        /**
         * @var string
         */
        protected $apiKey;

        /**
         * @var Client
         */
        protected $httpClient;

        /**
         * @var string
         */
        protected $authGateway;

        /**
         * AbstractAdapter constructor.
         * @param null $authGateway
         * @param bool $verifySSL
         */
        public function __construct($authGateway = null, $verifySSL = true)
        {
            ($authGateway) ? $this->authGateway = $authGateway : $this->authGateway = AbstractAdapter::AUTH_GATEWAY;
            $this->httpClient = new GuzzleClient(['verify' => $verifySSL]);
        }

        /**
         * @var array
         */
        protected $defaultHeaders = [
            'User-Agent' => 'Rise Art API - PHP client',
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ];

        /**
         * @return mixed
         * @throws RiseartException
         */
        public function authenticate()
        {
            try {
                $payload = $this->getPayload();
                $parameters = [
                    'headers' => $this->defaultHeaders,
                    'json' => $payload
                ];
                $response = $this->httpClient->post($this->authGateway, $parameters);
                $content = json_decode($response->getBody()->getContents());
                if ($content && isset($content->token)) {
                    return new RiseartToken($content->token);
                }
                throw RiseartException::manageFailedAuth($content, get_class($this));

            } catch (ClientException $e) {
                throw RiseartException::manageGuzzleException($e);
            } catch (\Exception $e) {
                throw RiseartException::manageGenericException($e);
            }
        }

        /**
         * @return string
         */
        public function getApiKey()
        {
            return $this->apiKey;
        }

        /**
         * @return string
         */
        public function getAuthGateway()
        {
            return $this->authGateway;
        }

        /**
         * @return GuzzleClient
         */
        public function getHttpClient()
        {
            return $this->httpClient;
        }
    }

}