<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesInputDto;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesOutputDto;

class ListCategoriesUseCase
{
    public function __construct(private CategoryRepositoryInterface $repository)
    {
    }

    public function execute(ListCategoriesInputDto $input): ListCategoriesOutputDto
    {
        $pagination = $this->repository->paginate(
            $input->filter,
            $input->order,
            $input->page,
            $input->totalPage
        );

        return new ListCategoriesOutputDto(
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
