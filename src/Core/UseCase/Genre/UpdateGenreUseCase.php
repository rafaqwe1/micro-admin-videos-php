<?php

namespace Core\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\DTO\Genre\UpdateGenre\UpdateGenreInputDto;
use Core\UseCase\DTO\Genre\UpdateGenre\UpdateGenreOutputDto;
use Core\UseCase\Interfaces\TransactionInterface;
use Throwable;

class UpdateGenreUseCase
{

    public function __construct(
        private GenreRepositoryInterface $repository,
        private TransactionInterface $transaction,
        private CategoryRepositoryInterface $categoryRepository
    ) {
    }

    public function execute(UpdateGenreInputDto $input): UpdateGenreOutputDto
    {
        $genre = $this->repository->findById($input->id);

        try {
            $genre->update($input->name);
            $this->validateCategoriesId($input->categories_id);
            $genre->resetCategories();
            
            if($input->is_active){
                $genre->activate();
            }else{
                $genre->deactivate();
            }

            foreach($input->categories_id as $id){
                $genre->addCategory($id);
            }
            
            $genreDb = $this->repository->update($genre);

            $this->transaction->commit();
            return new UpdateGenreOutputDto(
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
