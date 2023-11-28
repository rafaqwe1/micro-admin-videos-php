<?php

namespace App\Repositories\Eloquent;

use App\Models\Category as ModelCategory;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;

class CategoryEloquentRepository implements CategoryRepositoryInterface
{
    public function __construct(protected ModelCategory $model)
    {
    }

    public function insert(EntityCategory $category): EntityCategory
    {
        $model = $this->model->create([
            'id' => $category->id(),
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->isActive
        ]);

        return $this->toCategory($model);
    }

    public function findById(string $id): EntityCategory
    {
        $category = $this->model->find($id);

        if (!$category) {
            throw new EntityNotFoundException("Category not found");
        }

        return $this->toCategory($category);
    }

    public function getIdsListIds(array $ids): array
    {
        return $this->model
            ->whereIn("id", $ids)
            ->pluck("id")
            ->toArray();
    }

    public function findAll(string $filter = '', $order = 'DESC'): array
    {
        $categories = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter) {
                    $query->where("name", "like", "%{$filter}%");
                }
            })
            ->orderBy('id', $order)
            ->get();

        return $categories->toArray();
    }

    public function paginate(string $filter = '', string $order = 'DESC', int $page = 1, int $totalPage = 15): PaginationInterface
    {
        $query = $this->model;

        if ($filter) {
            $query->where("name", "like", "%{$filter}%");
        }

        $query->orderBy("id", $order);
        $pagination = $query->paginate();

        return new PaginationPresenter($pagination);
    }

    public function update(EntityCategory $category): EntityCategory
    {
        $categoryDb = $this->model->find($category->id());

        if (!$categoryDb) {
            throw new EntityNotFoundException("Category not found");
        }

        $categoryDb->fill([
            "name" => $category->name,
            'name' => $category->name,
            'description' => $category->description,
            'is_active' => $category->isActive
        ]);
        $categoryDb->update();

        return $this->toCategory($categoryDb);
    }

    public function delete(string $id): bool
    {
        $category = $this->model->find($id);
        if (!$category) {
            throw new EntityNotFoundException("Category not found");
        }
        return (bool) $category->delete();
    }

    private function toCategory(object $object): EntityCategory
    {
        return new EntityCategory(
            $object->id,
            $object->name,
            $object->description,
            $object->is_active,
            $object->created_at
        );
    }
}
