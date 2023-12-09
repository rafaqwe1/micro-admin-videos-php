<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CreateCastMember\CreateCastMemberInputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class CreateCastMemberUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testInsert()
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

        $mockRepository->shouldReceive("insert")->once()->andReturn($mockEntity);

        $useCase = new CreateCastMemberUseCase($mockRepository);
        
        $output = $useCase->execute(new CreateCastMemberInputDto("test", CastMemberType::ACTOR->value));

        $this->assertEquals($id->__toString(), $output->id);
        $this->assertEquals($name, $output->name);
        $this->assertEquals(CastMemberType::ACTOR->value, $output->type);

        Mockery::close();
    }
}
