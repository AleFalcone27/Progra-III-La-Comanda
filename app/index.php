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
require_once './controllers/OrderDetailsController.php';

require_once './middlewares/AuthMiddleware.php';
require_once './middlewares/RoleMiddleware.php';
require_once './middlewares/ProductExistsMiddleware.php';
require_once './middlewares/UploadedFilesMiddleware.php';
require_once './middlewares/LoginMiddleware.php';
require_once './utils/JwtAuth.php';

// Iniciamos la Session
session_start();

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add middlwares
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

// Auth Routes
$app->group('/', function (RouteCollectorProxy $group) {
  $group->post('login', \UserController::class . ':LogIn')->add(new LoginMiddleware());
  $group->post('logout', \UserController::class . ':LogOut');
});

// User Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('/', \UserController::class . ':GetAll');
  $group->get('/{name}', \UserController::class . ':GetOne');
  $group->post('/', \UserController::class . ':AddOne');
  $group->post('/delete', \UserController::class . ':DeleteOne');
  $group->put('/mod', \UserController::class . ':ModifyOne');
})->add(new RoleMiddleware(1))
->add(new AuthMiddleware());

// Products Routes
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('/', \ProductController::class . ':GetAll');
  $group->get('/{name}', \ProductController::class . ':GetOne');
  $group->post('/', \ProductController::class . ':AddOne');
  $group->put('/mod', \ProductController::class . ':ModifyOne');
  $group->put('/delete', \ProductController::class . ':DeleteOne');
  $group->post('/cargarcsv', \ProductController::class)
  ->add(new UploadedFilesMiddleware('text/cvs/','./UploadedProducts/'));
  $group->post('/crearproductos', \ProductController::class . ':PopulateByCSV');
})
->add(new RoleMiddleware(1,4))
->add(new AuthMiddleware());

// Table Routes
$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('/', \TableController::class . ':GetAll');
  $group->get('/{hex_code}', \TableController::class . ':GetOne');
  $group->post('/', \TableController::class . ':AddOne');
  $group->put('/mod', \TableController::class . ':ModifyOne');
  $group->put('/delete', \TableController::class . ':DeleteOne');
})->add(new RoleMiddleware(1,2))
->add(new AuthMiddleware());

// Order Routes
$app->group('/orden', function (RouteCollectorProxy $group) {
  $group->get('/', \OrderController::class . ':GetAll');
  $group->post('/', \OrderController::class . ':AddOne');
  $group->put('/update', \OrderController::class . ':UpdateStatus');
  $group->put('/mod', \OrderController::class . ':ModifyOne');
  $group->put('/start', \OrderDetailsController::class . ':StartPrepping');
  $group->put('/end', \OrderDetailsController::class . ':EndPrepping');
  $group->put('/serve', \OrderDetailsController::class . ':Serve');
})->add(new RoleMiddleware(1,2,3,4,5))
->add(new ProductexistsMiddleware())
->add(new AuthMiddleware());


$app->group('/jwt', function (RouteCollectorProxy $group) {
  
});

$app->run();
