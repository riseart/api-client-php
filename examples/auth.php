<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$loader = require '../vendor/autoload.php';

use Riseart\Api\Client as RiseartClient;
use \Riseart\Api\Auth\Adapter\Application as AuthModuleApplication;
use Riseart\Api\Auth\Adapter\Visitor as AuthModuleVisitor;
use Riseart\Api\Auth\Adapter\User as AuthModuleUser;

const API_KEY = '';
const VISITOR_ID = '';
const USERNAME = '';
const PASSWORD = '';
try {
    $application = new AuthModuleApplication([
        'apiKey' => API_KEY,
        'verifySSL' => false
    ]);

    $visitor = new AuthModuleVisitor([
        'apiKey' => API_KEY,
        'visitorId' => VISITOR_ID,
        'verifySSL' => false
    ]);

    $user = new AuthModuleUser([
        'apiKey' => API_KEY,
        'username' => USERNAME,
        'password' => PASSWORD,
        'verifySSL' => false
    ]);

    // Authenticate a module through the constructor
    $clientConfig = [
        'authAdapter' => $application
    ];
    $riseartClient = new RiseartClient($clientConfig);
    var_dump($riseartClient->getToken());

    // Authenticate a module through the method setAuthAdapter()
    $riseartClient->setAuthAdapter($visitor);
    var_dump($riseartClient->getToken());

    // Authenticate a user
    $riseartClient->setAuthAdapter($user);
    var_dump($riseartClient->getToken());

    // Retrieves the user information
    $response = $riseartClient->GET('/me');
    var_dump($response);

} catch (\Error $e) {
    var_dump($e->getMessage());
}
