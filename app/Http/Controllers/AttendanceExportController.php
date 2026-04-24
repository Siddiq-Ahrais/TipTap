<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class AttendanceExportController extends Controller
{
    /**
     * Export attendance records as a proper Excel (.xls) file with formatted table.
     *
     * Supported periods: today, weekly, monthly
     */
    public function export(Request $request): Response
    {
        $period = $request->query('period', 'today');
        $now = Carbon::now();

        switch ($period) {
            case 'weekly':
                $startDate = $now->copy()->startOfWeek(Carbon::MONDAY);
                $endDate = $now->copy()->endOfWeek(Carbon::SUNDAY);
                $label = 'Weekly_' . $startDate->format('d_M') . '_to_' . $endDate->format('d_M_Y');
                $periodTitle = 'Weekly Report: ' . $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
                break;

            case 'monthly':
                $month = $request->query('month', $now->format('m'));
                $year = $request->query('year', $now->format('Y'));
                $startDate = Carbon::createFromDate((int) $year, (int) $month, 1)->startOfMonth();
                $endDate = $startDate->copy()->endOfMonth();
                $label = 'Monthly_' . $startDate->format('M_Y');
                $periodTitle = 'Monthly Report: ' . $startDate->format('F Y');
                break;

            default: // today
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                $label = 'Today_' . $now->format('d_M_Y');
                $periodTitle = 'Daily Report: ' . $now->format('d M Y');
                break;
        }

        $attendances = Attendance::query()
            ->with('user:id,name,email,role,divisi')
            ->whereBetween('tanggal', [$startDate->toDateString(), $endDate->toDateString()])
            ->whereNotNull('waktu_masuk')
            ->orderBy('tanggal', 'asc')
            ->orderBy('waktu_masuk', 'asc')
            ->get();

        $filename = "Attendance_{$label}.xls";

        // Build an HTML table that Excel will render natively
        $html = $this->buildExcelHtml($attendances, $periodTitle);

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Build an HTML document that Excel will open as a proper spreadsheet.
     */
    private function buildExcelHtml($attendances, string $periodTitle): string
    {
        $rows = '';
        $rowNum = 1;

        foreach ($attendances as $attendance) {
            $user = $attendance->user;
            $empId = 'EMP-' . str_pad((string) ($user?->id ?? 0), 4, '0', STR_PAD_LEFT);

            $rawStatus = strtolower((string) $attendance->status);
            $clockOut = $attendance->waktu_keluar;
            $earlyStatus = strtolower((string) $attendance->early_checkout_status);

            if ($earlyStatus === 'pending') {
                $displayStatus = 'Pending Checkout';
                $statusColor = '#FEF3C7';
            } elseif ($clockOut) {
                if ($rawStatus === 'pulang cepat') {
                    $displayStatus = 'Early Checkout';
                    $statusColor = '#FFEDD5';
                } else {
                    $displayStatus = 'Checked Out';
                    $statusColor = '#F1F5F9';
                }
            } elseif ($rawStatus === 'terlambat') {
                $displayStatus = 'Late';
                $statusColor = '#FFE4E6';
            } else {
                $displayStatus = 'Checked In';
                $statusColor = '#D1FAE5';
            }

            $date = Carbon::parse($attendance->tanggal)->format('d M Y');
            $clockIn = $attendance->waktu_masuk ? Carbon::parse($attendance->waktu_masuk)->format('H:i:s') : '-';
            $clockOutFmt = $clockOut ? Carbon::parse($clockOut)->format('H:i:s') : '-';
            $name = e($user?->name ?? '-');
            $email = e($user?->email ?? '-');
            $divisi = e($user?->divisi ?? '-');

            $rows .= <<<ROW
            <tr>
                <td style="text-align:center; border:1px solid #D1D5DB; padding:6px;">{$rowNum}</td>
                <td style="border:1px solid #D1D5DB; padding:6px; font-weight:600;">{$empId}</td>
                <td style="border:1px solid #D1D5DB; padding:6px;">{$name}</td>
                <td style="border:1px solid #D1D5DB; padding:6px;">{$email}</td>
                <td style="border:1px solid #D1D5DB; padding:6px;">{$divisi}</td>
                <td style="border:1px solid #D1D5DB; padding:6px;">{$date}</td>
                <td style="border:1px solid #D1D5DB; padding:6px; font-family:monospace;">{$clockIn}</td>
                <td style="border:1px solid #D1D5DB; padding:6px; font-family:monospace;">{$clockOutFmt}</td>
                <td style="border:1px solid #D1D5DB; padding:6px; background-color:{$statusColor}; text-align:center; font-weight:600;">{$displayStatus}</td>
            </tr>
ROW;
            $rowNum++;
        }

        $totalRecords = $attendances->count();
        $generatedAt = Carbon::now()->format('d M Y H:i:s');

        return <<<HTML
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!--[if gte mso 9]>
    <xml>
        <x:ExcelWorkbook>
            <x:ExcelWorksheets>
                <x:ExcelWorksheet>
                    <x:Name>Attendance Report</x:Name>
                    <x:WorksheetOptions>
                        <x:DisplayGridlines/>
                    </x:WorksheetOptions>
                </x:ExcelWorksheet>
            </x:ExcelWorksheets>
        </x:ExcelWorkbook>
    </xml>
    <![endif]-->
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        table { border-collapse: collapse; width: 100%; }
        th { 
            background-color: #0B4A85; 
            color: #FFFFFF; 
            font-weight: 700; 
            border: 1px solid #0B4A85; 
            padding: 8px 6px; 
            text-align: left;
            font-size: 11pt;
        }
        td { font-size: 11pt; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td colspan="9" style="font-size:16pt; font-weight:700; padding:10px 0; color:#0B4A85;">
                Attendance Report — TipTap
            </td>
        </tr>
        <tr>
            <td colspan="9" style="font-size:11pt; padding:2px 0 4px; color:#475569;">
                {$periodTitle}
            </td>
        </tr>
        <tr>
            <td colspan="9" style="font-size:10pt; padding:2px 0 10px; color:#94A3B8;">
                Generated: {$generatedAt}
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width:40px; text-align:center;">No</th>
                <th style="width:90px;">Employee ID</th>
                <th style="width:150px;">Name</th>
                <th style="width:180px;">Email</th>
                <th style="width:100px;">Division</th>
                <th style="width:100px;">Date</th>
                <th style="width:80px;">Clock In</th>
                <th style="width:80px;">Clock Out</th>
                <th style="width:120px; text-align:center;">Status</th>
            </tr>
        </thead>
        <tbody>
            {$rows}
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9" style="padding:10px 6px 4px; border-top:2px solid #0B4A85;"></td>
            </tr>
            <tr>
                <td colspan="2" style="font-weight:700; padding:4px 6px;">Total Records:</td>
                <td style="font-weight:700; padding:4px 6px;">{$totalRecords}</td>
                <td colspan="6"></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
HTML;
    }
}
