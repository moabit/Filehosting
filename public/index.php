<?php

session_start();
require '../vendor/autoload.php';

// Configuration
$config = Filehosting\Helpers\Util::readJSON(__DIR__ . '/../config.json');
// DI Container
$container = new Slim\Container ($config);
require '../app/container.php';
// Slim app instance
$app = new \Slim\App($container);
// Middlewares
$app->add($container['csrf']);
// Routes
$app->get('/', 'HomeController:index');
$app->get('/test', 'HomeController:test');
$app->post('/', 'HomeController:uploadFile');

$app->get('/file/{id}', 'DownloadController:index');
$app->get('/file/{id}/{filename}', 'DownloadController:forceFileDownload');
$app->post('/file/{id}', 'DownloadController:addComment');

$app->get('/search', 'SearchController:index');

// Route for file uploader
$app->get('/delete/file/{id}', 'DownloadController:deleteFile')->add(new Filehosting\Middlewares\UploaderAuthMiddleware($container));

$app->run();
