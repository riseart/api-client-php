<?php
/**
 * Client.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api {

    use PHPUnit\Runner\Exception;
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
        public function __construct(Array $config = null)
        {
            // Validates the host
            (isset($config['host'])) ?
                $this->host = Validator::validateApiHost($config['host']) :
                $this->host = self::API_HOST;

            // Checks if the version is valid
            (isset($config['defaultVersion'])) ?
                $this->defaultVersion = Validator::validateVersion($config['defaultVersion']) :
                $this->defaultVersion = self::STABLE_API_VERSION;

            // Checks if there is an auth adapter then stores it
            (isset($config['authAdapter']) && $config['authAdapter'] instanceof InterfaceAdapter) ?
                $this->setAuthAdapter($config['authAdapter']) :
                null;

            // Checks if there is a valid token then validates and stores it
            (isset($config['token']) && $config['token'] instanceof RiseartToken) ?
                $this->setToken($config['token']) :
                null;
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
                $parameters = Validator::validateRequestParameters($parameters);
                $url = $this->buildUrl($endpoint, $resourceId, $version);
                $response = $this->client->request(
                    self::HTTP_METHOD_GET,
                    $url,
                    [
                        'headers' => $this->getHeaders(),
                        'query' => $parameters,
                    ]
                );

                return new RiseartResponse($response);
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
         * @param $parameters
         * @param null $version
         * @return RiseartResponse
         * @throws RiseartException
         */
        public function POST($endpoint, $parameters, $version = null)
        {
            try{
                $parameters = Validator::validateRequestParameters($parameters);
                $url = $this->buildUrl($endpoint, null, $version);
                return new RiseartResponse($this->client->request(
                    self::HTTP_METHOD_POST,
                    $url,
                    [
                        'headers' => $this->getHeaders(),
                        'json' => $parameters,
                    ]
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
         * @param $resourceId
         * @param $parameters
         * @param $version
         * @return string
         */
        private function buildUrl($endpoint, $resourceId = null, $version)
        {
            Validator::validateEndpoint($endpoint);
            Validator::validateResourceId($resourceId);

            // Compare, validate and select the right version
            $version = '/' . Validator::validateVersion($this->defaultVersion, $version);

            // Url encode the resource id
            $resourceId = ($resourceId) ? '/' . urlencode($resourceId) : '';

            return $this->host . $version . $endpoint . $resourceId;
        }

        /**
         * @return array
         * @throws RiseartException
         */
        private function getHeaders()
        {
            if ($this->getToken()->isExpired()) {
                throw RiseartException::JWTTokenWasExpired();
            }
            $headers = $this->defaultHeaders;
            ($this->getToken()) ? $headers['Authorization'] = 'Bearer ' . $this->getToken() : null;
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