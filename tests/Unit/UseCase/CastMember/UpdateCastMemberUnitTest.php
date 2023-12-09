<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\UpdateCastMember\UpdateCastMemberInputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class UpdateCastMemberUnitTest extends TestCase
{
    public function testUpdate()
    {
        $id = Uuid::random();
        
        $mockEntity = Mockery::mock(CastMember::class, [
            "cast member",
            CastMemberType::ACTOR,
            $id
        ]);

        $mockEntity->shouldReceive("id")->andReturn($id->__toString());
        $mockEntity->shouldReceive("createdAt")->andReturn(date("Y-m-d H:i:s"));
        $mockEntity->shouldReceive("update")->once();

        $mockRepository = Mockery::mock(new stdClass, CastMemberRepositoryInterface::class);
        $mockRepository->shouldReceive("findById")->once()->andReturn($mockEntity);

        $newName = "updated cast member";
        $mockEntityUpdated = Mockery::mock(CastMember::class, [
            $newName,
            CastMemberType::ACTOR,
            $id
        ]);

        $mockEntityUpdated->shouldReceive("id")->andReturn($id->__toString());
        $mockEntityUpdated->shouldReceive("createdAt")->andReturn(date("Y-m-d H:i:s"));

        $mockRepository->shouldReceive("update")->once()->andReturn($mockEntityUpdated);

        $useCase = new UpdateCastMemberUseCase($mockRepository);
        
        $output = $useCase->execute(new UpdateCastMemberInputDto($id, ""));

        $this->assertEquals($id->__toString(), $output->id);
        $this->assertEquals($newName, $output->name);
        $this->assertEquals(CastMemberType::ACTOR->value, $output->type);

        Mockery::close();
    }
}
