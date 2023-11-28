<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Genre;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresInputDto;
use Core\UseCase\Genre\ListGenresUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListGenresUseCaseTest extends TestCase
{
    
    public function testFindAll()
    {
        Genre::factory()->count(100)->create();

        $repository = new GenreEloquentRepository(new Genre());
        $useCase = new ListGenresUseCase($repository);

        $output = $useCase->execute(new ListGenresInputDto());

        $this->assertCount(15, $output->items);        
        $this->assertEquals(100, $output->total);
    }
}
