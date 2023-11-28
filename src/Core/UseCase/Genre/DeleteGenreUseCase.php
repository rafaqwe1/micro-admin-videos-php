<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\DeleteGenre\DeleteGenreOutputDto;

class DeleteGenreUseCase
{
    public function __construct(private GenreRepositoryInterface $repository)
    {
    }

    public function execute(string $id): DeleteGenreOutputDto
    {
        $category = $this->repository->findById($id);
        $success = $this->repository->delete($category->id());
        return new DeleteGenreOutputDto($success);
    }
}
