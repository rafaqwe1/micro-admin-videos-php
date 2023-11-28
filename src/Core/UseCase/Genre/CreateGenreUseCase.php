<?php

namespace Core\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\UseCase\DTO\Genre\CreateGenre\CreateGenreInputDto;
use Core\UseCase\DTO\Genre\CreateGenre\CreateGenreOutputDto;
use Core\UseCase\Interfaces\TransactionInterface;
use Throwable;

class CreateGenreUseCase
{

    public function __construct(
        private GenreRepositoryInterface $repository,
        private TransactionInterface $transaction,
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function execute(CreateGenreInputDto $input): CreateGenreOutputDto
    {
        $genre = new Genre(
            name: $input->name,
            isActive: $input->is_active,
            categoriesId: $input->categories_id
        );

        try {
            $this->validateCategoriesId($input->categories_id);
            $genreDb = $this->repository->insert($genre);
            $this->transaction->commit();
            return new CreateGenreOutputDto(
                $genreDb->id(),
                $genreDb->name,
                $genreDb->isActive,
                $genreDb->categoriesId,
                $genreDb->createdAt()
            );
        } catch (Throwable $th) {
            $this->transaction->rollback();
            throw $th;
        }
    }

    private function validateCategoriesId(array $ids)
    {
        if(!$ids){
            return;
        }

        $categoriesDb = $this->categoryRepository->getIdsListIds($ids);
        
        $arrayDiff = array_diff($ids, $categoriesDb);
        if(empty($arrayDiff)){
            return;
        }

        $msg = sprintf("%s %s not found",
            count($arrayDiff) > 1 ? "categories" : "category",
            implode(", ", $arrayDiff)
        );

        throw new EntityNotFoundException($msg);
    }
}
