<?php

declare(strict_types=1);

use DevKraken\Router\Router;
use DevKraken\Controller\SearchController;
use DevKraken\Database\Database;
use DevKraken\Cache\RedisClient;
use DevKraken\Service\SearchService;
use DevKraken\DTO\SearchResultDTO;
use DevKraken\DTO\NewSearchResultDTO;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../src/Config/config.php';

header("Content-Type: application/json");

$db = new Database();
$redis = !USE_REDIS ? new RedisClient() : null;
$searchService = new SearchService($db, $redis);
$searchController = new SearchController($searchService);

$router = new Router();

$router->addRoute('/', fn($queryParams) => $searchController->handle($queryParams, SearchResultDTO::class));
$router->addRoute('/search-zip', fn($queryParams) => $searchController->handle($queryParams, NewSearchResultDTO::class));

$path = $_SERVER['REQUEST_URI'];
$response = $router->routeRequest($path);

echo json_encode($response, JSON_THROW_ON_ERROR);