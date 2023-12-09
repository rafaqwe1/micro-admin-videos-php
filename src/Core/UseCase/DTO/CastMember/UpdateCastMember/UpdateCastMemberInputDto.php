<?php

namespace Core\UseCase\DTO\CastMember\UpdateCastMember;

class UpdateCastMemberInputDto
{
    public function __construct(
        public string $id,
        public string $name
    ) {
    }
}
