<?php

declare(strict_types=1);

namespace DevKraken\Service;

use DevKraken\DTO\SearchRequestDTO;
use DevKraken\Database\DatabaseInterface;
use DevKraken\Cache\CacheInterface;
use mysqli_result;
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

        $results = $this->performSearch($request->searchTerm, $dtoClass);

        if ($request->useCache && $this->cache !== null && !empty($results)) {
            $this->cache->set($cacheKey, serialize($results));
        }

        return $results;
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

    private function performSearch(string $searchTerm, string $dtoClass): array
    {
        $sql = "SELECT * FROM usa_zip_local 
                WHERE delivery_zip LIKE :search OR phy_city LIKE :search 
                ORDER BY delivery_zip, id
                LIMIT 10";

        $stmt = $this->db->getConnection()->prepare($sql);
        $searchParam = "$searchTerm%";
        $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        $stmt->execute();

        return $this->processSearchResults($stmt->fetchAll(PDO::FETCH_ASSOC), $dtoClass);
    }

    private function processSearchResults(array $results, string $dtoClass): array
    {
        $zipCodes = [];
        $data = [];

        foreach ($results as $row) {
            $zipCode = $row['delivery_zip'];
            if (!in_array($zipCode, $zipCodes, true)) {
                $data[] = new $dtoClass(
                    districtName: $row['district_name'],
                    physicalCity: $row['phy_city'],
                    physicalStateAbbr: $row['phy_state_abbr'],
                    physicalZip: $row['delivery_zip']
                );
                $zipCodes[] = $zipCode;
            }
        }

        return $data;
    }
}