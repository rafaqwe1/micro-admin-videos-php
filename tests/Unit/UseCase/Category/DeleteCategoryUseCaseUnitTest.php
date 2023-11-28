<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\DTO\Category\DeleteCategory\CategoryDeleteOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class DeleteCategoryUseCaseUnitTest extends TestCase
{
    private $mockRepo;
    private $mockEntity;

    public function testDeleteCategory()
    {
        $uuid = (string) Uuid::uuid4()->toString();
        $categoryName = 'Name';
        $categoryDesc = 'Desc';

        $this->mockEntity = Mockery::mock(Category::class, [
            $uuid, $categoryName, $categoryDesc,
        ]);
        $this->mockEntity->shouldReceive('id')->andReturn($uuid);

        $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive('findById')->andReturn($this->mockEntity);
        $this->mockRepo->shouldReceive('delete')->andReturn(true);

        $useCase = new DeleteCategoryUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($uuid);
        $this->assertInstanceOf(CategoryDeleteOutputDto::class, $responseUseCase);
        $this->assertTrue($responseUseCase->success);

        /**
         * spies
         */

        $this->spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $this->spy->shouldReceive("findById")->andReturn($this->mockEntity);
        $this->spy->shouldReceive("delete")->andReturn(true);
        $useCase = new DeleteCategoryUseCase($this->spy);
        
        $responseUseCase = $useCase->execute($uuid);
        $this->spy->shouldHaveReceived("delete");

    }

    public function testDeleteCategoryFalse()
    {
        $uuid = (string) Uuid::uuid4()->toString();
        $categoryName = 'Name';
        $categoryDesc = 'Desc';

        $this->mockEntity = Mockery::mock(Category::class, [
            $uuid, $categoryName, $categoryDesc,
        ]);
        $this->mockEntity->shouldReceive('id')->andReturn($uuid);

        $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive('findById')->andReturn($this->mockEntity);
        $this->mockRepo->shouldReceive('delete')->andReturn(false);

        $useCase = new DeleteCategoryUseCase($this->mockRepo);
        $responseUseCase = $useCase->execute($uuid);
        $this->assertInstanceOf(CategoryDeleteOutputDto::class, $responseUseCase);
        $this->assertFalse($responseUseCase->success);

    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
