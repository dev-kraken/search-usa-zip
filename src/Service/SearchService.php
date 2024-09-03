<?php

declare(strict_types=1);

namespace DevKraken\Service;

use DevKraken\DTO\SearchRequestDTO;
use DevKraken\Database\DatabaseInterface;
use DevKraken\Cache\CacheInterface;
use PDO;

readonly class SearchService
{
    public function __construct(
        private DatabaseInterface $db,
        private ?CacheInterface $cache = null
    ) {
    }

    public function search(SearchRequestDTO $request, string $dtoClass): array
    {
        $cacheKey = "search_results_".md5($request->searchTerm.$dtoClass);

        if ($request->useCache && $this->cache !== null) {
            $cachedResults = $this->getCachedResults($cacheKey, $dtoClass);
            if ($cachedResults !== null) {
                return $cachedResults;
            }
        }

        $searchData = $this->performSearch($request->searchTerm, $dtoClass);

        if ($request->useCache && $this->cache !== null && !empty($searchData['results'])) {
            $this->cache->set($cacheKey, serialize($searchData));
        }

        return $searchData;
    }

    private function getCachedResults(string $cacheKey, string $dtoClass): ?array
    {
        $cachedResults = $this->cache?->get($cacheKey);
        if ($cachedResults === null) {
            return null;
        }

        $unSerializedResults = @unserialize($cachedResults, ['allowed_classes' => [$dtoClass]]);
        return is_array($unSerializedResults) ? $unSerializedResults : null;
    }

    private function explainQuery(string $sql, array $params): array
    {
        $stmt = $this->db->getConnection()->prepare("EXPLAIN $sql");
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function performSearch(string $searchTerm, string $dtoClass): array
    {
        $sql = "SELECT delivery_zip, phy_city, phy_state_abbr, district_name 
            FROM usa_zip_local 
            WHERE delivery_zip LIKE :search OR phy_city LIKE :search
            GROUP BY delivery_zip
            ORDER BY delivery_zip
            LIMIT 10";

        $searchParam = "$searchTerm%";
        $params = [':search' => $searchParam];

        // Get EXPLAIN data
        $explainData = $this->explainQuery($sql, $params);

        // Perform the actual search
        $stmt = $this->db->getConnection()->prepare($sql);
        $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Process the results
        $processedResults = $this->processSearchResults($results, $dtoClass);

        // Return both the results and the EXPLAIN data
        return [
            'results' => $processedResults,
            'explain' => $explainData
        ];
    }

    private function processSearchResults(array $results, string $dtoClass): array
    {
        $zipCodes = [];
        $data = [];

        foreach ($results as $row) {
            $data[] = new $dtoClass(
                districtName: $row['district_name'],
                physicalCity: $row['phy_city'],
                physicalStateAbbr: $row['phy_state_abbr'],
                physicalZip: $row['delivery_zip']
            );
        }

        return $data;
    }
}