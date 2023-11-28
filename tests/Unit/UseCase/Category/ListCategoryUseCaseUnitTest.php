<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Entity\Category;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\UseCase\Category\ListCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryInputDto;
use Core\UseCase\DTO\Category\CategoryOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use stdClass;

class ListCategoryUseCaseUnitTest extends TestCase
{
    private $mockRepo;
    private $mockEntity;

    public function testGetById()
    {
        $id = Uuid::uuid4()->toString();
        $categoryName = 'name cat';

        $this->mockEntity = Mockery::mock(Category::class, [
            $id,
            $categoryName
        ]);
        $this->mockEntity->shouldReceive('id')->andReturn($id);
        $this->mockEntity->shouldReceive('createdAt')->andReturn(date("Y-m-d H:i:s"));

        $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive("findById")->with($id)->andReturn($this->mockEntity);

        $this->mockInputDto = Mockery::mock(CategoryInputDto::class, [
            $id
        ]);
        $useCase = new ListCategoryUseCase($this->mockRepo);
        $output = $useCase->execute($this->mockInputDto);

        $this->assertInstanceOf(CategoryOutputDto::class, $output);
        $this->assertEquals($categoryName, $output->name);
        $this->assertEquals($id, $output->id);


        /**
         * spies
         */

        $this->spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $this->spy->shouldReceive("findById")->andReturn($this->mockEntity);
        $useCase = new ListCategoryUseCase($this->spy);

        $useCase->execute($this->mockInputDto);
        $this->spy->shouldHaveReceived("findById");

        
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
