<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCastMemberRequest;
use App\Http\Requests\UpdateCastMemberRequest;
use App\Http\Resources\CastMemberResouce;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CreateCastMember\CreateCastMemberInputDto;
use Core\UseCase\DTO\CastMember\ListCastMember\ListCastMemberInputDto;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersInputDto;
use Core\UseCase\DTO\CastMember\UpdateCastMember\UpdateCastMemberInputDto;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CastMemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, ListCastMembersUseCase $useCase)
    {
        $response = $useCase->execute(new ListCastMembersInputDto(
            $request->get('filter', ''),
            $request->get('order', 'DESC'),
            (int) $request->get('page', 1),
            (int) $request->get('totalPage', 15)
        ));

        return CastMemberResouce::collection(collect($response->items))
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
    public function store(StoreCastMemberRequest $request, CreateCastMemberUseCase $useCase)
    {
        $output = $useCase->execute(new CreateCastMemberInputDto(
            name: $request->name ?? '',
            type: $request->type ?? 0
        ));

        return (new CastMemberResouce($output))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id, ListCastMemberUseCase $useCase)
    {
        $output = $useCase->execute(new ListCastMemberInputDto($id));

        return (new CastMemberResouce($output))->response();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCastMemberRequest $request, string $id, UpdateCastMemberUseCase $useCase)
    {
        $output = $useCase->execute(new UpdateCastMemberInputDto(
            id: $id,
            name: $request->name
        ));

        return (new CastMemberResouce($output))->response();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id, DeleteCastMemberUseCase $useCase)
    {
        $useCase->execute($id);
        return response()->noContent();
    }
}
