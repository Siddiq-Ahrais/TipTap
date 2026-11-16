import { createSeedData } from '../data/seed.js';
import {
    currentTimeString,
    daysInMonth,
    inclusiveDaysBetween,
    isoDayOfWeek,
    normalizeTimeWithSeconds,
    offsetDateString,
    parseLocalDate,
    todayDateString,
} from '../utils/dates.js';

const STORAGE_KEY = 'tiptap_static_demo_v1';
const ADMIN_ROLES = new Set(['admin', 'administrator', 'superadmin', 'super admin', 'super_admin']);

const TESTER_GROUPS = [
    { prefix: 'Atester', count: 5, type: 'clockin' },
    { prefix: 'Btester', count: 5, type: 'clockout' },
    { prefix: 'Ctester', count: 5, type: 'early_clockout' },
    { prefix: 'Dtester', count: 2, type: 'late' },
    { prefix: 'Etester', count: 3, type: 'no_clockin' },
];

export function isAdminRole(role) {
    return ADMIN_ROLES.has(String(role ?? '').trim().toLowerCase());
}

export function isAlphaRole(role) {
    return String(role ?? '').trim().toLowerCase() === 'alpha';
}

export function brandNameFromSettings(settings) {
    const domain = String(settings?.company_email_domain || 'TipTap');
    const dotIndex = domain.lastIndexOf('.');
    return dotIndex > 0 ? domain.slice(0, dotIndex) : domain;
}

export function loadState() {
    try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (raw) {
            const parsed = JSON.parse(raw);
            if (parsed && Array.isArray(parsed.users) && parsed.settings) {
                return parsed;
            }
        }
    } catch {
        // Fallback to fresh seed if storage is unavailable or corrupted
    }
    const seeded = createSeedData();
    saveState(seeded);
    return seeded;
}

export function saveState(state) {
    try {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    } catch {
        // Ignore quota errors in memory-only / restricted storage contexts
    }
}

export function resetState() {
    const seeded = createSeedData();
    saveState(seeded);
    return seeded;
}

export function isContractExpired(user) {
    if (!user) return false;
    const status = String(user.status_pekerjaan ?? '').trim().toLowerCase();
    const permanent = ['tetap', 'permanent', 'karyawan tetap'];
    if (permanent.includes(status) || !user.tgl_habis_kontrak) return false;

    const today = new Date();
    const todayMidnight = new Date(today.getFullYear(), today.getMonth(), today.getDate());
    const contractEnd = parseLocalDate(user.tgl_habis_kontrak);
    return Boolean(contractEnd && todayMidnight > contractEnd);
}

export function getUsedLeaveDays(state, userId) {
    const currentYear = String(new Date().getFullYear());
    return state.leaves
        .filter(
            (leave) =>
                leave.user_id === userId &&
                String(leave.tanggal_mulai ?? '').startsWith(currentYear) &&
                ['approved', 'pending'].includes(String(leave.status_approval ?? '').toLowerCase())
        )
        .reduce((sum, leave) => sum + inclusiveDaysBetween(leave.tanggal_mulai, leave.tanggal_selesai), 0);
}

export function getRemainingLeaveDays(state, user) {
    if (!user) return 0;
    const quota = user.leave_quota ?? 8;
    return Math.max(0, quota - getUsedLeaveDays(state, user.id));
}

