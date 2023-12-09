<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\DTO\CastMember\ListCastMember\ListCastMemberInputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListCastMemberUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_find()
    {
        $id = Uuid::random();
        $name = "new cast member";
        $mockEntity = Mockery::mock(CastMember::class, [
            $name,
            CastMemberType::ACTOR,
            $id
        ]);

        $mockEntity->shouldReceive("id")->andReturn($id->__toString());
        $mockEntity->shouldReceive("createdAt")->andReturn(date("Y-m-d H:i:s"));

        $mockRepository = Mockery::mock(new stdClass, CastMemberRepositoryInterface::class);

        $mockRepository->shouldReceive("findById")->once()->with($id->__toString())->andReturn($mockEntity);

        $useCase = new ListCastMemberUseCase($mockRepository);

        $input = Mockery::mock(ListCastMemberInputDto::class, [$id->__toString()]);
        $output = $useCase->execute($input);

        $this->assertEquals($id->__toString(), $output->id);
        $this->assertEquals($name, $output->name);
        $this->assertEquals(CastMemberType::ACTOR->value, $output->type);
    }
}
