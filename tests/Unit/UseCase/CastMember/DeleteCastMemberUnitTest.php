<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Entity\CastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\ValueObject\Uuid;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\DTO\CastMember\DeleteCastMember\DeleteCastMemberOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;

class DeleteCastMemberUnitTest extends TestCase
{
    private $mockRepo;
    private $mockEntity;

    public function testDeleteCastMember()
    {
        $uuid = Uuid::random()->__toString();
        $castMemberName = 'castMember';

        $this->mockEntity = Mockery::mock(CastMember::class, [
            $castMemberName, 
            CastMemberType::ACTOR,
            new Uuid($uuid),
        ]);
        $this->mockEntity->shouldReceive('id')->andReturn($uuid);

        $this->mockRepo = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->mockRepo->shouldReceive('findById')->once()->andReturn($this->mockEntity);
        $this->mockRepo->shouldReceive('delete')->once()->andReturn(true);

        $useCase = new DeleteCastMemberUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($uuid);
        $this->assertInstanceOf(DeleteCastMemberOutputDto::class, $responseUseCase);
        $this->assertTrue($responseUseCase->success);

    }

    public function testDeleteCastMemberFalse()
    {
        $uuid = Uuid::random()->__toString();
        $castMemberName = 'Name';

        $this->mockEntity = Mockery::mock(CastMember::class, [
            $castMemberName,
            CastMemberType::ACTOR,
            new Uuid($uuid)
        ]);
        $this->mockEntity->shouldReceive('id')->once()->andReturn($uuid);

        $this->mockRepo = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->mockRepo->shouldReceive('findById')->once()->andReturn($this->mockEntity);
        $this->mockRepo->shouldReceive('delete')->once()->andReturn(false);

        $useCase = new DeleteCastMemberUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($uuid);
        $this->assertInstanceOf(DeleteCastMemberOutputDto::class, $responseUseCase);
        $this->assertFalse($responseUseCase->success);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
