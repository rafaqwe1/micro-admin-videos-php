<?php

namespace Core\UseCase\DTO\Genre\UpdateGenre;

class UpdateGenreOutputDto
{
    public function __construct(
        public string $id,
        public string $name,
        public bool $is_active,
        public array $categories_id,
        public string $created_at
    ) {
    }
}
