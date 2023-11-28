<?php

namespace Core\UseCase\DTO\Genre\UpdateGenre;

class UpdateGenreInputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $is_active = true,
        public array $categories_id = []
    ) {
    }
}
