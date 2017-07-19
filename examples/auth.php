<?php;
$loader = require dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';

use Riseart\Api\Client as RiseartClient;
use Riseart\Api\Auth\Adapter\Application as AuthModuleApplication;
use Riseart\Api\Auth\Adapter\Visitor as AuthModuleVisitor;
use Riseart\Api\Auth\Adapter\User as AuthModuleUser;

const API_KEY = '';
const VISITOR_ID = '';
const USERNAME = '';
const PASSWORD = '';

try {
    // Rise Art API supports 3 authentication levels

    // 1: Application - Is used for a specific set of server side operations
    $application = new AuthModuleApplication(['apiKey' => API_KEY]);

    // 2: Visitor - allows to manage public endpoints
    $visitor = new AuthModuleVisitor(['apiKey' => API_KEY, 'visitorId' => VISITOR_ID]);

    // 3: User - allows to manage a specific user account
    $user = new AuthModuleUser(['apiKey' => API_KEY, 'username' => USERNAME, 'password' => PASSWORD]);

    // You can instantiate the client with a specific auth module
    // or pass it through the method setAuthAdapter()
    $clientConfig = [
        'authAdapter' => $application
    ];
    $riseartClient = new RiseartClient($clientConfig);
    var_dump($riseartClient->getToken());

    $riseartClient->setAuthAdapter($visitor);
    var_dump($riseartClient->getToken());

    // N.B. all the endpoints require to have a valid auth token
    // N.B  In order to call an endpoint, you need to perform the request over the HTTPS protocol

    // Here an example of user authentication and retrieving data of that user.
    $riseartClient->setAuthAdapter($user);
    $response = $riseartClient->GET('/me');
    var_dump($response);

} catch (\Error $e) {
    var_dump($e->getMessage());
}
