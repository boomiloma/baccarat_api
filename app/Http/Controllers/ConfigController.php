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

    public function get(Request $request)
    {
        return $this->configService->get($request->config);
    }

    public function store(Request $request)
    {
        return $this->configService->store($request->all(), $request->ip());
    }

    public function update(Request $request, Config $config)
    {
        return $this->configService->update($config, $request->all());
    }

    public function delete(Config $config)
    {
        return $this->configService->delete($config);
    }
}