export function computeEmployeeDashboardStats(state, user) {
    const today = todayDateString();
    const now = new Date();
    const yearMonthPrefix = today.slice(0, 7);
    const workDays = state.settings.work_days ?? [1, 2, 3, 4, 5];
    const isTodayWorkDay = workDays.includes(isoDayOfWeek(now));

    const todayRecord = state.attendances.find((a) => a.user_id === user.id && a.tanggal === today) ?? null;

    let statusKey = 'not_yet_clocked_in';
    if (todayRecord) {
        if (todayRecord.waktu_keluar) {
            statusKey = 'checked_out';
        } else if (String(todayRecord.early_checkout_status ?? '').toLowerCase() === 'pending') {
            statusKey = 'pending';
        } else {
            const raw = String(todayRecord.status ?? 'checked_in').toLowerCase();
            if (raw === 'terlambat' || raw === 'late') statusKey = 'late';
            else if (raw === 'hadir' || raw === 'checked_in') statusKey = 'checked_in';
            else statusKey = 'checked_in';
        }
    } else if (!isTodayWorkDay) {
        statusKey = 'day_off';
    }

    const monthlyRecords = state.attendances.filter(
        (a) => a.user_id === user.id && a.waktu_masuk && String(a.tanggal ?? '').startsWith(yearMonthPrefix)
    );
    const monthlyAttendance = new Set(monthlyRecords.map((a) => a.tanggal)).size;
    const totalDaysInMonth = daysInMonth(now.getFullYear(), now.getMonth() + 1);
    const workingDaysTarget = Math.max(1, totalDaysInMonth - Math.floor(totalDaysInMonth / 7) * 2);
    const lateDays = monthlyRecords.filter((a) => String(a.status ?? '').toLowerCase() === 'terlambat').length;
    const onTimeDays = monthlyRecords.length - lateDays;
    const punctualityPct = monthlyRecords.length === 0 ? 0 : Math.round((onTimeDays / monthlyRecords.length) * 100);

    const totalQuota = user.leave_quota ?? 8;
    const usedLeave = getUsedLeaveDays(state, user.id);
    const remainingLeave = Math.max(0, totalQuota - usedLeave);

    const recentRows = [...state.attendances]
        .filter((a) => a.user_id === user.id)
        .sort((a, b) => b.tanggal.localeCompare(a.tanggal))
        .slice(0, 3);

    return {
        isTodayWorkDay,
        todayRecord,
        statusKey,
        canClockIn: isTodayWorkDay && ['not_yet_clocked_in', 'absent'].includes(statusKey),
        canAttemptClockOut: ['checked_in', 'late'].includes(statusKey),
        requiresClockOutApproval: !isAdminRole(user.role),
        metrics: [
            {
                label: 'This Month Attendance',
                value: `${monthlyAttendance} Days`,
                caption: `Target ${workingDaysTarget} working days`,
                trend:
                    monthlyAttendance > 0
                        ? `${Math.round((monthlyAttendance / workingDaysTarget) * 100)}% achieved`
                        : 'No clock-in yet',
                trendDirection: monthlyAttendance > 0 ? 'up' : 'neutral',
            },
            {
                label: 'Leave Balance',
                value: `${remainingLeave} Days`,
                caption: `of ${totalQuota} days · ${usedLeave} used`,
                trend: remainingLeave >= totalQuota / 2 ? 'On track' : remainingLeave > 0 ? 'Low balance' : 'No quota left',
                trendDirection: remainingLeave >= totalQuota / 2 ? 'neutral' : 'down',
            },
            {
                label: 'Punctuality Score',
                value: `${punctualityPct}%`,
                caption: 'On-time arrival performance',
                trend: monthlyRecords.length === 0 ? 'No data yet' : `${onTimeDays} on-time, ${lateDays} late`,
                trendDirection: lateDays === 0 ? 'up' : 'down',
            },
        ],
        recentRows,
    };
}

