<?php

namespace Tests\Unit\UseCase\Genre;

use Core\Domain\Entity\Genre;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid as ValueObjectUuid;
use Core\UseCase\DTO\Genre\ListGenre\ListGenreInputDto;
use Core\UseCase\DTO\Genre\ListGenre\ListGenreOutputDto;
use Core\UseCase\Genre\ListGenreUseCase;
use Mockery;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class ListGenreUseCaseUnitTest extends TestCase
{
    private $mockRepo;
    private $mockEntity;

    public function testGetById()
    {
        $id = Uuid::uuid4()->toString();
        $genreName = 'name genre';

        $this->mockEntity = Mockery::mock(Genre::class, [
            $genreName,
            new ValueObjectUuid($id)
        ]);
        $this->mockEntity->shouldReceive('id')->andReturn($id);
        $this->mockEntity->shouldReceive('createdAt')->andReturn(date("Y-m-d H:i:s"));

        $this->mockRepo = Mockery::mock(stdClass::class, GenreRepositoryInterface::class);
        $this->mockRepo->shouldReceive("findById")->with($id)->andReturn($this->mockEntity);

        $this->mockInputDto = Mockery::mock(ListGenreInputDto::class, [
            $id
        ]);
        $useCase = new ListGenreUseCase($this->mockRepo);
        $output = $useCase->execute($this->mockInputDto);

        $this->assertInstanceOf(ListGenreOutputDto::class, $output);
        $this->assertEquals($genreName, $output->name);
        $this->assertEquals($id, $output->id);


        /**
         * spies
         */

        $this->spy = Mockery::spy(stdClass::class, GenreRepositoryInterface::class);
        $this->spy->shouldReceive("findById")->andReturn($this->mockEntity);
        $useCase = new ListGenreUseCase($this->spy);

        $useCase->execute($this->mockInputDto);
        $this->spy->shouldHaveReceived("findById");        
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
