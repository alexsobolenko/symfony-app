<?php

declare(strict_types=1);

namespace App\Model;

final class PaginatedDataModel
{
    /**
     * @var int
     */
    public int $pages;

    /**
     * @var int|null
     */
    public ?int $prev;

    /**
     * @var int|null
     */
    public ?int $next;

    /**
     * @param int $total
     * @param int $limit
     * @param int $page
     * @param array $items
     */
    public function __construct(
        public int $total = 0,
        public int $limit = 10,
        public int $page = 1,
        public array $items = []
    ) {
        $this->pages = 0;
        $this->prev = null;
        $this->next = null;

        if ($this->total > 0) {
            $this->pages = (int)ceil($this->total / $this->limit);

            if ($this->pages > 1) {
                if ($this->page !== $this->pages) {
                    $this->next = $this->page + 1;
                }
                if ($this->page !== 1) {
                    $this->prev = $this->page - 1;
                }
            } else {
                $this->page = 1;
            }
        }
    }

    /**
     * @param int $length
     * @return int[]
     */
    public function pageItems(int $length = 5): array
    {
        $res = [1];
        if ($this->total > 0) {
            $this->pages = (int)ceil($this->total / $this->limit);
            if ($this->pages <= $length) {
                for ($i = 2; $i <= $this->pages; $i++) {
                    $res[] = $i;
                }
            } else {
                if ($this->page > $length) {
                    $res[] = '...';
                }

                if ($this->page + ($length - 1) <= $this->pages && $this->page >= $length) {
                    for ($i = $this->page - 2; $i <= $this->page + 2; $i++) {
                        $res[] = $i;
                    }
                } else {
                    if ($this->page > $length) {
                        $start = $this->pages - $length - 1;
                        $end = $this->pages;
                    } else {
                        $start = 2;
                        $end = 5;
                    }

                    if ($start < 2) {
                        $start = 2;
                    }

                    if ($end > $this->pages) {
                        $end = $this->pages;
                    }

                    for ($i = $start; $i <= $end; $i++) {
                        $res[] = $i;
                    }
                }

                if ($this->page <= $this->pages - ($length - 1)) {
                    $res[] = '...';
                }

                if (!in_array($this->pages, $res, true)) {
                    $res[] = $this->pages;
                }
            }
        }

        return $res;
    }
}
