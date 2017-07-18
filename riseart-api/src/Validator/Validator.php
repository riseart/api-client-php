<?php
/**
 * Validator.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api\Validator {

    use Riseart\Api\Exception\RiseartException;

    /**
     * Class Validator
     * @package Riseart\Api\Validator
     */
    class Validator
    {
        /**
         * @param $version
         * @param null $currentVersion
         * @return bool|null
         * @throws RiseartException
         */
        static public function validateVersion($version, $currentVersion = null)
        {
            if ($version) {
                $version = strtolower($version);
                if (substr($version, 0, 1) === "v") {
                    return $version;
                }
                throw RiseartException::invalidVersion($version);
            }
            if ($currentVersion) {
                return $currentVersion;
            } else {
                throw RiseartException::invalidVersion($version);
            }
        }

        /**
         * @param $endpoint
         * @return bool
         * @throws RiseartException
         */
        static public function validateEndpoint($endpoint)
        {
            self::validateRequiredParameter($endpoint, "ENDPOINT");

            if (substr($endpoint, 0, 1) === "/") {
                return true;
            } else {
                throw RiseartException::invalidEndpoint();
            }

        }

        /**
         * @param $resourceId
         * @return bool
         * @throws RiseartException
         */
        public static function validateResourceId($resourceId)
        {
            // Resource ID can be null or a string
            if($resourceId === null){
                return '';
            }
            if (!is_string($resourceId)) {
                throw RiseartException::invalidResourceId();
            }
        }

        /**
         * @param $parameter
         * @param $parameterName
         * @return mixed
         * @throws RiseartException
         */
        static function validateRequiredParameter($parameter, $parameterName)
        {
            if (!$parameter || empty($parameter)) {
                throw RiseartException::missedRequiredParameter($parameterName);
            }
            return $parameter;
        }

        /**
         * @param $parameters
         * @return bool
         * @throws RiseartException
         */
        public static function validateRequestParameters($parameters)
        {
            // Parameters can be null or an array
            if (!is_array($parameters)) {
                throw RiseartException::invalidParameters($parameters);
            }
            return $parameters;
        }

        /**
         * @param $host
         * @return mixed
         * @throws RiseartException
         */
        public static function validateApiHost($host)
        {
            // TODO: Validate host URI
            return $host;
        }
    }

}