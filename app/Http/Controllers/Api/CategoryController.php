<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\Category\ListCategoryUseCase;
use Core\UseCase\Category\UpdateCategoryUseCase;
use Core\UseCase\DTO\Category\CategoryInputDto;
use Core\UseCase\DTO\Category\CreateCategory\CategoryCreateInputDto;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesInputDto;
use Core\UseCase\DTO\Category\UpdateCategory\CategoryUpdateInputDto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class CategoryController extends Controller
{
    public function index(Request $request, ListCategoriesUseCase $useCase)
    {
        $response = $useCase->execute(new ListCategoriesInputDto(
            $request->get('filter', ''),
            $request->get('order', 'DESC'),
            (int) $request->get('page', 1),
            (int) $request->get('totalPage', 15)
        ));

        return CategoryResource::collection(collect($response->items))
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

    public function store(StoreCategoryRequest $request, CreateCategoryUseCase $useCase)
    {
        $response = $useCase
            ->execute(new CategoryCreateInputDto(
                $request->name ?? '',
                $request->description ?? '',
                (bool) $request->is_active ?? true
            ));

        return (new CategoryResource($response))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(string $id, ListCategoryUseCase $useCase)
    {
        $response = $useCase->execute(new CategoryInputDto($id));

        return (new CategoryResource($response))
            ->response();
    }

    public function update(UpdateCategoryRequest $request, UpdateCategoryUseCase $useCase, $id)
    {
        $response = $useCase->execute(new CategoryUpdateInputDto(
            $id,
            $request->name,
            $request->description ?? '',
            (bool)($request->is_active ?? true)
        ));

        return (new CategoryResource($response))
            ->response();
    }

    public function destroy(string $id, DeleteCategoryUseCase $useCase)
    {
        $useCase->execute($id);
        return response()->noContent();
    }
}
