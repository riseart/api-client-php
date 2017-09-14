<?php
/**
 * RiseartException.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api\Exception {

    use GuzzleHttp\Exception\BadResponseException;

    /**
     * Class RiseartException
     * @package Riseart\Api\Exception
     */
    class RiseartException extends \Exception
    {
        CONST ERROR_TAG = '[RISEART-API-EXCEPTION] ';

        /** //TODO: Description */
        const EXCEPTION_CODE_PHP_EXCEPTION = 1;
        /** //TODO: Description */
        const EXCEPTION_CODE_GUZZLE_EXCEPTION = 2;
        /** //TODO: Description */
        const EXCEPTION_CODE_MISSED_REQUIRED_PARAMETER = 3;
        /** //TODO: Description */
        const EXCEPTION_CODE_INVALID_ENDPOINT = 4;
        /** //TODO: Description */
        const EXCEPTION_CODE_INVALID_RESOURCE_ID = 5;
        /** //TODO: Description */
        const EXCEPTION_CODE_INVALID_PARAMETERS = 6;
        /** //TODO: Description */
        const EXCEPTION_CODE_INVALID_API_VERSION = 7;
        /** //TODO: Description */
        const EXCEPTION_CODE_INVALID_CLIENT_CONFIG = 8;
        /** //TODO: Description */
        const EXCEPTION_CODE_AUTHENTICATION_FAILED = 9;
        /** //TODO: Description */
        const EXCEPTION_CODE_JWT_TOKEN_EXPIRED = 10;
        /** //TODO: Description */
        const EXCEPTION_CODE_JWT_TOKEN_INVALID_FORMAT = 11;
        /** //TODO: Description */
        const EXCEPTION_CODE_JWT_TOKEN_INVALID_PAYLOAD = 12;
        /** //TODO: Description */
        const EXCEPTION_CODE_AUTH_MODULE_USER_CONSUMED = 13;
        /** //TODO: Description */
        const EXCEPTION_CODE_INVALID_HOST = 14;

        /**
         * @var int
         */
        public $httpStatusCode;
        /**
         * @var string
         */
        public $message;
        /**
         * @var mixed
         */
        public $rawError;

        /**
         * @return mixed
         */
        public function getRawError()
        {
            return $this->rawError;
        }

        /**
         * @return mixed
         */
        public function getHttpStatusCode()
        {
            return $this->httpStatusCode;
        }

        /**
         * @param mixed $httpStatusCode
         */
        public function setHttpStatusCode($httpStatusCode)
        {
            $this->httpStatusCode = $httpStatusCode;
        }

        /**
         * @param mixed $message
         */
        public function setMessage($message)
        {
            $this->message = $message;
        }

        /**
         * @param mixed $rawError
         */
        public function setRawError($rawError)
        {
            $this->rawError = $rawError;
        }

        /**
         * @param BadResponseException $clientException
         * @return RiseartException
         */
        public static function manageGuzzleException(BadResponseException $clientException)
        {
            // API response object
        	$response  = $clientException->getResponse();

        	// Build exception
            $exception = new self("", self::EXCEPTION_CODE_GUZZLE_EXCEPTION, $clientException);
            $exception->setHttpStatusCode($response->getStatusCode());

            // Exception response body
            $data = $response->getBody()->getContents();
            $data = json_decode($data);

            if ($data) {
				// Parse API error response body
				$message = [];
                if (isset($data->error)) {
					if(isset($data->error->type)) {
						$message[] = $data->error->type;
					}
					if(isset($data->error->title)) {
						$message[] = $data->error->title;
					}
					if(isset($data->error->detail)) {
						$message[] = $data->error->detail;
					}
				}

                if(count($message) == 0) {
					$message = "Unrecognized error type from API, no error information received";
				} else {
                	$message = implode(": ", $message);
				}

                $exception->setMessage(self::ERROR_TAG . $message);
                $exception->setRawError($data);
            } else {
                // API response is not a valid JSON
                $exception->setMessage(self::ERROR_TAG . 'Unrecognized error from API, no response body received');
                $exception->setRawError($response->getBody()->getContents());
            }

            return $exception;
        }

        /**
         * @param $contentResponse
         * @param $module
         * @return RiseartException
         */
        public static function manageFailedAuth($contentResponse, $module)
        {
            $exception = new self(
                self::ERROR_TAG . "The authentication with the $module was failed",
                self::ERROR_CODE_AUTHENTICATION_FAILED
            );
            $exception->rawError = $contentResponse;
            return $exception;
        }

        /**
         * @param \Exception $e
         * @return RiseartException
         */
        public static function manageGenericException(\Exception $e)
        {
            return new self(
                self::ERROR_TAG . $e->getMessage(),
                self::EXCEPTION_CODE_PHP_EXCEPTION
            );
        }

        /**
         * @param $parameter
         * @return RiseartException
         */
        public static function missedRequiredParameter($parameter)
        {
            return new self(
                self::ERROR_TAG . "$parameter is required and must be set",
                self::EXCEPTION_CODE_MISSED_REQUIRED_PARAMETER
            );
        }

        /**
         * @return RiseartException
         */
        public static function invalidEndpoint()
        {
            return new self(
                self::ERROR_TAG . "The endpoint needs to start with '/'",
                self::EXCEPTION_CODE_INVALID_ENDPOINT
            );
        }

        /**
         * @return RiseartException
         */
        public static function invalidResourceId()
        {
            return new self(
                self::ERROR_TAG . "The resource id must be a scalar value",
                self::EXCEPTION_CODE_INVALID_RESOURCE_ID
            );
        }

        /**
         * @param $value
         * @return RiseartException
         */
        public static function invalidParameters($value)
        {
            return new self(
                self::ERROR_TAG . "Parameters must be an array - current value is $value",
                self::EXCEPTION_CODE_INVALID_PARAMETERS
            );
        }

        /**
         * @return RiseartException
         */
        public static function JWTTokenWasExpired()
        {
            return new self(
                self::ERROR_TAG . "The provided token was expired",
                self::EXCEPTION_CODE_JWT_TOKEN_EXPIRED
            );
        }

        /**
         * @param $token
         * @return RiseartException
         */
        public static function invalidJWTTokenFormat($token)
        {
            return new self(
                self::ERROR_TAG . "The provided token: $token, is not a valid JWT string format",
                self::EXCEPTION_CODE_JWT_TOKEN_INVALID_FORMAT
            );
        }

        /**
         * @param $payload
         * @return RiseartException
         */
        public static function invalidJWTTokenPayload($payload)
        {
            return new self(
                self::ERROR_TAG . "The payload provided is not valid: " . json_encode($payload),
                self::EXCEPTION_CODE_JWT_TOKEN_INVALID_PAYLOAD
            );
        }

        /**
         * @param $version
         * @return RiseartException
         */
        public static function invalidVersion($version)
        {
            return new self(
                self::ERROR_TAG . "The version provided '$version' does't start with the character 'v'",
                self::EXCEPTION_CODE_INVALID_API_VERSION
            );
        }

        /**
         * @return RiseartException
         */
        public static function userAuthAdapterAlreadyUsed()
        {
            return new self(
                self::ERROR_TAG . "The user adapter was already used, you need to provide the user password again",
                self::EXCEPTION_CODE_AUTH_MODULE_USER_CONSUMED
            );
        }

        /**
         * @param array $config
         * @return RiseartException
         */
        public static function invalidClientConfig(array $config)
        {
            return new self(
                self::ERROR_TAG . "The config file passed to the client is not valid - The provided config (json formatted) is: " . json_encode($config),
                self::EXCEPTION_CODE_INVALID_CLIENT_CONFIG
            );
        }

        /**
         * @param $host
         * @return RiseartException
         */
        public static function invalidApiHost($host)
        {
            return new self(
                self::ERROR_TAG . "The host provided is not a valid API address -".$host,
                self::EXCEPTION_CODE_INVALID_HOST
            );
        }

    }

}