export function computeAdminStats(state) {
    const today = todayDateString();
    const yearMonthPrefix = today.slice(0, 7);

    const approvedEmployees = state.users.filter((u) => u.is_approved && !isAlphaRole(u.role));
    const pendingUsers = state.users
        .filter((u) => !u.is_approved)
        .sort((a, b) => String(a.created_at).localeCompare(String(b.created_at)));

    const pendingEarlyCheckouts = state.attendances
        .filter((a) => a.early_checkout_status === 'pending')
        .sort((a, b) => String(b.early_checkout_requested_at ?? '').localeCompare(String(a.early_checkout_requested_at ?? '')));

    const pendingLeaves = state.leaves
        .filter((l) => String(l.status_approval ?? '').toLowerCase() === 'pending')
        .sort((a, b) => String(b.created_at ?? '').localeCompare(String(a.created_at ?? '')));

    const todayAttendances = state.attendances
        .filter((a) => a.tanggal === today && a.waktu_masuk)
        .sort((a, b) => String(a.waktu_masuk).localeCompare(String(b.waktu_masuk)));

    const monthlyAttendances = state.attendances.filter(
        (a) => a.waktu_masuk && String(a.tanggal ?? '').startsWith(yearMonthPrefix)
    );
    const presentCount = monthlyAttendances.filter((a) => String(a.status ?? '').toLowerCase() !== 'terlambat').length;
    const lateCount = monthlyAttendances.filter((a) => String(a.status ?? '').toLowerCase() === 'terlambat').length;
    const onLeaveCount = state.leaves.filter(
        (l) => String(l.status_approval ?? '').toLowerCase() === 'approved'
    ).length;
    const clockedInTodayIds = new Set(todayAttendances.map((a) => a.user_id));
    const absentTodayCount = Math.max(
        0,
        approvedEmployees.filter((u) => !isAdminRole(u.role) && !clockedInTodayIds.has(u.id)).length
    );

    return {
        totalEmployeeCount: state.users.filter((u) => u.is_approved).length,
        clockedInTodayCount: clockedInTodayIds.size,
        pendingUsers,
        pendingEarlyCheckouts,
        pendingLeaves,
        todayAttendances,
        summaryCards: [
            { label: 'Present', value: presentCount, helper: 'On-time records this month', icon: 'check', accent: 'border-[#11A582]' },
            { label: 'Late', value: lateCount, helper: 'Late arrivals this month', icon: 'clock', accent: 'border-[#FF6B6B]' },
            { label: 'On Leave', value: onLeaveCount, helper: 'Approved leave records', icon: 'calendar', accent: 'border-[#FBB117]' },
            { label: 'Absent', value: absentTodayCount, helper: 'Employees not yet clocked in today', icon: 'warning', accent: 'border-gray-500' },
        ],
    };
}

export function performLogin(state, { email, password, loginType }) {
    const cleanEmail = String(email ?? '').trim().toLowerCase();
    const user = state.users.find((u) => u.email.toLowerCase() === cleanEmail);

    if (!user || user.password !== password) {
        return { ok: false, errors: { email: 'These credentials do not match our records.' } };
    }
    if (!user.is_approved) {
        return { ok: false, errors: { email: 'Account pending Admin approval.' } };
    }
    if (loginType === 'employee' && (isAdminRole(user.role) || isAlphaRole(user.role))) {
        return { ok: false, errors: { email: 'Admin accounts must use the Admin Login page.' } };
    }
    if (loginType === 'admin' && !isAdminRole(user.role) && !isAlphaRole(user.role)) {
        return { ok: false, errors: { email: 'Employee accounts must use the Employee Login page.' } };
    }

    state.currentUserId = user.id;
    saveState(state);
    return {
        ok: true,
        user,
        redirect: isAlphaRole(user.role) ? '/alpha/settings' : '/dashboard',
    };
}

export function performRegister(state, { name, username, password, confirmation }) {
    const cleanName = String(name ?? '').trim();
    const cleanUsername = String(username ?? '').trim().replace(/@/g, '').toLowerCase();
    const errors = {};

    if (!cleanName) errors.name = 'Name is required.';
    if (!cleanUsername || !/^[a-zA-Z0-9._-]+$/.test(cleanUsername)) {
        errors.username = 'Username may only contain letters, numbers, dots, hyphens, and underscores.';
    }
    if (!password || password.length < 8) {
        errors.password = 'Password must be at least 8 characters.';
    }
    if (password !== confirmation) {
        errors.password_confirmation = 'Password confirmation does not match.';
    }

    const email = `${cleanUsername}@${state.settings.company_email_domain}`;
    if (state.users.some((u) => u.email.toLowerCase() === email)) {
        errors.email = 'This company email is already registered.';
    }

    if (Object.keys(errors).length > 0) {
        return { ok: false, errors };
    }

    const newUser = {
        id: state.nextIds.user++,
        name: cleanName,
        email,
        password,
        role: 'user',
        divisi: 'Operations',
        status_pekerjaan: 'kontrak',
        tgl_habis_kontrak: offsetDateString(180),
        is_approved: false,
        leave_quota: 8,
        created_at: new Date().toISOString(),
    };

    state.users.push(newUser);
    saveState(state);
    return { ok: true, user: newUser, message: 'Registration successful. Awaiting admin approval.' };
}

