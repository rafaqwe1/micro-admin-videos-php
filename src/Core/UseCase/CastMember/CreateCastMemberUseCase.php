<?php
namespace Core\UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\CreateCastMember\CreateCastMemberInputDto;
use Core\UseCase\DTO\CastMember\CreateCastMember\CreateCastMemberOutputDto;

class CreateCastMemberUseCase
{
    public function __construct(
        private CastMemberRepositoryInterface $repository
    ) {
    }

    public function execute(CreateCastMemberInputDto $input): CreateCastMemberOutputDto
    {
        $castMember = $this->repository->insert(new CastMember(
            name: $input->name,
            type: $input->type
        ));

        $output = new CreateCastMemberOutputDto(
            id: $castMember->id(),
            name: $castMember->name,
            type: $castMember->type->value,
            created_at: $castMember->createdAt()
        );

        return $output;
    }
}
