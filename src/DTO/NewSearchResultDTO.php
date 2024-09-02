<?php

declare(strict_types=1);

namespace DevKraken\DTO;

readonly class NewSearchResultDTO implements SearchResultInterface
{
    public function __construct(
        public string $districtName,
        public string $physicalCity,
        public string $physicalStateAbbr,
        public string $physicalZip
    ) {
    }

    public function toArray(): array
    {
        return [
            'zip' => $this->physicalZip,
            'city' => $this->physicalCity,
            'state' => $this->physicalStateAbbr
        ];
    }
}