<?php
declare(strict_types=1);

// Env Loader
$envKeeper = new DevKraken\KrakenEnvKeeper(__DIR__.'/../../.env');
$envKeeper->load();

// Database
define("DB_HOST", (string) $envKeeper->get('DB_HOST'));
define("DB_USER", (string) $envKeeper->get('DB_USER'));
define("DB_PASS", (string) $envKeeper->get('DB_PASS'));
define("DB_NAME", (string) $envKeeper->get('DB_NAME'));

// Redis
define("REDIS_HOST", (string) $envKeeper->get('REDIS_HOST'));
define("REDIS_PORT", (int) $envKeeper->get('REDIS_PORT'));
define("USE_REDIS", $envKeeper->get('USE_REDIS') === 'true');
define("REDIS_CACHE_EXP", (int) $envKeeper->get('REDIS_CACHE_EXP'));