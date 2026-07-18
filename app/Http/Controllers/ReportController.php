<?php

namespace App\Http\Controllers;

use App\Exports\ReportExport;
use App\Http\Requests\ReportRequest;
use App\Repositories\Contracts\ReportRepositoryInterface;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(private ReportRepositoryInterface $reports, private ReportService $service) {}

    public function index(ReportRequest $request, string $type = 'members'): View
    {
        $report = $this->service->generate($type, $request->validated());

        return view('reports.index', [
            ...$report,
            'type' => $type,
            'rows' => $this->service->paginate($report['rows']),
            'savingTypes' => $this->reports->savingTypes(),
        ]);
    }

    public function pdf(ReportRequest $request, string $type): Response
    {
        $report = $this->service->generate($type, $request->validated());

        return Pdf::loadView('reports.pdf', [...$report, 'filters' => $request->validated()])
            ->setPaper('a4', 'landscape')
            ->download("laporan-{$type}-".now()->format('Ymd-His').'.pdf');
    }

    public function excel(ReportRequest $request, string $type): BinaryFileResponse
    {
        $report = $this->service->generate($type, $request->validated());

        return Excel::download(new ReportExport($report['headings'], $report['rows']), "laporan-{$type}-".now()->format('Ymd-His').'.xlsx');
    }
}