export function performClockIn(state, user) {
    if (isContractExpired(user)) {
        return { ok: false, error: 'Contract has expired. Attendance access is blocked.' };
    }

    const now = new Date();
    const today = todayDateString(now);
    const workDays = state.settings.work_days ?? [1, 2, 3, 4, 5];

    if (!workDays.includes(isoDayOfWeek(now))) {
        return { ok: false, error: 'Today is not a scheduled work day. Clock-in is not required.' };
    }

    if (state.attendances.some((a) => a.user_id === user.id && a.tanggal === today)) {
        return { ok: false, error: 'User has already clocked in today.' };
    }

    const nowTime = currentTimeString(now);
    const officeStart = normalizeTimeWithSeconds(state.settings.jam_masuk_kantor, '08:00:00');
    const status = nowTime > officeStart ? 'Terlambat' : 'Hadir';

    state.attendances.push({
        id: state.nextIds.attendance++,
        user_id: user.id,
        tanggal: today,
        waktu_masuk: nowTime,
        waktu_keluar: null,
        status,
        early_checkout_status: null,
        early_checkout_requested_at: null,
        early_checkout_reviewed_at: null,
        early_checkout_reviewed_by: null,
        early_checkout_note: null,
    });
    saveState(state);
    return { ok: true, message: `Clock-in successful at ${nowTime} (${status === 'Terlambat' ? 'Late' : 'On Time'}).` };
}

export function performClockOut(state, user) {
    if (isContractExpired(user)) {
        return { ok: false, error: 'Contract has expired. Attendance access is blocked.' };
    }

    const now = new Date();
    const today = todayDateString(now);
    const attendance = state.attendances.find((a) => a.user_id === user.id && a.tanggal === today);

    if (!attendance) return { ok: false, error: 'No active clock-in record found for today.' };
    if (attendance.waktu_keluar) return { ok: false, error: 'User has already clocked out.' };

    if (isAdminRole(user.role)) {
        attendance.waktu_keluar = currentTimeString(now);
        attendance.early_checkout_status = null;
        attendance.early_checkout_requested_at = null;
        saveState(state);
        return { ok: true, message: 'Clock-out successful.' };
    }

    if (attendance.early_checkout_status === 'pending') {
        return { ok: false, error: 'Your clock-out request is already waiting for admin approval.' };
    }
    if (attendance.early_checkout_status === 'approved') {
        return { ok: false, error: 'Your clock-out request has already been approved.' };
    }

    attendance.early_checkout_status = 'pending';
    attendance.early_checkout_requested_at = now.toISOString();
    attendance.early_checkout_reviewed_at = null;
    attendance.early_checkout_reviewed_by = null;
    attendance.early_checkout_note = null;
    saveState(state);

    return { ok: true, message: 'Clock-out request submitted. Please wait for admin approval.' };
}

export function performSubmitLeave(state, user, form) {
    const errors = [];
    const today = todayDateString();
    const allowedTypes = ['Sakit', 'Cuti Tahunan', 'Keperluan Mendadak'];

    if (!allowedTypes.includes(form.jenis_izin)) errors.push('Please select a valid leave type.');
    if (!form.tanggal_mulai || form.tanggal_mulai < today) errors.push('Start date must be today or later.');
    if (!form.tanggal_selesai || form.tanggal_selesai < form.tanggal_mulai) {
        errors.push('The end date must be greater than or equal to the start date.');
    }
    if (!String(form.alasan ?? '').trim()) errors.push('Please provide a reason for your absence.');
    if (form.jenis_izin === 'Sakit' && !form.attachment) {
        errors.push('Supporting document is required when sick (Sakit).');
    }

    const requestedDays = inclusiveDaysBetween(form.tanggal_mulai, form.tanggal_selesai);
    const remaining = getRemainingLeaveDays(state, user);
    if (form.tanggal_mulai && form.tanggal_selesai && requestedDays > remaining) {
        errors.push(`Not enough leave quota. You are requesting ${requestedDays} day(s) but only have ${remaining} day(s) remaining.`);
    }

    if (errors.length > 0) return { ok: false, errors };

    state.leaves.unshift({
        id: state.nextIds.leave++,
        user_id: user.id,
        jenis_izin: form.jenis_izin,
        tanggal_mulai: form.tanggal_mulai,
        tanggal_selesai: form.tanggal_selesai,
        alasan: String(form.alasan).trim(),
        bukti_file: form.attachment ?? null,
        status_approval: 'Pending',
        rejection_reason: null,
        created_at: new Date().toISOString(),
    });
    saveState(state);
    return { ok: true, message: 'Leave application submitted successfully.' };
}

