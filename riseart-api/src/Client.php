<?php
/**
 * Client.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api {

    use Riseart\Api\Auth\Adapter\InterfaceAdapter;
    use Riseart\Api\Exception\RiseartException;
    use Riseart\Api\Token\RiseartToken;
    use Riseart\Api\Validator\Validator;
    use GuzzleHttp\Client as GuzzleClient;
    use GuzzleHttp\Exception\ClientException as GuzzleException;
    use Riseart\Api\Response\RiseartResponse;

    /**
     * Class Client
     * @package Riseart\Api
     */
    class Client
    {
        const STABLE_API_VERSION = 'v1';
        const API_HOST = 'https://api.riseart.local';

        const HTTP_METHOD_GET = 'GET';
        const HTTP_METHOD_POST = 'POST';
        const HTTP_METHOD_PUT = 'PUT';
        const HTTP_METHOD_DELETE = 'DELETE';

        /**
         * @var GuzzleClient
         */
        private $client;
        /**
         * @var string
         */
        private $host;
        /**
         * @var string
         */
        private $defaultVersion;

        /**
         * @var InterfaceAdapter
         */
        private $authAdapter;

        /**
         * @var RiseartToken
         */
        private $token = null;

        /**
         * @var array
         */
        private $defaultHeaders = [
            'User-Agent' => 'Rise Art PHP client',
            'Accept' => 'application/json',
            'Content-type' => 'application/json',
        ];

        /**
         * Client constructor.
         * @param array $config
         * @throws RiseartException
         */
        public function __construct(Array $config)
        {
            //  Validate the host
            (isset($config['host'])) ? $this->host = Validator::validateApiHost($config['host']) : $this->host = self::API_HOST;

            // Check if the version is valid
            (isset($config['defaultVersion'])) ? $this->defaultVersion = Validator::validateVersion($config['defaultVersion']) : $this->defaultVersion = self::STABLE_API_VERSION;

            // Check if there is an auth adapter then authenticate
            (isset($config['authAdapter']) && $config['authAdapter'] instanceof InterfaceAdapter) ? $this->setAuthAdapter($config['authAdapter']) : null;

            // Check if there is a valid token then validate it
            (isset($config['token']) && $config['token'] instanceof RiseartToken) ? $this->setToken($config['token']) : null;
        }

        /**
         * @param InterfaceAdapter $authAdapter
         * @return mixed|null|string
         */
        public function setAuthAdapter(InterfaceAdapter $authAdapter)
        {
            $this->authAdapter = $authAdapter;
            $this->client = $authAdapter->getHttpClient();
            $this->resetToken();
        }

        /**
         * @return InterfaceAdapter
         */
        public function getAuthAdapter()
        {
            return $this->authAdapter;
        }

        /**
         * @param $endpoint
         * @param null $resourceId
         * @param array $parameters
         * @param null $version
         * @return RiseartResponse
         * @throws RiseartException
         */
        public function GET($endpoint, $resourceId = null, $parameters = [], $version = null)
        {
            try {
                return new RiseartResponse($this->client->request(
                    self::HTTP_METHOD_GET,
                    $this->buildUrl($endpoint, $resourceId, $parameters, $version),
                    ['headers' => $this->getHeaders()]
                ));
            } catch (GuzzleException $e) {
                throw RiseartException::manageGuzzleException($e);
            } catch (RiseartException $e) {
                throw $e;
            } catch (\Exception $e) {
                throw RiseartException::manageGenericException($e);
            }
        }

        /**
         * @param $endpoint
         * @param $resourceId
         * @param $parameters
         * @param $version
         * @return string
         */
        private function buildUrl($endpoint, $resourceId, $parameters = [], $version)
        {
            Validator::validateEndpoint($endpoint);
            Validator::validateResourceId($resourceId);
            Validator::validateParameters($parameters);
            // Compare, validate and select the right version
            $version = '/' . Validator::validateVersion($this->defaultVersion, $version);
            // Url encode the resource id
            $resourceId = ($resourceId) ? '/' . urlencode($resourceId) : '';
            // Convert the param array to an url encoded query string
            $parameters = (count($parameters) > 0) ? '?' . http_build_query($parameters) : '';

            return $this->host . $version . $endpoint . $resourceId . $parameters;
        }

        /**
         * @return array
         */
        private function getHeaders()
        {
            $headers = $this->defaultHeaders;
            ($this->getToken()) ? $headers['Authorization'] = 'Bearer ' . $this->getToken()->validate() : null;
            return $headers;
        }

        /**
         * @return RiseartToken
         */
        public function getToken()
        {
            // Lazy load auth token
            if (!$this->token) {
                if ($this->authAdapter) {
                    $this->token = $this->authAdapter->authenticate();
                }
            }

            return $this->token;
        }

        /**
         * @param $token
         * @return string
         */
        public function setToken(RiseartToken $token)
        {
            $this->token = $token;
            return $this->token;
        }

        /**
         * resetToken
         */
        public function resetToken()
        {
            $this->token = null;
        }
    }
}