<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportCsvRequest;
use App\Services\CsvService;
use App\Services\MemberCardService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportExportController extends Controller
{
    public function index(): View
    {
        return view('admin.import-export.index', [
            'types' => CsvService::TYPES,
            'headers' => [
                'books' => (new CsvService)->headers('books'),
                'users' => (new CsvService)->headers('users'),
            ],
        ]);
    }

    public function import(ImportCsvRequest $request, CsvService $csv, MemberCardService $memberCards): RedirectResponse
    {
        $result = $csv->import(
            $request->string('type')->toString(),
            $request->file('file'),
            $memberCards,
        );

        return redirect()->route('admin.import-export.index')
            ->with('success', "Import selesai: {$result['imported']} baris masuk, {$result['skipped']} baris dilewati.");
    }

    public function export(string $type, CsvService $csv): StreamedResponse
    {
        abort_unless(in_array($type, CsvService::TYPES, true), 404);

        return $csv->export($type);
    }
}
