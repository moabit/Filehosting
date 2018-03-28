<?php

use Slim\Container;

// Dependencies

// Eloquent ORM
$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']); // primary db
$capsule->addConnection($container['settings']['sphinx'], 'sphinxSearch'); // sphinx search
$capsule->setAsGlobal();
$capsule->bootEloquent();
$container['db'] = function () use ($capsule) {
    return $capsule;
};
// Twig
$container['twig'] = function (Container $c): \Slim\Views\Twig {
    $twig = new \Slim\Views\Twig('../views/templates', [
        'strict_variables' => true
    ]);
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $twig->addExtension(new \Slim\Views\TwigExtension($c['router'], $basePath));
    return $twig;
};
// Slim CSRF Guard
$container['csrf'] = function (Container $c): \Slim\Csrf\Guard {
    return new \Slim\Csrf\Guard('csrf',$storage=null, null, 200, 16, true);
};
// GetID3
require_once(__DIR__ . '/../vendor/james-heinrich/getid3/getid3/getid3.php');
$container['getID3'] = function (Container $c): getID3 {
    return new getID3 ();
};
// Controllers
$container['HomeController'] = function (Container $c): \Filehosting\Controllers\HomeController {
    return new \Filehosting\Controllers\HomeController($c);
};
$container['DownloadController'] = function (Container $c): \Filehosting\Controllers\DownloadController {
    return new \Filehosting\Controllers\DownloadController($c);
};
$container['SearchController'] = function (Container $c): \Filehosting\Controllers\SearchController {
    return new \Filehosting\Controllers\SearchController($c);
};
// Helpers
$container['fileSystem'] = function (): \Filehosting\Helpers\FileSystem {
    return new \Filehosting\Helpers\FileSystem (__DIR__);
};
// Validators
$container['commentValidator'] = function (Container $c): \Filehosting\Validators\CommentValidator {
    return new \Filehosting\Validators\CommentValidator;
};
// UploaderAuth
$container['uploaderAuth'] = function (Container $c): \Filehosting\Auth\UploaderAuth {
    return new \Filehosting\Auth\UploaderAuth ($c);
};

$container['search'] = function (Container $c): \Filehosting\Helpers\SphinxSearchGateway {
    return new \Filehosting\Helpers\SphinxSearchGateway($c['db']->connection('sphinxSearch'));
};
