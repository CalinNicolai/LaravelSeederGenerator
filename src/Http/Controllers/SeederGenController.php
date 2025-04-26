<?php

namespace CalinNicolai\Seedergen\Http\Controllers;

use CalinNicolai\Seedergen\Services\ConfigService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SeederGenController
{
    public function __construct(protected ConfigService $configService)
    {
    }

    public function index(): View
    {
        $config = config('seedergen.database');

        return view('seedergen::generator', compact('config'));
    }

    public function store(Request $request, string $table): RedirectResponse
    {
        $data = $request->get($table);

        $this->configService->updateTableConfig($table, $data);

        return redirect()->route('seeder-generator.index');
    }
}
