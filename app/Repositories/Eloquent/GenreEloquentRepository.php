<?php

namespace App\Repositories\Eloquent;

use App\Models\Genre as Model;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use DateTime;

class GenreEloquentRepository implements GenreRepositoryInterface
{
    public function __construct(private Model $model)
    {
    }

    public function insert(Genre $genre): Genre
    {
        $register = $this->model->create([
            "id" => $genre->id(),
            "name" => $genre->name,
            "is_active" => $genre->isActive,
            "created_at" => $genre->createdAt()
        ]);

        if (count($genre->categoriesId)) {
            $register->categories()->sync($genre->categoriesId);
        }

        return $this->toGenre($register);
    }

    public function findById(string $id): Genre
    {
        $genre = $this->model->find($id);
        if (!$genre) {
            throw new EntityNotFoundException("Genre {$id} not found");
        }

        return $this->toGenre($genre);
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        return $this->model
            ->where(function ($query) use ($filter) {
                if (!empty($filter)) {
                    $query->where('name', 'like', "%{$filter}%");
                }
            })
            ->orderBy('name', $order)->get()->toArray();
    }

    public function paginate(string $filter = '', string $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        $result = $this->model
            ->where(function ($query) use ($filter) {
                if (!empty($filter)) {
                    $query->where('name', 'like', "%{$filter}%");
                }
            })
            ->orderBy('name', $order)
            ->paginate(perPage: $totalPage, page: $page);
        
            return new PaginationPresenter($result);
    }

    public function update(Genre $genre): Genre
    {
        if(!$genreDb = $this->model->find($genre->id())){
            throw new EntityNotFoundException("Genre {$genre->id()} not found");
        }

        $genreDb->update([
            "name" => $genre->name,
            "is_active" => $genre->isActive,
        ]);
        
        $genreDb->categories()->sync($genre->categoriesId);
        $genreDb->refresh();

        return $this->toGenre($genreDb);
    }

    public function delete(string $id): bool
    {
        if(!$genreDb = $this->model->find($id)){
            throw new EntityNotFoundException("Genre {$id} not found");
        }
        
        return (bool) $genreDb->delete();
    }

    private function toGenre(object $object): Genre
    {
        return new Genre(
            name: $object->name,
            id: new Uuid($object->id),
            isActive: $object->is_active,
            categoriesId: $object->categories()->pluck("id")->toArray(),
            createdAt: new DateTime($object->created_at)
        );
    }
}
