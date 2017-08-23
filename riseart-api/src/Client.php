<?php
/**
 * Client.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api {

    use GuzzleHttp\Exception\ClientException;
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
        const API_HOST = 'https://api.riseart.com';
        const AUTH_GATEWAY = 'https://api.riseart.com/auth';
        const SSL_VERIFY_DEFAULT = true;

        const HTTP_METHOD_GET = 'GET';
        const HTTP_METHOD_POST = 'POST';
        const HTTP_METHOD_PUT = 'PUT';
        const HTTP_METHOD_DELETE = 'DELETE';

        /**
         * @var GuzzleClient
         */
        private $client = null;

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
         * @var boolean
         */
        private $verifySSL;

        /**
         * @var string
         */
        private $authGateway;

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

            // Checks if there is a valid token then validates and stores it
            (isset($config['token']) && $config['token'] instanceof RiseartToken) ?
                $this->setToken($config['token']) :
                null;

            // Checks if there is an auth adapter then stores it
            (isset($config['authAdapter']) && $config['authAdapter'] instanceof InterfaceAdapter) ?
                $this->setAuthAdapter($config['authAdapter']) :
                null;

            // Checks if there is a verifySSL option
            (isset($config['verifySSL'])) ?
                $this->setVerifySSL($config['verifySSL']) :
                $this->setVerifySSL(self::SSL_VERIFY_DEFAULT);

            // Checks if there is a different authGateway
            (isset($config['authGateway'])) ?
                $this->setAuthGateway($config['authGateway']) :
                $this->setAuthGateway(self::AUTH_GATEWAY);
        }

        /**
         * @return boolean
         */
        public function getVerifySSL()
        {
            return $this->verifySSL;
        }

        /**
         * @param boolean $verifySSL
         */
        public function setVerifySSL($verifySSL)
        {
            $this->verifySSL = $verifySSL;
        }

        /**
         * @return mixed
         */
        public function getAuthGateway()
        {
            return $this->authGateway;
        }

        /**
         * @param mixed $authGateway
         */
        public function setAuthGateway($authGateway)
        {
            $this->authGateway = $authGateway;
        }

        /**
         * @return GuzzleClient
         */
        protected function getClient()
        {
            if ($this->client) {
                return $this->client;
            }
            $this->client = new GuzzleClient([
                'verify' => $this->verifySSL
            ]);
            return $this->client;
        }

        /**
         * @return RiseartToken
         */
        public function getToken()
        {
            // Lazy load auth token
            if (!$this->token) {
                if ($this->authAdapter) {
                    $this->token = $this->authenticate();
                }
            }

            return $this->token;
        }

        /**
         * @return RiseartToken
         * @throws RiseartException
         */
        public function authenticate()
        {
            try {
                $payload = $this->authAdapter->getPayload();
                $parameters = [
                    'headers' => $this->defaultHeaders,
                    'json' => $payload
                ];
                $response = $this->getClient()->post($this->getAuthGateway(), $parameters);

                $content = json_decode($response->getBody()->getContents());
                if ($content && isset($content->token)) {
                    return new RiseartToken($content->token);
                }

                throw RiseartException::manageFailedAuth($content, get_class($this->getAuthAdapter()));

            } catch (ClientException $e) {
                throw RiseartException::manageGuzzleException($e);
            } catch (\Exception $e) {
                throw RiseartException::manageGenericException($e);
            }
        }

        /**
         * @param string $endpoint
         * @param mixed $resourceId
         * @param array $parameters
         * @param string $version
         * @return RiseartResponse
         * @throws RiseartException
         */
        public function GET($endpoint, $resourceId = null, $parameters = [], $version = null)
        {
            try {
                $parameters = Validator::validateRequestParameters($parameters);
                $url = $this->buildUrl($endpoint, $resourceId, $version);
                $response = $this->getClient()->request(
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
            try {
                $parameters = Validator::validateRequestParameters($parameters);
                $url = $this->buildUrl($endpoint, null, $version);

                return new RiseartResponse($this->getClient()->request(
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
         * @param $endpoint
         * @param $resourceId
         * @param $parameters
         * @param null $version
         * @return RiseartResponse
         * @throws RiseartException
         */
        public function PUT($endpoint, $resourceId, $parameters, $version = null)
        {
            try {
                $parameters = Validator::validateRequestParameters($parameters);
                $url = $this->buildUrl($endpoint, $resourceId, $version);

                return new RiseartResponse($this->getClient()->request(
                    self::HTTP_METHOD_PUT,
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
         * @return Client
         */
        public function setAuthAdapter(InterfaceAdapter $authAdapter)
        {
            $this->authAdapter = $authAdapter;
            $this->resetToken();

            return $this;
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
            // Get and validate token
            $token = $this->getToken();
            if ($token && $token->isExpired()) {
                throw RiseartException::JWTTokenWasExpired();
            }

            // Default headers
            $headers = $this->defaultHeaders;

            // Authorization
            ($token) ? $headers['Authorization'] = 'Bearer ' . $token : null;

            return $headers;
        }

        /**
         * @param $token
         * @return Client
         */
        public function setToken(RiseartToken $token)
        {
            $this->token = $token;

            return $this;
        }

        /**
         * resetToken
         * @return Client
         */
        public function resetToken()
        {
            $this->token = null;

            return $this;
        }

    }

}