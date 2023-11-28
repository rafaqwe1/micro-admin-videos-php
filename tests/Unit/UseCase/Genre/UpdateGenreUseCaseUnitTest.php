<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\DTO\Genre\UpdateGenre\UpdateGenreInputDto;
use Core\UseCase\DTO\Genre\UpdateGenre\UpdateGenreOutputDto;
use Core\UseCase\Genre\UpdateGenreUseCase;
use Core\UseCase\Interfaces\TransactionInterface;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class UpdateGenreUseCaseUnitTest extends TestCase
{

    // private static string $genderId;

    // public static function setUpBeforeClass(): void
    // {
    //     parent::setUpBeforeClass();
        
    //     self::%$genderId = Uuid::uuid4()->toString();
    // }

    public function testUpdateNewGenre()
    {
        $uuid = Uuid::uuid4()->toString();
        $useCase = new UpdateGenreUseCase($this->mockGenreRepository($uuid), $this->mockTransaction(), $this->mockCategoryRepository($uuid));
        
        $mockInputDto = $this->mockInput($uuid, [$uuid]);        
        $responseUseCase = $useCase->execute($mockInputDto);
        $this->assertInstanceOf(UpdateGenreOutputDto::class, $responseUseCase);
        $this->assertEquals($uuid, $responseUseCase->id);

        /**
         * spies
         */

        $spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $spy->shouldReceive("findById")->andReturn($this->mockEntity($uuid));
        $spy->shouldReceive("update")->andReturn($this->mockEntity($uuid));
        $useCase = new UpdateGenreUseCase($spy, $this->mockTransaction(), $this->mockCategoryRepository($uuid));
        
        $responseUseCase = $useCase->execute($mockInputDto);
        $spy->shouldHaveReceived("update");        
    }

    public function testUpdateCategoryNotFound()
    {
        $this->expectException(EntityNotFoundException::class);
        $uuid = Uuid::uuid4()->toString();
        $useCase = new UpdateGenreUseCase($this->mockGenreRepository($uuid), $this->mockTransaction(), $this->mockCategoryRepository($uuid));
        
        $mockInputDto = $this->mockInput($uuid, [$uuid, "fake_id"]);        
        $useCase->execute($mockInputDto);
    }

    private function mockGenreRepository(string $uuid)
    {
        $mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $mockRepo->shouldReceive("update")->andReturn($this->mockEntity($uuid));
        $mockRepo->shouldReceive("findById")->andReturn($this->mockEntity($uuid));
        return $mockRepo;
    }

    private function mockCategoryRepository(string $uuid)
    {
        $mockCategoryRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $mockCategoryRepo->shouldReceive("getIdsListIds")->andReturn([$uuid]);
        return $mockCategoryRepo;
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
        $mockEntity->shouldReceive('update');
        $mockEntity->shouldReceive('resetCategories');
        $mockEntity->shouldReceive('activate');
        $mockEntity->shouldReceive('deactivate');
        $mockEntity->shouldReceive('addCategory');

        return $mockEntity;
    }

    private function mockTransaction()
    {
        $mockTransaction = Mockery::mock(stdClass::class, TransactionInterface::class);
        $mockTransaction->shouldReceive("commit");
        $mockTransaction->shouldReceive("rollback");
        return $mockTransaction;
    }

    private function mockInput(string $uuid, array $uuidCategories)
    {
        return Mockery::mock(UpdateGenreInputDto::class, [
            $uuid,
            "Genre name",
            true,
            $uuidCategories
        ]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
