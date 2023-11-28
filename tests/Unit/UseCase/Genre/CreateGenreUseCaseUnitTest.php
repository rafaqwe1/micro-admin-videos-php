<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\DTO\Genre\CreateGenre\CreateGenreInputDto;
use Core\UseCase\DTO\Genre\CreateGenre\CreateGenreOutputDto;
use Core\UseCase\Genre\CreateGenreUseCase;
use Core\UseCase\Interfaces\TransactionInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class CreateGenreUseCaseUnitTest extends TestCase
{
    public function testCreateNewGenre()
    {
        $uuid = Uuid::uuid4()->toString();

        $mockEntity = $this->mockEntity($uuid);

        $mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive("insert")->andReturn($mockEntity);

        $mockCategoryRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepo->shouldReceive("getIdsListIds")->andReturn([$uuid]);
        
        $mockTransaction = $this->mockTransaction();

        $mockInputDto = $this->mockInput([$uuid]);
        
        $useCase = new CreateGenreUseCase($mockRepo, $mockTransaction, $mockCategoryRepo);
        
        $responseUseCase = $useCase->execute($mockInputDto);
        $this->assertInstanceOf(CreateGenreOutputDto::class, $responseUseCase);
        $this->assertEquals($uuid, $responseUseCase->id);

        /**
         * spies
         */

        $spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $spy->shouldReceive("insert")->andReturn($mockEntity);
        $useCase = new CreateGenreUseCase($spy, $mockTransaction, $mockCategoryRepo);
        
        $responseUseCase = $useCase->execute($mockInputDto);
        $spy->shouldHaveReceived("insert");        
    }

    public function testCreateCategoryNotFound()
    {
        $this->expectException(EntityNotFoundException::class);
        $uuid = Uuid::uuid4()->toString();

        $mockEntity = $this->mockEntity($uuid);

        $mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive("insert")->andReturn($mockEntity);

        $mockCategoryRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepo->shouldReceive("getIdsListIds")->andReturn([$uuid]);
        
        $mockTransaction = $this->mockTransaction();

        $mockInputDto = $this->mockInput([$uuid, "fake"]);
        
        $useCase = new CreateGenreUseCase($mockRepo, $mockTransaction, $mockCategoryRepo);
        
        $useCase->execute($mockInputDto);
    }

    private function mockEntity(string $uuid)
    {
        $genreName = 'name genre';

        $mockEntity = Mockery::mock(Genre::class, [
            $genreName,
            new ValueObjectUuid($uuid),
            true,
            [$uuid]
        ]);
        $mockEntity->shouldReceive('id')->andReturn($uuid);
        $mockEntity->shouldReceive('createdAt')->andReturn(date("Y-m-d H:i:s"));

        return $mockEntity;
    }

    private function mockTransaction()
    {
        $mockTransaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $mockTransaction->shouldReceive("commit");
        $mockTransaction->shouldReceive("rollback");
        return $mockTransaction;
    }

    private function mockInput(array $uuids)
    {
        return Mockery::mock(CreateGenreInputDto::class, [
            "Genre name",
            true,
            $uuids
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
