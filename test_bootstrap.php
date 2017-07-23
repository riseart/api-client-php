<?php
require_once 'vendor/autoload.php';
(is_file('local_env.php')) ? require 'local_env.php' : null;

if(!getenv("RISEART_TESTS_APPLICATION_AUTH_API_KEY")){
    throw new RuntimeException("RISEART_TESTS_APPLICATION_AUTH_API_KEY env is required");
}

if(!getenv("RISEART_TESTS_VISITOR_AUTH_API_KEY")){
    throw new RuntimeException("RISEART_TESTS_VISITOR_AUTH_API_KEY env is required");
}

if(!getenv("RISEART_TESTS_DEFAULT_VISITOR_ID")){
    throw new RuntimeException("RISEART_TESTS_DEFAULT_VISITOR_ID env is required");
}

if(!getenv("RISEART_TESTS_USER_AUTH_API_KEY")){
    throw new RuntimeException("RISEART_TESTS_USER_AUTH_API_KEY env is required");
}

if(!getenv("RISEART_TESTS_DEFAULT_USERNAME")){
    throw new RuntimeException("RISEART_TESTS_DEFAULT_USERNAME env is required");
}

if(!getenv("RISEART_TESTS_DEFAULT_PASSWORD")){
    throw new RuntimeException("RISEART_TESTS_DEFAULT_PASSWORD env is required");
}