export function performUpdateSettings(state, payload) {
    let domain = String(payload.company_email_domain ?? '').trim().toLowerCase();
    if (domain.includes('@')) domain = domain.split('@')[1];

    if (!/^[a-z0-9]+([.-][a-z0-9]+)*\.[a-z]{2,}$/.test(domain)) {
        return { ok: false, error: 'Use a valid company email domain like tiptap.id.' };
    }
    if (!payload.jam_masuk_kantor || !payload.jam_mulai_pulang) {
        return { ok: false, error: 'Standard clock-in and clock-out times are required.' };
    }

    const workDays = Array.isArray(payload.work_days) && payload.work_days.length > 0
        ? [...new Set(payload.work_days.map(Number).filter((n) => n >= 1 && n <= 7))].sort((a, b) => a - b)
        : state.settings.work_days ?? [1, 2, 3, 4, 5];

    state.settings = {
        company_email_domain: domain,
        jam_masuk_kantor: String(payload.jam_masuk_kantor).slice(0, 5),
        jam_mulai_pulang: String(payload.jam_mulai_pulang).slice(0, 5),
        work_days: workDays,
    };
    saveState(state);
    return { ok: true, message: 'Company settings updated successfully.' };
}

export function performGenerateTesters(state, daysInput) {
    const days = Number(daysInput);
    if (!Number.isInteger(days) || days < 1 || days > 365) {
        return { ok: false, error: 'Number of days must be between 1 and 365.' };
    }

    let createdUsers = 0;
    let createdRecords = 0;
    const today = todayDateString();
    const [inH, inM] = (state.settings.jam_masuk_kantor || '08:00').split(':').map(Number);
    const [outH, outM] = (state.settings.jam_mulai_pulang || '17:00').split(':').map(Number);
    const fmtSec = (totalMin) => {
        const clamped = ((totalMin % 1440) + 1440) % 1440;
        return `${String(Math.floor(clamped / 60)).padStart(2, '0')}:${String(clamped % 60).padStart(2, '0')}:00`;
    };
    const inBase = inH * 60 + inM;
    const outBase = outH * 60 + outM;

    for (const group of TESTER_GROUPS) {
        for (let i = 1; i <= group.count; i++) {
            const name = `${group.prefix}${i}`;
            const email = `${name.toLowerCase()}@test.dev`;
            let user = state.users.find((u) => u.email === email);

            if (!user) {
                user = {
                    id: state.nextIds.user++,
                    name,
                    email,
                    password: 'tester123',
                    role: 'user',
                    divisi: 'Testing',
                    status_pekerjaan: 'tetap',
                    tgl_habis_kontrak: null,
                    is_approved: true,
                    leave_quota: 8,
                    created_at: new Date().toISOString(),
                };
                state.users.push(user);
                createdUsers++;
            }

            if (group.type === 'no_clockin') continue;

            for (let d = 0; d < days; d++) {
                const date = offsetDateString(-d);
                if (state.attendances.some((a) => a.user_id === user.id && a.tanggal === date)) continue;

                const jitter = ((i * 7 + d * 3) % 11) - 5;
                let record;
                if (group.type === 'clockin') {
                    record = {
                        waktu_masuk: fmtSec(inBase - Math.abs(jitter)),
                        waktu_keluar: date === today ? null : fmtSec(outBase + Math.abs(jitter)),
                        status: 'Hadir',
                        early_checkout_status: null,
                    };
                } else if (group.type === 'clockout') {
                    record = {
                        waktu_masuk: fmtSec(inBase - Math.abs(jitter)),
                        waktu_keluar: fmtSec(outBase + Math.abs(jitter) + 5),
                        status: 'Hadir',
                        early_checkout_status: null,
                    };
                } else if (group.type === 'early_clockout') {
                    const earlyMin = outBase - 90;
                    record = {
                        waktu_masuk: fmtSec(inBase - 3),
                        waktu_keluar: fmtSec(earlyMin),
                        status: 'Pulang Cepat',
                        early_checkout_status: 'approved',
                        early_checkout_requested_at: `${date}T${fmtSec(earlyMin)}`,
                        early_checkout_reviewed_at: `${date}T${fmtSec(earlyMin + 10)}`,
                    };
                } else {
                    record = {
                        waktu_masuk: fmtSec(inBase + 25 + Math.abs(jitter)),
                        waktu_keluar: fmtSec(outBase + 10),
                        status: 'Terlambat',
                        early_checkout_status: null,
                    };
                }

                state.attendances.push({
                    id: state.nextIds.attendance++,
                    user_id: user.id,
                    tanggal: date,
                    early_checkout_requested_at: null,
                    early_checkout_reviewed_at: null,
                    early_checkout_reviewed_by: null,
                    early_checkout_note: null,
                    ...record,
                });
                createdRecords++;
            }
        }
    }

    saveState(state);
    return {
        ok: true,
        message: `Dev Tools: ${createdUsers} tester account(s) created, ${createdRecords} attendance record(s) generated for ${days} day(s).`,
    };
}

