import {
    formatDayMonthYear,
    getMonthBounds,
    getWeekBounds,
    todayDateString,
} from './dates.js';

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

export function formatEmployeeCode(userId) {
    return `EMP-${String(userId ?? 0).padStart(4, '0')}`;
}

/**
 * Resolve report date window and download an Excel-compatible .xls table
 * matching AttendanceExportController::export.
 */
export function exportAttendanceReport({ attendances, users, period = 'today', month = null, year = null }) {
    const now = new Date();
    let startStr;
    let endStr;
    let label;
    let periodTitle;

    if (period === 'weekly') {
        const week = getWeekBounds(now);
        startStr = week.startStr;
        endStr = week.endStr;
        label = `Weekly_${formatDayMonthYear(startStr).replaceAll(' ', '_')}_to_${formatDayMonthYear(endStr).replaceAll(' ', '_')}`;
        periodTitle = `Weekly Report: ${formatDayMonthYear(startStr)} - ${formatDayMonthYear(endStr)}`;
    } else if (period === 'monthly') {
        const m = Number(month ?? now.getMonth() + 1);
        const y = Number(year ?? now.getFullYear());
        const bounds = getMonthBounds(y, m);
        startStr = bounds.startStr;
        endStr = bounds.endStr;
        label = `Monthly_${bounds.monthName.slice(0, 3)}_${y}`;
        periodTitle = `Monthly Report: ${bounds.monthName} ${y}`;
    } else {
        startStr = todayDateString(now);
        endStr = startStr;
        label = `Today_${formatDayMonthYear(startStr).replaceAll(' ', '_')}`;
        periodTitle = `Daily Report: ${formatDayMonthYear(startStr)}`;
    }

    const userMap = new Map(users.map((u) => [u.id, u]));
    const filtered = attendances
        .filter((a) => a.waktu_masuk && a.tanggal >= startStr && a.tanggal <= endStr)
        .sort((a, b) => {
            if (a.tanggal !== b.tanggal) return a.tanggal.localeCompare(b.tanggal);
            return String(a.waktu_masuk).localeCompare(String(b.waktu_masuk));
        });

    let rowsHtml = '';
    filtered.forEach((att, idx) => {
        const user = userMap.get(att.user_id);
        const empId = formatEmployeeCode(user?.id ?? 0);
        const rawStatus = String(att.status ?? '').toLowerCase();
        const clockOut = att.waktu_keluar;
        const earlyStatus = String(att.early_checkout_status ?? '').toLowerCase();

        let displayStatus = 'Checked In';
        let statusColor = '#D1FAE5';

        if (earlyStatus === 'pending') {
            displayStatus = 'Pending Checkout';
            statusColor = '#FEF3C7';
        } else if (clockOut) {
            if (rawStatus === 'pulang cepat') {
                displayStatus = 'Early Checkout';
                statusColor = '#FFEDD5';
            } else {
                displayStatus = 'Checked Out';
                statusColor = '#F1F5F9';
            }
        } else if (rawStatus === 'terlambat') {
            displayStatus = 'Late';
            statusColor = '#FFE4E6';
        }

        rowsHtml += `
            <tr>
                <td style="text-align:center; border:1px solid #D1D5DB; padding:6px;">${idx + 1}</td>
                <td style="border:1px solid #D1D5DB; padding:6px; font-weight:600;">${escapeHtml(empId)}</td>
                <td style="border:1px solid #D1D5DB; padding:6px;">${escapeHtml(user?.name ?? '-')}</td>
                <td style="border:1px solid #D1D5DB; padding:6px;">${escapeHtml(user?.email ?? '-')}</td>
                <td style="border:1px solid #D1D5DB; padding:6px;">${escapeHtml(user?.divisi ?? '-')}</td>
                <td style="border:1px solid #D1D5DB; padding:6px;">${escapeHtml(formatDayMonthYear(att.tanggal))}</td>
                <td style="border:1px solid #D1D5DB; padding:6px; font-family:monospace;">${escapeHtml(att.waktu_masuk || '-')}</td>
                <td style="border:1px solid #D1D5DB; padding:6px; font-family:monospace;">${escapeHtml(clockOut || '-')}</td>
                <td style="border:1px solid #D1D5DB; padding:6px; background-color:${statusColor}; text-align:center; font-weight:600;">${escapeHtml(displayStatus)}</td>
            </tr>
        `;
    });

    if (!rowsHtml) {
        rowsHtml = `
            <tr>
                <td colspan="9" style="text-align:center; border:1px solid #D1D5DB; padding:12px; color:#64748B;">No attendance records for this period.</td>
            </tr>
        `;
    }

    const generatedAt = `${formatDayMonthYear(todayDateString(now))} ${now.toTimeString().slice(0, 8)}`;

    const excelHtml = `<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; }
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #0B4A85; color: #FFFFFF; border: 1px solid #0B4A85; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h2 style="color:#0B4A85; margin-bottom:4px;">TipTap Attendance Report</h2>
    <p style="margin-top:0; color:#475569;">${escapeHtml(periodTitle)} &middot; Total records: ${filtered.length} &middot; Generated: ${escapeHtml(generatedAt)}</p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Department</th>
                <th>Date</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>${rowsHtml}</tbody>
    </table>
</body>
</html>`;

    const filename = `Attendance_${label}.xls`;
    const blob = new Blob([excelHtml], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    setTimeout(() => URL.revokeObjectURL(url), 1000);

    return { filename, count: filtered.length };
}
