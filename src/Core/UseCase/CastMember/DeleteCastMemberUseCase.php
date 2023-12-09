<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\DeleteCastMember\DeleteCastMemberOutputDto;

class DeleteCastMemberUseCase
{
    public function __construct(
        private CastMemberRepositoryInterface $repository
    ) {
    }

    public function execute(string $id): DeleteCastMemberOutputDto
    {
        $castMember = $this->repository->findById($id);
        return new DeleteCastMemberOutputDto($this->repository->delete($castMember->id()));
    }
}
