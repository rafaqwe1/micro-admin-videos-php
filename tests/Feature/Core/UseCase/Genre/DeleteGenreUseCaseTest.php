<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Category;
use App\Models\Genre;
use App\Repositories\Eloquent\GenreEloquentRepository;
use Core\UseCase\Genre\DeleteGenreUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteGenreUseCaseTest extends TestCase
{
    public function testDelete()
    {
        $genre = Genre::factory()->create();

        $respository = new GenreEloquentRepository(new Genre());
        $useCase = new DeleteGenreUseCase($respository);
        $output = $useCase->execute($genre->id);

        $this->assertTrue($output->success);
        $this->assertSoftDeleted("genres", ["id" => $genre->id]);
    }
}
