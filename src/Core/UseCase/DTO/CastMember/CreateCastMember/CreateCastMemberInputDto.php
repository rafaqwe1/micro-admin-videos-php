<?php

namespace Core\UseCase\DTO\CastMember\CreateCastMember;

class CreateCastMemberInputDto
{
    public function __construct(
        public string $name,
        public int $type
    ) {
    }
}
