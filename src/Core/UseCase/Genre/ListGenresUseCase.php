<?php

namespace Core\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresInputDto;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresOutputDto;

class ListGenresUseCase
{
    public function __construct(private GenreRepositoryInterface $repository)
    {
    }

    public function execute(ListGenresInputDto $input): ListGenresOutputDto
    {
        $pagination = $this->repository->paginate(
            $input->filter,
            $input->order,
            $input->page,
            $input->totalPage
        );

        return new ListGenresOutputDto(
            $pagination->items(),
            $pagination->total(),
            $pagination->currentPage(),
            $pagination->lastPage(),
            $pagination->firstPage(),
            $pagination->perPage(),
            $pagination->to(),
            $pagination->from()
        );
    }
}
