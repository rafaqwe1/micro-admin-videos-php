<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\DTO\Genre\DeleteGenre\DeleteGenreOutputDto;
use Core\UseCase\Genre\DeleteGenreUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class DeleteGenreUseCaseUnitTest extends TestCase
{
    private $mockRepo;
    private $mockEntity;

    public function testDeleteGenre()
    {
        $uuid = (string) Uuid::uuid4()->toString();
        $genreName = 'genre';

        $this->mockEntity = Mockery::mock(Genre::class, [
            $genreName, new ValueObjectUuid($uuid),
        ]);
        $this->mockEntity->shouldReceive('id')->andReturn($uuid);

        $this->mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $this->mockRepo->shouldReceive('findById')->andReturn($this->mockEntity);
        $this->mockRepo->shouldReceive('delete')->andReturn(true);

        $useCase = new DeleteGenreUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($uuid);
        $this->assertInstanceOf(DeleteGenreOutputDto::class, $responseUseCase);
        $this->assertTrue($responseUseCase->success);

        /**
         * spies
         */

        $this->spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $this->spy->shouldReceive("findById")->andReturn($this->mockEntity);
        $this->spy->shouldReceive("delete")->andReturn(true);
        $useCase = new DeleteGenreUseCase($this->spy);
        
        $responseUseCase = $useCase->execute($uuid);
        $this->spy->shouldHaveReceived("delete");

    }

    public function testDeleteGenreFalse()
    {
        $uuid = (string) Uuid::uuid4()->toString();
        $genreName = 'Name';

        $this->mockEntity = Mockery::mock(Genre::class, [
            $genreName, new ValueObjectUuid($uuid)
        ]);
        $this->mockEntity->shouldReceive('id')->andReturn($uuid);

        $this->mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $this->mockRepo->shouldReceive('findById')->andReturn($this->mockEntity);
        $this->mockRepo->shouldReceive('delete')->andReturn(false);

        $useCase = new DeleteGenreUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($uuid);
        $this->assertInstanceOf(DeleteGenreOutputDto::class, $responseUseCase);
        $this->assertFalse($responseUseCase->success);

    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
