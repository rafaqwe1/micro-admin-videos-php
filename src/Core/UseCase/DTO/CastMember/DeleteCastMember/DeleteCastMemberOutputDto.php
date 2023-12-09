<?php

namespace Core\UseCase\DTO\CastMember\DeleteCastMember;

class DeleteCastMemberOutputDto
{
    public function __construct(
        public bool $success
    ) {
    }
}
