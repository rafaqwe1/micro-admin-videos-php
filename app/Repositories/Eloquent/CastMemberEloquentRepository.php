<?php

namespace App\Repositories\Eloquent;

use App\Models\CastMember as ModelCastMember;
use App\Repositories\Presenters\PaginationPresenter;
use Core\Domain\Entity\CastMember as EntityCastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;

class CastMemberEloquentRepository implements CastMemberRepositoryInterface
{
    public function __construct(protected ModelCastMember $model)
    {
    }

    public function insert(EntityCastMember $castMember): EntityCastMember
    {
        
        $model = $this->model->create([
            'id' => $castMember->id(),
            'name' => $castMember->name,
            'type' => $castMember->type->value,
        ]);

        return $this->toCastMember($model);
    }

    public function findById(string $id): EntityCastMember
    {
        $castMember = $this->model->find($id);

        if (!$castMember) {
            throw new EntityNotFoundException("CastMember not found");
        }

        return $this->toCastMember($castMember);
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
        $castMembers = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter) {
                    $query->where("name", "like", "%{$filter}%");
                }
            })
            ->orderBy('id', $order)
            ->get();

        return $castMembers->toArray();
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

    public function update(EntityCastMember $castMember): EntityCastMember
    {
        $castMemberDb = $this->model->find($castMember->id());

        if (!$castMemberDb) {
            throw new EntityNotFoundException("CastMember not found");
        }

        $castMemberDb->fill([
            'name' => $castMember->name,
            'type' => $castMember->type->value,
        ]);
        $castMemberDb->update();

        return $this->toCastMember($castMemberDb);
    }

    public function delete(string $id): bool
    {
        $castMember = $this->model->find($id);
        if (!$castMember) {
            throw new EntityNotFoundException("CastMember not found");
        }
        return (bool) $castMember->delete();
    }

    private function toCastMember(object $object): EntityCastMember
    {
        return new EntityCastMember(
            id: new Uuid($object->id),
            name: $object->name,
            type: $object->type,
            createdAt: $object->created_at
        );
    }
}
