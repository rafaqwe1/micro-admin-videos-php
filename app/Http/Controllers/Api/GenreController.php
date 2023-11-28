<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;
use App\Http\Resources\GenreResource;
use Core\UseCase\DTO\Genre\CreateGenre\CreateGenreInputDto;
use Core\UseCase\DTO\Genre\ListGenre\ListGenreInputDto;
use Core\UseCase\DTO\Genre\ListGenres\ListGenresInputDto;
use Core\UseCase\DTO\Genre\UpdateGenre\UpdateGenreInputDto;
use Core\UseCase\Genre\CreateGenreUseCase;
use Core\UseCase\Genre\DeleteGenreUseCase;
use Core\UseCase\Genre\ListGenresUseCase;
use Core\UseCase\Genre\ListGenreUseCase;
use Core\UseCase\Genre\UpdateGenreUseCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ListGenresUseCase $useCase)
    {
        $response = $useCase->execute(new ListGenresInputDto(
            $request->get('filter', ''),
            $request->get('order', 'DESC'),
            (int) $request->get('page', 1),
            (int) $request->get('totalPage', 15)
        ));

        return GenreResource::collection(collect($response->items))
            ->additional([
                'meta' => [
                    'total' => $response->total,
                    'current_page' => $response->page,
                    'last_page' => $response->last_page,
                    'first_page' => $response->first_page,
                    'per_page' => $response->per_page,
                    'to' => $response->to,
                    'from' => $response->from,
                ]
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreGenreRequest $request, CreateGenreUseCase $useCase)
    {
        $output = $useCase->execute(new CreateGenreInputDto(
            $request->name,
            $request->is_active ?? true,
            $request->categories_id ?? []
        ));

        return (new GenreResource($output))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id, ListGenreUseCase $useCase)
    {
        $output = $useCase->execute(new ListGenreInputDto($id));

        return new GenreResource($output);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateGenreRequest $request, string $id, UpdateGenreUseCase $useCase)
    {
        $output = $useCase->execute(new UpdateGenreInputDto(
            id: $id,
            name: $request->name,
            is_active: $request->is_active ?? true,
            categories_id: $request->categories_id ?? []
        ));
    
        return new GenreResource($output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id, DeleteGenreUseCase $useCase)
    {
        $useCase->execute($id);
        return response()->noContent();
    }
}
