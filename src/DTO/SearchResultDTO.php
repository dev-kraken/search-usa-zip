<?php

declare(strict_types=1);

namespace DevKraken\DTO;

readonly class SearchResultDTO implements SearchResultInterface
{
    public function __construct(
        public string $physicalZip,
        public string $physicalCity,
        public string $physicalStateAbbr,
        public string $districtName,
    ) {
    }

    public function toArray(): array
    {
        return [
            'physical_city' => $this->physicalCity,
            'physical_state_abbr' => $this->physicalStateAbbr,
            'physical_zip' => $this->physicalZip,
            'district_name' => $this->districtName,
        ];
    }
}