<?php

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';
require_once __DIR__ . '/../app/core/Session.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/core/Controller.php';

require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/DashboardController.php';
require_once __DIR__ . '/../app/controllers/CategoryController.php';
require_once __DIR__ . '/../app/controllers/EquipmentController.php';
require_once __DIR__ . '/../app/controllers/LocationController.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';

$router = new Router();

$router->get('/', [DashboardController::class, 'index']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/categories', [CategoryController::class, 'index']);
$router->get('/categories/create', [CategoryController::class, 'create']);
$router->post('/categories/create', [CategoryController::class, 'create']);
$router->get('/categories/edit', [CategoryController::class, 'edit']);
$router->post('/categories/edit', [CategoryController::class, 'edit']);
$router->get('/categories/delete', [CategoryController::class, 'delete']);

$router->get('/equipments', [EquipmentController::class, 'index']);
$router->get('/equipments/create', [EquipmentController::class, 'create']);
$router->post('/equipments/create', [EquipmentController::class, 'create']);
$router->get('/equipments/edit', [EquipmentController::class, 'edit']);
$router->post('/equipments/edit', [EquipmentController::class, 'edit']);
$router->get('/equipments/delete', [EquipmentController::class, 'delete']);

$router->get('/locations', [LocationController::class, 'index']);
$router->get('/locations/create', [LocationController::class, 'create']);
$router->post('/locations/create', [LocationController::class, 'create']);
$router->get('/locations/edit', [LocationController::class, 'edit']);
$router->post('/locations/edit', [LocationController::class, 'edit']);
$router->get('/locations/delete', [LocationController::class, 'delete']);

$router->get('/catalog', [HomeController::class, 'catalog']);
$router->get('/home', [HomeController::class, 'index']);

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$router->dispatch($uri);
