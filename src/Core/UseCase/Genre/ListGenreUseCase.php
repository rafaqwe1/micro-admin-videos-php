<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\ListGenre\ListGenreInputDto;
use Core\UseCase\DTO\Genre\ListGenre\ListGenreOutputDto;

class ListGenreUseCase
{
    public function __construct(private GenreRepositoryInterface $repository)
    {
    }

    public function execute(ListGenreInputDto $input): ListGenreOutputDto
    {
        $genre = $this->repository->findById(
            $input->id
        );

        return new ListGenreOutputDto(
            $genre->id(),
            $genre->name,
            $genre->isActive,
            $genre->categoriesId,
            $genre->createdAt()
        );
    }
}
