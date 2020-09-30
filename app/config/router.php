<?php

/** @var Di $di */
/** @var Router $router */

use Phalcon\Di;
use Phalcon\Mvc\Router;

$router = $di->getShared('router');

// Define your routes here

$router->handle($_SERVER['REQUEST_URI']);

//$router->setDefaults(array(
////    'namespace'  => 'Store\Controllers',
//    'controller' => 'poll',
//	'action' => 'index'
//));
//
//$router->add("/polls/{id}",
//	[
////        'namespace'  => 'Store\Controllers',
//        "controller" => "polls",
//		"action"     => "view",
//	]);

$router->add(
    "/polls/{id:\d+}",
    "Polls::view",
    ["GET"]
);

$router->add(
    "/polls_options/{id:\d+}",
    "PollsOptions::index",
    ["GET"]
);

$router->add(
    "/polls_options/{id:\d+}/new",
    "PollsOptions::new",
    ["GET"]
);

$router->add(
    "/polls_options/vote/{id:\d+}",
    "PollsOptions::vote",
    ["GET"]
);
