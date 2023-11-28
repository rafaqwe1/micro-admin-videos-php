<?php

namespace Tests\Unit\UseCase\Category;

use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesInputDto;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListCategoriesUseCaseUnitTest extends TestCase
{
    private $mockRepo;

    public function testWhenEmpty()
    {

        $this->mockPagination = $this->mockPagination();

        $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive("paginate")->andReturn($this->mockPagination);

        $this->mockInputDto = Mockery::mock(ListCategoriesInputDto::class, [
            
        ]);

        $useCase = new ListCategoriesUseCase($this->mockRepo);
        $output = $useCase->execute($this->mockInputDto);

        $this->assertInstanceOf(ListCategoriesOutputDto::class, $output);
        $this->assertEmpty($output->items);
        $this->assertEquals(0, $output->total);

        /**
         * spies
         */

        $this->spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $this->spy->shouldReceive("paginate")->andReturn($this->mockPagination);
        $useCase = new ListCategoriesUseCase($this->spy);

        $useCase->execute($this->mockInputDto);
        $this->spy->shouldHaveReceived("paginate");

        
    }

    public function testListCategories()
    {
        $item = new stdClass();
        $item->id = 'id';
        $item->name = 'name';
        $item->description = 'description';
        $item->is_active = 'is_active';
        $item->created_at = 'created_at';
        $item->updated_at = 'updated_at';

        $this->mockPagination = $this->mockPagination([$item]);

        $this->mockRepo = Mockery::mock(stdClass::class, CategoryRepositoryInterface::class);
        $this->mockRepo->shouldReceive("paginate")->andReturn($this->mockPagination);

        $this->mockInputDto = Mockery::mock(ListCategoriesInputDto::class, [
            
        ]);

        $useCase = new ListCategoriesUseCase($this->mockRepo);
        $output = $useCase->execute($this->mockInputDto);

        $this->assertCount(1, $output->items);
        $this->assertInstanceOf(ListCategoriesOutputDto::class, $output);
        $this->assertInstanceOf(stdClass::class, $output->items[0]);

        /**
         * spies
         */

        $this->spy = Mockery::spy(stdClass::class, CategoryRepositoryInterface::class);
        $this->spy->shouldReceive("paginate")->andReturn($this->mockPagination);
        $useCase = new ListCategoriesUseCase($this->spy);

        $useCase->execute($this->mockInputDto);
        $this->spy->shouldHaveReceived("paginate");
    }

    protected function mockPagination(array $items = []){
        $mockPagination = Mockery::mock(stdClass::class, PaginationInterface::class);
        $mockPagination->shouldReceive("items")->andReturn($items);
        $mockPagination->shouldReceive("total")->andReturn(0);
        $mockPagination->shouldReceive("currentPage")->andReturn(1);
        $mockPagination->shouldReceive("lastPage")->andReturn(1);
        $mockPagination->shouldReceive("firstPage")->andReturn(1);
        $mockPagination->shouldReceive("to")->andReturn(1);
        $mockPagination->shouldReceive("from")->andReturn(0);
        $mockPagination->shouldReceive("perPage")->andReturn(15);
        return $mockPagination;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
