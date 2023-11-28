<?php

namespace Core\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\DTO\Category\DeleteCategory\CategoryDeleteOutputDto;

class DeleteCategoryUseCase
{
    public function __construct(private CategoryRepositoryInterface $repository)
    {
    }

    public function execute(string $id): CategoryDeleteOutputDto
    {
        $category = $this->repository->findById($id);
        $success = $this->repository->delete($category->id());
        return new CategoryDeleteOutputDto($success);
    }
}
