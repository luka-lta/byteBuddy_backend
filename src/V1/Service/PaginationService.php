<?php

declare(strict_types=1);

namespace ByteBuddyApi\V1\Service;

class PaginationService
{
    public function paginate(int $totalItems, int $page, int $itemsPerPage): array
    {
        $totalPages = ceil($totalItems / $itemsPerPage);

        return [
            'currentPage' => $page,
            'itemsPerPage' => $itemsPerPage,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ];
    }
}
