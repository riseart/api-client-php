<?php
/**
 * RiseartResponse.php - created 18/07/2017
 *
 * @author diego
 */

namespace Riseart\Api\Response {

    use GuzzleHttp\Psr7\Response;

    /**
     * Class RiseartResponse
     * @package Riseart\Api\Response
     */
    class RiseartResponse
    {
        //TODO:  This class is just a wrapper we need to improve this part

        protected $response;

        /**
         * RiseartResponse constructor.
         * @param Response $response
         */
        public function __construct(Response $response)
        {
            $this->response = $response;
        }

        /**
         * @param $name
         * @param $arguments
         * @return mixed
         */
        public function __call($name, $arguments)
        {
            return call_user_func_array(array($this->response, $name), $arguments);
        }

    }
}

