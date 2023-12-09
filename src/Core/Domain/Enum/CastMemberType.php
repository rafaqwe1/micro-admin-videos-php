<?php

namespace Core\Domain\Enum;

use InvalidArgumentException;

enum CastMemberType: int
{
    case DIRECTOR = 1;
    case ACTOR = 2;

    public static function createIfValid(int $value): self
    {
        $enum = self::tryFrom($value);
        if($enum){
            return $enum;
        }

        throw new InvalidArgumentException("Invalid cast member type");
    }
}
