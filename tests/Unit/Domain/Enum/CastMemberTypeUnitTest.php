<?php

namespace Tests\Unit\Domain\Enum;

use Core\Domain\Enum\CastMemberType;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CastMemberTypeUnitTest extends TestCase
{
    
    public function test_valid()
    {
        $type = CastMemberType::createIfValid(CastMemberType::ACTOR->value);
        $this->assertEquals($type->value, CastMemberType::ACTOR->value);
    }

    public function test_invalid()
    {
        $this->expectException(InvalidArgumentException::class);
        CastMemberType::createIfValid(0);
    }
}
