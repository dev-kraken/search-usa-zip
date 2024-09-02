<?php

declare(strict_types=1);

namespace DevKraken\Database;

use PDO;

interface DatabaseInterface
{
    public function getConnection(): PDO;

    public function closeConnection(): void;
}