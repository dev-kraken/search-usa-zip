<?php

declare(strict_types=1);

namespace DevKraken\DTO;

readonly class SearchRequestDTO
{
    public function __construct(
        public string $searchTerm,
        public bool $useCache
    ) {
    }
}