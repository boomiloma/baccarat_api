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
            $gameResult= Config::create($data);
            return response()->json([
                'messages' => ['Created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function get($id): JsonResponse
    {
        try {
            if($id != null) {
                $config = Config::find($id);
            } else {
                $config = Config::latest('id')->first();
            }
            if(!empty($config)){
                return response()->json([
                    'data' => $config
                ], 201);
            }
            return response()->json([
                'data' => [],
                'messsage' => 'Do not have data'
            ], 201);

        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($config, array $data): JsonResponse
    {
        try {
            $config->update($data);
            return response()->json([
                'messages' => ['Updated successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }


    public function delete($config): JsonResponse
    {
        try {
            $config->delete();
            return response()->json([
                'messages' => ['Deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

}
