<?php

namespace Tests\Unit\UseCase\CastMember;

use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersInputDto;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersOutputDto;
use Mockery;
use PHPUnit\Framework\TestCase;
use stdClass;

class ListCastMembersUnitTest extends TestCase
{
    private $mockRepo;

    public function testWhenEmpty()
    {

        $this->mockPagination = $this->mockPagination();

        $this->mockRepo = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->mockRepo->shouldReceive("paginate")->once()->andReturn($this->mockPagination);

        $this->mockInputDto = Mockery::mock(ListCastMembersInputDto::class, [
            'teste', 'desc', 1, 15
        ]);

        $useCase = new ListCastMembersUseCase($this->mockRepo);
        $output = $useCase->execute($this->mockInputDto);

        $this->assertInstanceOf(ListCastMembersOutputDto::class, $output);
        $this->assertEmpty($output->items);
        $this->assertEquals(0, $output->total);
    }

    public function testListCastMembers()
    {
        $item = new stdClass();
        $item->id = 'id';
        $item->name = 'name';
        $item->type = 1;
        $this->mockPagination = $this->mockPagination([$item]);

        $this->mockRepo = Mockery::mock(stdClass::class, CastMemberRepositoryInterface::class);
        $this->mockRepo->shouldReceive("paginate")->once()->andReturn($this->mockPagination);

        $this->mockInputDto = Mockery::mock(ListCastMembersInputDto::class, [
            
        ]);

        $useCase = new ListCastMembersUseCase($this->mockRepo);
        $output = $useCase->execute($this->mockInputDto);

        $this->assertCount(1, $output->items);
        $this->assertInstanceOf(ListCastMembersOutputDto::class, $output);
        $this->assertInstanceOf(stdClass::class, $output->items[0]);
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
