<?php

namespace Core\UseCase\DTO\Genre\CreateGenre;

class CreateGenreInputDto
{
    public function __construct(
        public string $name,
        public bool $is_active = true,
        public array $categories_id = []
    ) {
    }
}
