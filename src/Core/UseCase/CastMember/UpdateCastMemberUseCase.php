<?php
namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\UpdateCastMember\UpdateCastMemberInputDto;
use Core\UseCase\DTO\CastMember\UpdateCastMember\UpdateCastMemberOutputDto;

class UpdateCastMemberUseCase
{
    public function __construct(
        private CastMemberRepositoryInterface $repository
    ) {
    }

    public function execute(UpdateCastMemberInputDto $input): UpdateCastMemberOutputDto
    {
        $castMember = $this->repository->findById($input->id);

        $castMember->update($input->name);
        $castMemberDb = $this->repository->update($castMember);

        $output = new UpdateCastMemberOutputDto(
            id: $castMemberDb->id(),
            name: $castMemberDb->name,
            type: $castMemberDb->type->value,
            created_at: $castMemberDb->createdAt()
        );

        return $output;
    }
}
