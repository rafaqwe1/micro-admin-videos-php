<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Category;
use App\Models\Genre;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\UseCase\DTO\Genre\ListGenre\ListGenreInputDto;
use Core\UseCase\Genre\ListGenreUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListGenreUseCaseTest extends TestCase
{

    public function testFindById()
    {
        $category = Category::factory()->create();
        $genre = Genre::factory()->create();

        $genre->categories()
            ->sync([$category->id]);
        $respository = new GenreEloquentRepository(new Genre());
        $useCase = new ListGenreUseCase($respository);
        $output = $useCase->execute(new ListGenreInputDto($genre->id));

        $this->assertEquals($genre->id, $output->id);
        $this->assertEquals($genre->name, $output->name);
        $this->assertCount(1, $output->categories_id);
        $this->assertEquals($genre->is_active, $output->is_active);
        $this->assertEquals($genre->created_at, $output->created_at);
    }
}
