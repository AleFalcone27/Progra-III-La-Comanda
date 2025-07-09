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
require_once './middlewares/ProductExistsMiddleware.php';
require_once './middlewares/UploadedFilesMiddleware.php';
require_once './utils/JwtAuth.php';

// Iniciamos la Session
session_start();

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add middlwares
$app->addErrorMiddleware(true, true, true);
$app->addBodyParsingMiddleware();

// Auth Routes
$app->group('/', function (RouteCollectorProxy $group) {
  $group->post('login', \UserController::class . ':UserLogIn');
  $group->post('logout', \UserController::class . ':LogOut');
  $group->post('register', \UserController::class . ':AddOne');
});

// User Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
  $group->get('/', \UserController::class . ':GetAll');
  $group->get('/{name}', \UserController::class . ':GetOne');
  $group->post('/delete', \UserController::class . ':DeleteOne');
  $group->put('/mod', \UserController::class . ':ModifyOne');
})->add(new AuthMiddleware(1));


// Products Routes
$app->group('/productos', function (RouteCollectorProxy $group) {
  $group->get('/all', \ProductController::class . ':GetAll');
  $group->get('/{name}', \ProductController::class . ':GetOne');
  $group->post('/', \ProductController::class . ':AddOne');
  $group->put('/mod', \ProductController::class . ':ModifyOne');
  $group->put('/delete', \ProductController::class . ':DeleteOne');
  $group->post('/cargarcsv', \ProductController::class)->add(new UploadedFilesMiddleware('text/cvs/','./UploadedProducts/'));
  $group->post('/crearproductos', \ProductController::class . ':PopulateByCSV');
  $group->get('/obtenerproductos/todos', \ProductController::class . ':GetProductsCSV');
})->add(new AuthMiddleware(1));

// Table Routes
$app->group('/mesas', function (RouteCollectorProxy $group) {
  $group->get('/', \TableController::class . ':GetAll');
  $group->get('/{hex_code}', \TableController::class . ':GetOne');
  $group->post('/', \TableController::class . ':AddOne');
  $group->put('/mod', \TableController::class . ':ModifyOne');
  $group->put('/delete', \TableController::class . ':DeleteOne');
})->add(new AuthMiddleware(1,2));

// Order Routes
$app->group('/orden', function (RouteCollectorProxy $group) {
  $group->get('/', \OrderController::class . ':GetOrdersToPrepare')->add(new AuthMiddleware(1,3));
  $group->post('/', \OrderController::class . ':AddOne')->add(new ProductexistsMiddleware());
  $group->put('/update', \OrderController::class . ':UpdateStatus');
  $group->put('/mod', \OrderController::class . ':ModifyOne');
  $group->put('/start', \OrderDetailsController::class . ':StartPrepping')->add(new AuthMiddleware(1,3));
  $group->put('/end', \OrderDetailsController::class . ':EndPrepping')->add(new AuthMiddleware(1,3));
  $group->get('/ReadyToServe', \OrderDetailsController::class . ':ReadyToServe')->add(new AuthMiddleware(1,2));
  $group->put('/serve', \OrderDetailsController::class . ':Serve')->add(new AuthMiddleware(1,2));
  $group->post('/saveImage', \OrderController::class . ':SaveOrderImage')->add(new AuthMiddleware(1,2));
  $group->get('/descargar', \OrderController::class . ':GetCSVFile');
});



$app->run();
