<?php

namespace App\Repositories\Presenters;

use Core\Domain\Repository\PaginationInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use stdClass;

class PaginationPresenter implements PaginationInterface
{

    protected array $items = [];

    public function __construct(protected LengthAwarePaginator $paginator)
    {
        $this->resolveItems($paginator->items());
    }

    /**
     *
     * @return stdClass[]
     */
    public function items(): array
    {
        return $this->items;
    }

    public function total(): int
    {
        return $this->paginator->total();
    }

    public function lastPage(): int
    {
        return $this->paginator->lastPage();
    }

    public function firstPage(): int
    {
        return 1;
    }

    public function currentPage(): int
    {
        return $this->paginator->currentPage();
    }

    public function perPage(): int
    {
        return $this->paginator->perPage();
    }

    public function to(): int
    {
        return (int)$this->paginator->firstItem();
    }

    public function from(): int
    {
        return (int)$this->paginator->lastItem();
    }

    private function resolveItems(array $items)
    {
        foreach($items as $item){
            $stdClass = new stdClass();
            foreach($item->toArray() as $key => $value){
                $stdClass->{$key} = $value;
            }
            $this->items[] = $stdClass;
        }        
    }
}
