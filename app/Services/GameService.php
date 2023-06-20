<?php

namespace App\Services;

use App\Models\GameResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class GameService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = 'updated_at';
            $sortOrder = $request->descending == 'false' ? 'desc' : 'asc';

            $query = (new GameResult())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });


            $results = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data, $ip): JsonResponse
    {
        try {
            $gameResult= GameResult::create($data);
            return response()->json([
                'messages' => ['Created successfully'],
                'id' =>  $gameResult->id
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($id,$data): JsonResponse
    {
        try {
            $result = GameResult::find($id);
            $result->result = $data['result'];
            $result->update();
            return response()->json([
                'messages' => ['Updated successfully'],
                'reuslt' =>  $id
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }


    public function delete($city): JsonResponse
    {
        try {
            $city->delete();

            return response()->json([
                'messages' => ['Deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

}
