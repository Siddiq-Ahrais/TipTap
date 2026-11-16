/**
 * Date and time formatting utilities using the browser's local clock.
 */

const MONTHS_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
const MONTHS_FULL = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December',
];
const DAYS_SHORT = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const DAYS_FULL = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

function pad2(n) {
    return String(n).padStart(2, '0');
}

export function todayDateString(baseDate = new Date()) {
    return `${baseDate.getFullYear()}-${pad2(baseDate.getMonth() + 1)}-${pad2(baseDate.getDate())}`;
}

export function offsetDateString(dayOffset, baseDate = new Date()) {
    const d = new Date(baseDate.getFullYear(), baseDate.getMonth(), baseDate.getDate() + dayOffset);
    return todayDateString(d);
}

export function currentTimeString(baseDate = new Date()) {
    return `${pad2(baseDate.getHours())}:${pad2(baseDate.getMinutes())}:${pad2(baseDate.getSeconds())}`;
}

export function normalizeTimeWithSeconds(timeStr, fallback = '08:00:00') {
    if (!timeStr) return fallback;
    const parts = String(timeStr).trim().split(':');
    const hh = pad2(Number(parts[0] ?? 8));
    const mm = pad2(Number(parts[1] ?? 0));
    const ss = pad2(Number(parts[2] ?? 0));
    return `${hh}:${mm}:${ss}`;
}

export function shortTime(timeStr) {
    if (!timeStr) return '-';
    return String(timeStr).slice(0, 5);
}

export function parseLocalDate(dateStr) {
    if (!dateStr) return null;
    const [y, m, d] = String(dateStr).slice(0, 10).split('-').map(Number);
    if (!y || !m || !d) return null;
    return new Date(y, m - 1, d);
}

export function isoDayOfWeek(date = new Date()) {
    const day = date.getDay();
    return day === 0 ? 7 : day; // 1=Mon .. 7=Sun
}

export function daysInMonth(year, month1Based) {
    return new Date(year, month1Based, 0).getDate();
}

export function inclusiveDaysBetween(startStr, endStr) {
    const s = parseLocalDate(startStr);
    const e = parseLocalDate(endStr);
    if (!s || !e) return 0;
    const sUtc = Date.UTC(s.getFullYear(), s.getMonth(), s.getDate());
    const eUtc = Date.UTC(e.getFullYear(), e.getMonth(), e.getDate());
    return Math.max(0, Math.round((eUtc - sUtc) / 86400000) + 1);
}

export function formatHeaderDate(date = new Date()) {
    return `${DAYS_FULL[date.getDay()]}, ${pad2(date.getDate())} ${MONTHS_SHORT[date.getMonth()]} ${date.getFullYear()}`;
}

export function formatDayMonthYear(dateStr) {
    const d = parseLocalDate(dateStr);
    if (!d) return '-';
    return `${pad2(d.getDate())} ${MONTHS_SHORT[d.getMonth()]} ${d.getFullYear()}`;
}

export function formatWeekdayDayMonthYear(dateStr) {
    const d = parseLocalDate(dateStr);
    if (!d) return '-';
    return `${DAYS_SHORT[d.getDay()]}, ${pad2(d.getDate())} ${MONTHS_SHORT[d.getMonth()]} ${d.getFullYear()}`;
}

export function formatMonthDayYear(dateStr) {
    const d = parseLocalDate(dateStr);
    if (!d) return '-';
    return `${MONTHS_SHORT[d.getMonth()]} ${pad2(d.getDate())}, ${d.getFullYear()}`;
}

export function formatMonthDay(dateStr) {
    const d = parseLocalDate(dateStr);
    if (!d) return '-';
    return `${MONTHS_SHORT[d.getMonth()]} ${pad2(d.getDate())}`;
}

export function formatDateTimeDisplay(isoStr) {
    if (!isoStr) return '-';
    const d = new Date(isoStr);
    if (Number.isNaN(d.getTime())) return '-';
    return `${pad2(d.getDate())} ${MONTHS_SHORT[d.getMonth()]} ${d.getFullYear()} ${pad2(d.getHours())}:${pad2(d.getMinutes())}`;
}

export function formatTimeAmPm(isoStr) {
    if (!isoStr) return '';
    const d = new Date(isoStr);
    if (Number.isNaN(d.getTime())) return '';
    let h = d.getHours();
    const ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return `${pad2(h)}:${pad2(d.getMinutes())} ${ampm}`;
}

export function getWeekBounds(baseDate = new Date()) {
    const isoDay = isoDayOfWeek(baseDate);
    const start = new Date(baseDate.getFullYear(), baseDate.getMonth(), baseDate.getDate() - (isoDay - 1));
    const end = new Date(start.getFullYear(), start.getMonth(), start.getDate() + 6);
    return {
        start,
        end,
        startStr: todayDateString(start),
        endStr: todayDateString(end),
    };
}

export function getMonthBounds(year, month1Based) {
    const start = new Date(year, month1Based - 1, 1);
    const end = new Date(year, month1Based, 0);
    return {
        start,
        end,
        startStr: todayDateString(start),
        endStr: todayDateString(end),
        monthName: MONTHS_FULL[month1Based - 1],
    };
}

export const MONTH_OPTIONS = MONTHS_FULL.map((label, idx) => ({
    value: pad2(idx + 1),
    shortLabel: MONTHS_SHORT[idx],
    label,
}));
