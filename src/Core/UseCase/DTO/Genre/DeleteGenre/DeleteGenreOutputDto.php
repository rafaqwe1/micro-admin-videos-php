<?php

namespace Core\UseCase\DTO\Genre\DeleteGenre;

class DeleteGenreOutputDto
{
    public function __construct(
        public bool $success
    ) {
    }
}
