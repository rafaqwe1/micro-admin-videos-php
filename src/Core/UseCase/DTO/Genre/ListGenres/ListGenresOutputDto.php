<?php

namespace Core\UseCase\DTO\Genre\ListGenres;

class ListGenresOutputDto
{
    public function __construct(
        public array $items,
        public int $total,
        public int $page,
        public int $last_page,
        public int $first_page,
        public int $per_page,
        public int $to,
        public int $from
    ){}
}
