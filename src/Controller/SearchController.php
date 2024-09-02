<?php

declare(strict_types=1);

namespace DevKraken\Controller;

use DevKraken\DTO\SearchRequestDTO;
use DevKraken\Service\SearchService;
use DevKraken\DTO\SearchResultInterface;

readonly class SearchController
{
    public function __construct(
        private SearchService $searchService
    ) {
    }

    public function handle(array $queryParams, string $dtoClass): array
    {
        $requestDTO = $this->createSearchRequestDTO($queryParams);
        $results = $this->searchService->search($requestDTO, $dtoClass);

        return $this->formatResults($results);
    }

    private function createSearchRequestDTO(array $queryParams): SearchRequestDTO
    {
        $searchTerm = $queryParams['search'] ?? '';
        return new SearchRequestDTO($searchTerm, USE_REDIS);
    }

    /**
     * @param  SearchResultInterface[]  $results
     */
    private function formatResults(array $results): array
    {
        return array_map(
            static fn(SearchResultInterface $dto): array => $dto->toArray(),
            $results
        );
    }
}