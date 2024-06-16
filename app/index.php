<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/DataAccess.php';
require_once './controllers/UserController.php';
require_once './controllers/ProductController.php';
require_once './controllers/TableController.php';
require_once './controllers/OrderController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// User Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('/', \UserController::class . ':GetAll');
    $group->get('/{name}', \UserController::class . ':GetOne');
    $group->post('/', \UserController::class . ':AddOne');
    $group->put('/mod', \UserController::class . ':ModifyOne');
    $group->post('/delete',\UserController::class . ':DeleteOne');
  });

// Products Routes
$app->group('/productos', function(RouteCollectorProxy $group){
  $group->get('/', \ProductController::class . ':GetAll');
  $group->get('/{name}', \ProductController::class . ':GetOne');
  $group->post('/', \ProductController::class . ':AddOne');
  $group->put('/mod', \ProductController::class . ':ModifyOne');
  $group->put('/delete',\ProductController::class . ':DeleteOne');
});

// Table Routes
$app->group('/mesas', function(RouteCollectorProxy $group){
  $group->get('/', \TableController::class . ':GetAll');
  $group->get('/{hex_code}', \TableController::class . ':GetOne');
  $group->post('/', \TableController::class . ':AddOne');
  $group->put('/mod', \TableController::class . ':ModifyOne');
  $group->put('/delete',\TableController::class . ':DeleteOne');
});

// Order Routes
$app->group('/orden', function(RouteCollectorProxy $group){
  $group->get('/', \OrderController::class . ':GetAll');
  $group->post('/', \OrderController::class . ':AddOne');
  $group->put('/update',\OrderController::class . ':UpdateStatus');
  $group->put('/mod', \OrderController::class . ':ModifyOne');
});


$app->get('[/funciona]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
