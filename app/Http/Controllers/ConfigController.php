<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Services\ConfigService;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    public function __construct(private ConfigService $configService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->configService->paginate($request);
    }

    public function store(Request $request)
    {
        return $this->configService->store($request->all(), $request->ip());
    }

    public function update(Request $request, Config $gameResult)
    {
        return $this->configService->update($gameResult, $request->all());
    }

    public function delete(Config $banner)
    {
        return $this->configService->delete($banner);
    }
}