export function getTesterAccountsSnapshot(state) {
    const typeLabels = {
        clockin: { label: 'Clock In (Present)', color: 'bg-emerald-100 text-emerald-700', desc: 'Clocked in on time (A1-5)' },
        clockout: { label: 'Checked Out', color: 'bg-slate-100 text-slate-600', desc: 'Clocked in & out normally (B1-5)' },
        early_clockout: { label: 'Early Checkout', color: 'bg-orange-100 text-orange-700', desc: 'Early checkout approved (C1-5)' },
        late: { label: 'Late', color: 'bg-rose-100 text-rose-700', desc: 'Late arrivals (D1-2)' },
        no_clockin: { label: 'No Clock In', color: 'bg-amber-100 text-amber-700', desc: 'No attendance records (E1-3)' },
    };

    const accounts = [];
    for (const group of TESTER_GROUPS) {
        for (let i = 1; i <= group.count; i++) {
            const name = `${group.prefix}${i}`;
            const email = `${name.toLowerCase()}@test.dev`;
            const user = state.users.find((u) => u.email === email);
            accounts.push({
                name,
                email,
                type: group.type,
                exists: Boolean(user),
                attendance_days: user ? state.attendances.filter((a) => a.user_id === user.id).length : 0,
                meta: typeLabels[group.type],
            });
        }
    }

    return { accounts, typeLabels };
}

export function performResetTesters(state) {
    const testerEmails = new Set();
    for (const group of TESTER_GROUPS) {
        for (let i = 1; i <= group.count; i++) {
            testerEmails.add(`${group.prefix.toLowerCase()}${i}@test.dev`);
        }
    }

    const testerIds = new Set(state.users.filter((u) => testerEmails.has(u.email)).map((u) => u.id));
    const prevUsers = state.users.length;
    const prevAtt = state.attendances.length;

    state.users = state.users.filter((u) => !testerIds.has(u.id));
    state.attendances = state.attendances.filter((a) => !testerIds.has(a.user_id));
    saveState(state);

    return {
        ok: true,
        message: `Dev Tools reset: ${prevUsers - state.users.length} tester account(s) and ${prevAtt - state.attendances.length} attendance record(s) deleted.`,
    };
}
