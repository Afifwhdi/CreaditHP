<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\CreditReport;
use App\Models\Customer;
use App\Models\Installment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class CreditReportDownloadController extends Controller
{
    /** Storage disk untuk file laporan */
    private const DISK = 'public';

    /** Tipe laporan yang didukung */
    private const TYPE_OVERDUE   = 'overdue';
    private const TYPE_PAID      = 'paid';
    private const TYPE_CUSTOMERS = 'customers';

    /** Map tipe laporan -> view blade */
    private const VIEW_MAP = [
        self::TYPE_OVERDUE   => 'pdf.report.overdue',
        self::TYPE_PAID      => 'pdf.report.paid',
        self::TYPE_CUSTOMERS => 'pdf.report.customers',
    ];

    /**
     * Endpoint unduh laporan.
     * GET /credit-reports/{report}/download?purge=1
     */

    public function download(Request $request, CreditReport $report): BinaryFileResponse
    {
        $this->ensureReportPathExists($report);

        if (!$this->fileExists($report)) {
            $this->renderAndStorePdf($report);
        }

        $downloadName = $this->buildDownloadName($report);
        $absolutePath = $this->absolutePath($report);

        $response = response()->download($absolutePath, $downloadName);

        if ($request->boolean('purge', false)) {
            $response->deleteFileAfterSend(true);
        }

        return $response;
    }


    /**
     * Render PDF dari data laporan lalu simpan ke storage.
     */
    
    private function renderAndStorePdf(CreditReport $report): void
    {
        [$payload, $view] = $this->buildPayloadAndView($report);

        $pdf = Pdf::loadView($view, $payload)->setPaper('a4', 'portrait');

        Storage::disk(self::DISK)->put($report->path_file, $pdf->output());
    }

    /**
     * Konstruksi payload + pilih view berdasarkan tipe laporan.
     * Menghindari if-else besar dengan switch yang jelas.
     *
     * @return array{0: array<string,mixed>, 1: string}
     */
    private function buildPayloadAndView(CreditReport $report): array
    {
        $start = optional($report->start_date)->toDateString();
        $end   = optional($report->end_date)->toDateString();

        $rows = match ($report->report_type) {
            self::TYPE_OVERDUE   => $this->queryOverdue($start, $end),
            self::TYPE_PAID      => $this->queryPaid($start, $end),
            self::TYPE_CUSTOMERS => $this->queryCustomers($report->customer_status, $start, $end),
            default              => collect(),
        };

        $view = self::VIEW_MAP[$report->report_type] ?? self::VIEW_MAP[self::TYPE_CUSTOMERS];

        return [[
            'title'  => $report->name ?? 'Laporan',
            'start'  => $start,
            'end'    => $end,
            'status' => $report->customer_status,
            'rows'   => $rows,
        ], $view];
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // Queries (dipisah per laporan agar teruji & mudah dirawat)
    // ─────────────────────────────────────────────────────────────────────────────

    /** @return Collection<int,Installment> */
    private function queryOverdue(?string $start, ?string $end): Collection
    {
        $q = Installment::query()
            ->with('customer')
            ->whereNull('paid_at');

        if ($start) {
            $q->whereDate('due_date', '>=', $start);
        }
        if ($end) {
            $q->whereDate('due_date', '<=', $end);
        }

        return $q->orderBy('due_date')->get();
    }

    /** @return Collection<int,Installment> */
    private function queryPaid(?string $start, ?string $end): Collection
    {
        $q = Installment::query()
            ->with('customer')
            ->whereNotNull('paid_at');

        if ($start) {
            $q->whereDate('paid_at', '>=', $start);
        }
        if ($end) {
            $q->whereDate('paid_at', '<=', $end);
        }

        return $q->orderBy('paid_at')->get();
    }

    /** @return Collection<int,Customer> */
    private function queryCustomers(?string $status, ?string $start, ?string $end): Collection
    {
        $q = Customer::query();

        if ($status) {
            $q->where('status', $status);
        }
        if ($start) {
            $q->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $q->whereDate('created_at', '<=', $end);
        }

        return $q->orderBy('name')->get();
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // File helpers
    // ─────────────────────────────────────────────────────────────────────────────

    private function ensureReportPathExists(CreditReport $report): void
    {
        if (!filled($report->path_file)) {
            $report->path_file = $this->buildDefaultRelativePath($report);
            $report->save();
        }
    }

    private function buildDefaultRelativePath(CreditReport $report): string
    {
        $slug = str($report->name ?: 'laporan')->slug('-');
        return "reports/{$slug}.pdf";
    }

    private function fileExists(CreditReport $report): bool
    {
        return Storage::disk(self::DISK)->exists($report->path_file);
    }

    private function absolutePath(CreditReport $report): string
    {
        return Storage::disk(self::DISK)->path($report->path_file);
    }

    private function buildDownloadName(CreditReport $report): string
    {
        return trim(($report->name ?: 'laporan') . '.pdf');
    }
}