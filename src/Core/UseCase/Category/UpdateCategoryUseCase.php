<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\UpdateCategory\CategoryUpdateInputDto;
use Core\UseCase\DTO\Category\UpdateCategory\CategoryUpdateOutputDto;

class UpdateCategoryUseCase
{
    public function __construct(private CategoryRepositoryInterface $repository)
    {
    }

    public function execute(CategoryUpdateInputDto $input): CategoryUpdateOutputDto
    {
        $category = $this->repository->findById($input->id);

        $category->update(
            name: $input->name,
            description: $input->description ?? $category->description,
        );

        $categoryUpdated = $this->repository->update($category);
        return new CategoryUpdateOutputDto(
            $categoryUpdated->id,
            $categoryUpdated->name,
            $categoryUpdated->description,
            $categoryUpdated->isActive,
            $categoryUpdated->createdAt()
        );

    }
}
