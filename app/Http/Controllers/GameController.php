<?php

namespace App\Http\Controllers;

use App\Models\GameResult;
use App\Services\GameService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function __construct(private GameService $gameService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->gameService->paginate($request);
    }

    public function store(Request $request)
    {
        return $this->gameService->store($request->all(), $request->ip());
    }

    public function update(Request $request)
    {
        return $this->gameService->update($request->id, $request->all());
    }

    public function delete(GameResult $gameResult)
    {
        return $this->gameService->delete($gameResult);
    }
}
