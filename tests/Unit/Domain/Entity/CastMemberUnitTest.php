<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\ValueObject\Uuid;
use PHPUnit\Framework\TestCase;

class CastMemberUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testAttributes()
    {
        $uuid = Uuid::random();
        $castMember = new CastMember(
            id: $uuid,
            name: "cast member",
            type: CastMemberType::DIRECTOR
        );

        $this->assertEquals($uuid->__toString(), $castMember->id());
        $this->assertEquals("cast member", $castMember->name);
        $this->assertEquals(CastMemberType::DIRECTOR, $castMember->type);
    }

    public function testUpdate()
    {
        $castMember = new CastMember(
            name: "cast member",
            type: CastMemberType::ACTOR
        );

        $castMember->update("updated cast member");
        $this->assertEquals("updated cast member", $castMember->name);
    }

    public function testValidateMinName()
    {
        $uuid = Uuid::random();
        $this->expectException(EntityValidationException::class);
        new CastMember(
            id: $uuid,
            name: "ca",
            type: CastMemberType::ACTOR
        );
    }

    public function testValidateMaxName()
    {
        $uuid = Uuid::random();
        $this->expectException(EntityValidationException::class);
        new CastMember(
            id: $uuid,
            name: random_bytes(256),
            type: CastMemberType::ACTOR
        );
    }

    public function testExceptionUpdate()
    {
        $castMember = new CastMember(
            name: 'test',
            type: CastMemberType::ACTOR
        );

        $this->expectException(EntityValidationException::class);
        $castMember->update(name: 'a');
    }
}
