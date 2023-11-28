<?php

namespace Core\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\CreateCategory\CategoryCreateInputDto;
use Core\UseCase\DTO\Category\CreateCategory\CategoryCreateOutputDto;

class CreateCategoryUseCase
{
    public function __construct(private CategoryRepositoryInterface $repository)
    {
    }

    public function execute(CategoryCreateInputDto $input): CategoryCreateOutputDto
    {
        $category = $this->repository->insert(new Category(name: $input->name, description: $input->description, isActive: $input->isActive));

        return new CategoryCreateOutputDto(
            $category->id(),
            $category->name,
            $category->description,
            $category->isActive,
            $category->createdAt()
        );

    }
}
