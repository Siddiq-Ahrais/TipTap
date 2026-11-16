import { offsetDateString, todayDateString } from '../utils/dates.js';

/**
 * Generates initial demo state relative to the viewer's current local date
 * so attendance, approvals, metrics, and exports feel realistic immediately.
 */
export function createSeedData() {
    const today = todayDateString();
    const now = Date.now();
    const hoursAgo = (h) => new Date(now - h * 3600 * 1000).toISOString();

    const users = [
        {
            id: 1,
            name: 'Super Admin',
            email: 'superadmin@tiptap.com',
            password: 'password123',
            role: 'superadmin',
            divisi: 'Management',
            status_pekerjaan: 'tetap',
            tgl_habis_kontrak: null,
            is_approved: true,
            leave_quota: 12,
            created_at: hoursAgo(720),
        },
        {
            id: 2,
            name: 'Alpha',
            email: 'alpha@tiptap.id',
            password: 'password123',
            role: 'alpha',
            divisi: 'Development',
            status_pekerjaan: 'tetap',
            tgl_habis_kontrak: null,
            is_approved: true,
            leave_quota: 8,
            created_at: hoursAgo(700),
        },
        {
            id: 3,
            name: 'Budi Santoso',
            email: 'budi.santoso@tiptap.id',
            password: 'password123',
            role: 'user',
            divisi: 'Engineering',
            status_pekerjaan: 'tetap',
            tgl_habis_kontrak: null,
            is_approved: true,
            leave_quota: 8,
            created_at: hoursAgo(600),
        },
        {
            id: 4,
            name: 'Siti Rahma',
            email: 'siti.rahma@tiptap.id',
            password: 'password123',
            role: 'user',
            divisi: 'Product',
            status_pekerjaan: 'tetap',
            tgl_habis_kontrak: null,
            is_approved: true,
            leave_quota: 8,
            created_at: hoursAgo(580),
        },
        {
            id: 5,
            name: 'Andi Wijaya',
            email: 'andi.wijaya@tiptap.id',
            password: 'password123',
            role: 'user',
            divisi: 'Operations',
            status_pekerjaan: 'kontrak',
            tgl_habis_kontrak: offsetDateString(120),
            is_approved: true,
            leave_quota: 8,
            created_at: hoursAgo(500),
        },
        {
            id: 6,
            name: 'Dewi Lestari',
            email: 'dewi.lestari@tiptap.id',
            password: 'password123',
            role: 'user',
            divisi: 'Marketing',
            status_pekerjaan: 'tetap',
            tgl_habis_kontrak: null,
            is_approved: true,
            leave_quota: 8,
            created_at: hoursAgo(450),
        },
        {
            id: 7,
            name: 'Nadia Putri',
            email: 'nadia.putri@tiptap.id',
            password: 'password123',
            role: 'user',
            divisi: 'Finance',
            status_pekerjaan: 'kontrak',
            tgl_habis_kontrak: offsetDateString(180),
            is_approved: false,
            leave_quota: 8,
            created_at: hoursAgo(3),
        },
        {
            id: 8,
            name: 'Rafi Mahendra',
            email: 'rafi.mahendra@tiptap.id',
            password: 'password123',
            role: 'user',
            divisi: 'Product',
            status_pekerjaan: 'kontrak',
            tgl_habis_kontrak: offsetDateString(180),
            is_approved: false,
            leave_quota: 8,
            created_at: hoursAgo(7),
        },
        {
            id: 9,
            name: 'Gina Ananta',
            email: 'gina.ananta@tiptap.id',
            password: 'password123',
            role: 'user',
            divisi: 'Operations',
            status_pekerjaan: 'kontrak',
            tgl_habis_kontrak: offsetDateString(180),
            is_approved: false,
            leave_quota: 8,
            created_at: hoursAgo(24),
        },
    ];

    // Budi (id 3) intentionally has NO record today so presenter can demonstrate Clock In -> Clock Out live!
    const budiHistory = [
        [-1, '07:53:12', '17:04:45', 'Hadir', null],
        [-2, '07:48:30', '17:12:09', 'Hadir', null],
        [-3, '08:11:04', '17:06:18', 'Terlambat', null],
        [-4, '07:55:41', '17:01:22', 'Hadir', null],
        [-5, '07:58:19', '15:30:00', 'Pulang Cepat', 'approved'],
        [-6, '07:49:05', '17:09:41', 'Hadir', null],
        [-7, '07:56:20', '17:02:11', 'Hadir', null],
        [-8, '08:06:44', '17:15:30', 'Terlambat', null],
        [-9, '07:50:11', '17:00:55', 'Hadir', null],
        [-10, '07:47:50', '17:08:12', 'Hadir', null],
        [-11, '07:59:02', '17:03:20', 'Hadir', null],
        [-12, '07:54:16', '17:05:09', 'Hadir', null],
    ].map(([offset, waktu_masuk, waktu_keluar, status, earlyStatus], idx) => ({
        id: idx + 1,
        user_id: 3,
        tanggal: offsetDateString(offset),
        waktu_masuk,
        waktu_keluar,
        status,
        early_checkout_status: earlyStatus,
        early_checkout_requested_at: earlyStatus ? `${offsetDateString(offset)}T15:30:00` : null,
        early_checkout_reviewed_at: earlyStatus ? `${offsetDateString(offset)}T15:35:00` : null,
        early_checkout_reviewed_by: earlyStatus ? 1 : null,
        early_checkout_note: null,
    }));

    const todayAttendances = [
        {
            id: 101,
            user_id: 6, // Dewi
            tanggal: today,
            waktu_masuk: '07:45:10',
            waktu_keluar: '17:05:22',
            status: 'Hadir',
            early_checkout_status: null,
            early_checkout_requested_at: null,
            early_checkout_reviewed_at: null,
            early_checkout_reviewed_by: null,
            early_checkout_note: null,
        },
        {
            id: 102,
            user_id: 4, // Siti
            tanggal: today,
            waktu_masuk: '07:52:14',
            waktu_keluar: null,
            status: 'Hadir',
            early_checkout_status: null,
            early_checkout_requested_at: null,
            early_checkout_reviewed_at: null,
            early_checkout_reviewed_by: null,
            early_checkout_note: null,
        },
        {
            id: 103,
            user_id: 5, // Andi — pending early checkout for Admin Approval queue
            tanggal: today,
            waktu_masuk: '08:24:09',
            waktu_keluar: null,
            status: 'Terlambat',
            early_checkout_status: 'pending',
            early_checkout_requested_at: hoursAgo(1),
            early_checkout_reviewed_at: null,
            early_checkout_reviewed_by: null,
            early_checkout_note: null,
        },
    ];

    const leaves = [
        {
            id: 1,
            user_id: 3, // Budi past approved leave (2 days used of 8)
            jenis_izin: 'Cuti Tahunan',
            tanggal_mulai: offsetDateString(-20),
            tanggal_selesai: offsetDateString(-19),
            alasan: 'Family gathering in Bandung.',
            bukti_file: null,
            status_approval: 'Approved',
            rejection_reason: null,
            created_at: hoursAgo(500),
        },
        {
            id: 2,
            user_id: 3, // Budi past rejected leave
            jenis_izin: 'Keperluan Mendadak',
            tanggal_mulai: offsetDateString(-14),
            tanggal_selesai: offsetDateString(-14),
            alasan: 'Personal errand during sprint review.',
            bukti_file: null,
            status_approval: 'Rejected',
            rejection_reason: 'Critical sprint release day; please reschedule errand after 16:00.',
            created_at: hoursAgo(360),
        },
        {
            id: 3,
            user_id: 4, // Siti pending leave for Admin Review
            jenis_izin: 'Cuti Tahunan',
            tanggal_mulai: offsetDateString(2),
            tanggal_selesai: offsetDateString(4),
            alasan: 'Family event outside the city.',
            bukti_file: null,
            status_approval: 'Pending',
            rejection_reason: null,
            created_at: hoursAgo(5),
        },
        {
            id: 4,
            user_id: 6, // Dewi pending sick leave with sample SVG medical certificate preview
            jenis_izin: 'Sakit',
            tanggal_mulai: offsetDateString(1),
            tanggal_selesai: offsetDateString(2),
            alasan: 'Seasonal fever; doctor advised two days of rest.',
            bukti_file: {
                name: 'medical_certificate_dewi.png',
                type: 'image/svg+xml',
                size: 18420,
                previewUrl:
                    'data:image/svg+xml;utf8,' +
                    encodeURIComponent(
                        '<svg xmlns="http://www.w3.org/2000/svg" width="520" height="320" viewBox="0 0 520 320">' +
                            '<rect width="520" height="320" rx="16" fill="#F8FAFC" stroke="#CBD5E1" stroke-width="2"/>' +
                            '<rect x="24" y="24" width="472" height="56" rx="10" fill="#0B4A85"/>' +
                            '<text x="44" y="58" font-family="sans-serif" font-size="18" font-weight="bold" fill="#FFFFFF">CLINIC MEDICAL CERTIFICATE (DEMO)</text>' +
                            '<text x="36" y="120" font-family="sans-serif" font-size="15" fill="#1E293B">Patient: Dewi Lestari (Marketing)</text>' +
                            '<text x="36" y="152" font-family="sans-serif" font-size="14" fill="#475569">Recommendation: 2 days of rest for recovery.</text>' +
                            '<text x="36" y="184" font-family="sans-serif" font-size="14" fill="#475569">Attending Physician: Dr. Hendra Wijaya</text>' +
                            '<rect x="340" y="220" width="140" height="60" rx="8" fill="#E7EFF6" stroke="#0B4A85" stroke-dasharray="4 4"/>' +
                            '<text x="362" y="255" font-family="sans-serif" font-size="13" font-weight="bold" fill="#0B4A85">VERIFIED CLINIC</text>' +
                        '</svg>'
                    ),
            },
            status_approval: 'Pending',
            rejection_reason: null,
            created_at: hoursAgo(2),
        },
    ];

    const posts = [
        {
            id: 1,
            judul: 'Q4 Attendance & Early Checkout Policy Reminder',
            deskripsi:
                'Employees requesting to leave before 17:00 must submit an Early Checkout request from the dashboard so supervisors and HR can approve the record before shift completion.',
            created_at: hoursAgo(48),
        },
        {
            id: 2,
            judul: 'Scheduled Office Hours & Work Days',
            deskripsi:
                'Standard office hours remain 08:00 - 17:00 (Monday through Friday). Clock-ins after 08:00 are automatically flagged as Late in attendance analytics.',
            created_at: hoursAgo(120),
        },
        {
            id: 3,
            judul: 'Medical Certificate Requirement for Sick Leave',
            deskripsi:
                'When applying for Sick Leave (Sakit), please attach a clear JPG, PNG, or PDF medical certificate (up to 5MB) to speed up approval.',
            created_at: hoursAgo(240),
        },
    ];

    return {
        settings: {
            company_email_domain: 'tiptap.id',
            jam_masuk_kantor: '08:00',
            jam_mulai_pulang: '17:00',
            work_days: [1, 2, 3, 4, 5],
        },
        users,
        attendances: [...todayAttendances, ...budiHistory],
        leaves,
        posts,
        currentUserId: null,
        nextIds: {
            user: 10,
            attendance: 200,
            leave: 10,
            post: 10,
        },
    };
}
