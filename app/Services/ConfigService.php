<?php

namespace App\Services;

use App\Models\Config;
use App\Models\GameResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ConfigService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = 'updated_at';
            $sortOrder = $request->descending == 'false' ? 'desc' : 'asc';

            $query = (new Config())->newQuery()->orderBy($sortBy, $sortOrder);

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
            // dd($data);
            // $data
            $gameResult= Config::create($data);
            return response()->json([
                'messages' => ['Created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($city, array $data): JsonResponse
    {
        try {

            return response()->json([
                'messages' => ['Updated successfully'],
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
