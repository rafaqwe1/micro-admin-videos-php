<?php

namespace Core\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersInputDto;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersOutputDto;

class ListCastMembersUseCase
{
    public function __construct(
        private CastMemberRepositoryInterface $repository
    ) {
    }

    public function execute(ListCastMembersInputDto $input): ListCastMembersOutputDto
    {
        $pagination = $this->repository->paginate($input->filter, $input->order, $input->page, $input->totalPage);
        return new ListCastMembersOutputDto(
            $pagination->items(),
            $pagination->total(),
            $pagination->currentPage(),
            $pagination->lastPage(),
            $pagination->firstPage(),
            $pagination->perPage(),
            $pagination->to(),
            $pagination->from()
        );
    }
}
