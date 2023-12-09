<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\ListCastMember\ListCastMemberInputDto;
use Core\UseCase\DTO\CastMember\ListCastMember\ListCastMemberOutputDto;

class ListCastMemberUseCase
{
    public function __construct(
        private CastMemberRepositoryInterface $repository
    ) {
    }

    public function execute(ListCastMemberInputDto $input): ListCastMemberOutputDto
    {
        $castMember = $this->repository->findById($input->id);
        return new ListCastMemberOutputDto(
            id: $castMember->id(),
            name: $castMember->name,
            type: $castMember->type->value,
            created_at: $castMember->createdAt()
        );
    }
}
