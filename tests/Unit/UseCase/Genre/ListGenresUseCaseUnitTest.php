<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresInputDto;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresOutputDto;
use Core\UseCase\Genre\ListGenresUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListGenresUseCaseUnitTest extends TestCase
{

    private $mockRepo;

    public function testWhenEmpty()
    {

        $this->mockPagination = $this->mockPagination();

        $this->mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $this->mockRepo->shouldReceive("paginate")->andReturn($this->mockPagination);

        $this->mockInputDto = Mockery::mock(ListGenresInputDto::class, [
            'teste', 'desc', 1, 15
        ]);

        $useCase = new ListGenresUseCase($this->mockRepo);
        $output = $useCase->execute($this->mockInputDto);

        $this->assertInstanceOf(ListGenresOutputDto::class, $output);
        $this->assertEmpty($output->items);
        $this->assertEquals(0, $output->total);

        /**
         * spies
         */

        $this->spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $this->spy->shouldReceive("paginate")->andReturn($this->mockPagination);
        $useCase = new ListGenresUseCase($this->spy);

        $useCase->execute($this->mockInputDto);
        $this->spy->shouldHaveReceived("paginate");
    }

    public function testListGenres()
    {
        $item = new stdClass();
        $item->id = 'id';
        $item->name = 'name';
        $item->categories_id = [];
        $item->is_active = 'is_active';
        $item->created_at = 'created_at';
        $item->updated_at = 'updated_at';

        $this->mockPagination = $this->mockPagination([$item]);

        $this->mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $this->mockRepo->shouldReceive("paginate")->andReturn($this->mockPagination);

        $this->mockInputDto = Mockery::mock(ListGenresInputDto::class, [
            
        ]);

        $useCase = new ListGenresUseCase($this->mockRepo);
        $output = $useCase->execute($this->mockInputDto);

        $this->assertCount(1, $output->items);
        $this->assertInstanceOf(ListGenresOutputDto::class, $output);
        $this->assertInstanceOf(stdClass::class, $output->items[0]);

        /**
         * spies
         */

        $this->spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $this->spy->shouldReceive("paginate")->andReturn($this->mockPagination);
        $useCase = new ListGenresUseCase($this->spy);

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
