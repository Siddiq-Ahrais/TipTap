import {
    brandNameFromSettings,
    computeAdminStats,
    computeEmployeeDashboardStats,
    getRemainingLeaveDays,
    getTesterAccountsSnapshot,
    getUsedLeaveDays,
    isAdminRole,
    isAlphaRole,
    loadState,
    performClockIn,
    performClockOut,
    performGenerateTesters,
    performLogin,
    performRegister,
    performResetTesters,
    performSubmitLeave,
    performUpdateSettings,
    resetState,
    saveState,
} from './store/tiptapStore.js';
import {
    MONTH_OPTIONS,
    formatDateTimeDisplay,
    formatDayMonthYear,
    formatHeaderDate,
    formatMonthDay,
    formatMonthDayYear,
    formatTimeAmPm,
    formatWeekdayDayMonthYear,
    inclusiveDaysBetween,
    todayDateString,
} from './utils/dates.js';
import { exportAttendanceReport, formatEmployeeCode } from './utils/exportExcel.js';

const STATUS_META = {
    day_off: {
        label: 'Day Off',
        tone: 'sky',
        description: 'Today is not a scheduled work day. Enjoy your day off!',
    },
    checked_in: {
        label: 'Checked In',
        tone: 'emerald',
        description: 'You are currently clocked in. Use clock out when your shift ends.',
    },
    late: {
        label: 'Late',
        tone: 'rose',
        description: 'You checked in late today. Please keep your team informed.',
    },
    checked_out: {
        label: 'Checked Out',
        tone: 'slate',
        description: 'Attendance is complete for today. Great job wrapping up on time.',
    },
    pending: {
        label: 'Pending',
        tone: 'amber',
        description: 'Your attendance is pending or currently marked as leave for review.',
    },
    absent: {
        label: 'Absent',
        tone: 'rose',
        description: 'No attendance record found for today. Please check with your supervisor.',
    },
    not_yet_clocked_in: {
        label: 'Not Yet Clocked In',
        tone: 'amber',
        description: 'Start your workday by clocking in when your shift begins.',
    },
};

const WORK_DAY_OPTIONS = [
    { num: 1, name: 'Monday' },
    { num: 2, name: 'Tuesday' },
    { num: 3, name: 'Wednesday' },
    { num: 4, name: 'Thursday' },
    { num: 5, name: 'Friday' },
    { num: 6, name: 'Saturday' },
    { num: 7, name: 'Sunday' },
];

const GUEST_ROUTES = new Set(['home', 'about', 'login', 'adminLogin', 'register', 'forgotPassword']);

function matchRoute(rawPath) {
    const clean = (rawPath || '/').replace(/\/+$/, '') || '/';
    const postEdit = clean.match(/^\/posts\/(\d+)\/edit$/);
    if (postEdit) return { name: 'postsEdit', path: clean, id: Number(postEdit[1]) };

    const postShow = clean.match(/^\/posts\/(\d+)$/);
    if (postShow) return { name: 'postsShow', path: clean, id: Number(postShow[1]) };

    const map = {
        '/': 'home',
        '/about': 'about',
        '/login': 'login',
        '/admin/login': 'adminLogin',
        '/register': 'register',
        '/forgot-password': 'forgotPassword',
        '/dashboard': 'dashboard',
        '/attendance/history': 'attendanceHistory',
        '/leaves': 'leavesIndex',
        '/leaves/create': 'leavesCreate',
        '/approval': 'approvalIndex',
        '/approval/registrations': 'approvalRegistrations',
        '/approval/early-checkouts': 'approvalEarlyCheckouts',
        '/approval/leaves': 'approvalLeaves',
        '/approval/settings': 'approvalSettings',
        '/posts': 'postsIndex',
        '/posts/create': 'postsCreate',
        '/profile': 'profile',
        '/alpha/settings': 'alphaSettings',
        '/alpha/devtools': 'alphaDevtools',
    };

    return { name: map[clean] ?? 'home', path: map[clean] ? clean : '/' };
}

export function createTipTapApp() {
    const initialState = loadState();
    const now = new Date();
    const currYear = now.getFullYear();

    return {
        state: initialState,
        route: matchRoute(window.location.pathname),
        flashStatus: '',
        flashError: '',

        monthOptions: MONTH_OPTIONS,
        yearOptions: [currYear - 1, currYear, currYear + 1],
        workDayOptions: WORK_DAY_OPTIONS,

        payrollExport: {
            month: String(now.getMonth() + 1).padStart(2, '0'),
            year: String(currYear),
        },

        loginForm: { email: '', password: '', showPassword: false, errors: {} },
        adminLoginForm: { email: '', password: '', showPassword: false, errors: {} },
        registerForm: { name: '', username: '', password: '', confirmation: '', errors: {} },
        forgotForm: { email: '', sentMessage: '' },

        attendancePagination: { page: 1, perPage: 10 },
        approvalTodayPagination: { page: 1, perPage: 5 },
        registrationSearch: '',

        leaveFilterStatus: '',
        expandedLeaveId: null,
        leaveForm: {
            jenis_izin: '',
            tanggal_mulai: '',
            tanggal_selesai: '',
            alasan: '',
            attachment: null,
            dragging: false,
            errors: [],
        },

        leaveReviewModal: { open: false, leave: null, note: '' },
        settingsForm: {
            company_email_domain: initialState.settings.company_email_domain,
            jam_masuk_kantor: initialState.settings.jam_masuk_kantor,
            jam_mulai_pulang: initialState.settings.jam_mulai_pulang,
            work_days: [...(initialState.settings.work_days ?? [1, 2, 3, 4, 5])],
        },

        postForm: { judul: '', deskripsi: '' },
        profileForm: { name: '', email: '', saved: false },
        passwordForm: { current_password: '', password: '', password_confirmation: '', error: '', saved: false },
        deleteAccountForm: { password: '', error: '' },
        alphaDaysInput: 10,

        init() {
            window.addEventListener('popstate', () => {
                this.applyRoute(window.location.pathname, false);
            });
            this.applyRoute(window.location.pathname, true);
        },

        get currentUser() {
            if (!this.state.currentUserId) return null;
            return this.state.users.find((u) => u.id === this.state.currentUserId) ?? null;
        },

        isAdmin() {
            return Boolean(this.currentUser && isAdminRole(this.currentUser.role));
        },

        isAlpha() {
            return Boolean(this.currentUser && isAlphaRole(this.currentUser.role));
        },

        isGuestLayout() {
            return GUEST_ROUTES.has(this.route.name) || !this.currentUser;
        },

        brandName() {
            return brandNameFromSettings(this.state.settings);
        },

        todayDate() {
            return todayDateString();
        },

        todayHeaderLabel() {
            return formatHeaderDate(new Date());
        },

        guestNavItems() {
            return [
                { label: 'Home', route: 'home', path: '/' },
                { label: 'About', route: 'about', path: '/about' },
                { label: 'Login', route: 'login', path: '/login' },
                { label: 'Register', route: 'register', path: '/register' },
            ];
        },

        primarySidebarItems() {
            if (this.isAlpha()) {
                return [
                    { label: 'System Config', path: '/alpha/settings', prefixes: ['/alpha/settings'] },
                    { label: 'Dev Tools', path: '/alpha/devtools', prefixes: ['/alpha/devtools'] },
                ];
            }
            const items = [
                { label: 'Dashboard', path: '/dashboard', prefixes: ['/dashboard', '/attendance'] },
            ];
            if (this.isAdmin()) {
                items.push({
                    label: 'Approval',
                    path: '/approval',
                    prefixes: ['/approval'],
                    excludePrefixes: ['/approval/settings'],
                });
            }
            items.push(
                { label: 'Leaves', path: '/leaves', prefixes: ['/leaves'] },
                { label: 'Posts', path: '/posts', prefixes: ['/posts'] },
                { label: 'Profile', path: '/profile', prefixes: ['/profile'] }
            );
            return items;
        },

        bottomSidebarItems() {
            if (!this.isAlpha() && this.isAdmin()) {
                return [{ label: 'System Config', path: '/approval/settings', prefixes: ['/approval/settings'] }];
            }
            return [];
        },

        activeSidebarItems() {
            return [...this.primarySidebarItems(), ...this.bottomSidebarItems()];
        },

        isNavActive(item) {
            const p = this.route.path;
            if (item.excludePrefixes?.some((ex) => p.startsWith(ex))) return false;
            return item.prefixes.some((pre) => p === pre || p.startsWith(`${pre}/`));
        },

        pageHeader() {
            const name = this.route.name;
            if (name === 'dashboard') {
                return {
                    eyebrow: this.isAdmin() ? 'Admin Dashboard' : 'Employee Dashboard',
                    title: this.isAdmin() ? 'Monthly Operations Overview' : `Welcome back, ${this.currentUser?.name ?? ''}`,
                };
            }
            const headers = {
                attendanceHistory: { eyebrow: 'Attendance', title: 'Attendance History' },
                leavesIndex: { eyebrow: 'Workspace', title: 'Leaves' },
                leavesCreate: { eyebrow: 'Leaves', title: 'Manajemen Izin & Cuti' },
                approvalIndex: { eyebrow: 'Approval Center', title: 'Employee Approval Menu' },
                approvalRegistrations: { eyebrow: 'Approval Center', title: 'Pending Registrations' },
                approvalEarlyCheckouts: { eyebrow: 'Approval Center', title: 'Early Checkout Approval' },
                approvalLeaves: { eyebrow: 'Approval Center', title: 'Leave Approval' },
                approvalSettings: { eyebrow: 'System Config', title: 'Global Working Hours Settings' },
                postsIndex: {
                    eyebrow: 'Workspace',
                    title: 'Announcements',
                    subtitle: this.isAdmin()
                        ? 'Manage all announcements and posts.'
                        : 'Read the latest announcements and posts from admin.',
                },
                postsShow: { eyebrow: 'Announcements', title: 'Detail Post' },
                postsCreate: { eyebrow: 'Announcements', title: 'Tambah Post', subtitle: 'Isi data post baru lalu simpan.' },
                postsEdit: { eyebrow: 'Announcements', title: 'Edit Post', subtitle: 'Update the selected post details.' },
                profile: { eyebrow: 'Account', title: 'Profile' },
                alphaSettings: { eyebrow: 'System Config', title: 'Alpha Developer Panel' },
                alphaDevtools: { eyebrow: 'Developer Tools', title: 'Tester Accounts & Data' },
            };
            return headers[name] ?? { eyebrow: 'TipTap', title: 'Employee Workspace' };
        },

        navigate(rawPath, { keepFlash = false } = {}) {
            if (!keepFlash) {
                this.flashStatus = '';
                this.flashError = '';
            }
            this.applyRoute(rawPath, true);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        applyRoute(rawPath, pushHistory) {
            let matched = matchRoute(rawPath);
            const user = this.currentUser;

            // Enforce route access rules matching Laravel middleware
            if (!user && !GUEST_ROUTES.has(matched.name)) {
                matched = matchRoute('/login');
            } else if (user) {
                if (['login', 'adminLogin', 'register', 'forgotPassword'].includes(matched.name)) {
                    matched = matchRoute(isAlphaRole(user.role) ? '/alpha/settings' : '/dashboard');
                } else if (isAlphaRole(user.role) && !['home', 'about', 'alphaSettings', 'alphaDevtools'].includes(matched.name)) {
                    matched = matchRoute('/alpha/settings');
                } else if (!isAlphaRole(user.role) && ['alphaSettings', 'alphaDevtools'].includes(matched.name)) {
                    matched = matchRoute('/dashboard');
                } else if (
                    !isAdminRole(user.role) &&
                    ['approvalIndex', 'approvalRegistrations', 'approvalEarlyCheckouts', 'approvalLeaves', 'approvalSettings', 'postsCreate', 'postsEdit'].includes(matched.name)
                ) {
                    this.flashError = 'Forbidden: Admin access required.';
                    matched = matchRoute('/dashboard');
                }
            }

            this.route = matched;
            this.syncFormsFromState();
            document.title = `${this.brandName()} — ${this.pageHeader().title}`;

            if (pushHistory && window.location.pathname !== matched.path) {
                window.history.pushState({}, '', matched.path);
            }
        },

        syncFormsFromState() {
            this.settingsForm = {
                company_email_domain: this.state.settings.company_email_domain,
                jam_masuk_kantor: this.state.settings.jam_masuk_kantor,
                jam_mulai_pulang: this.state.settings.jam_mulai_pulang,
                work_days: [...(this.state.settings.work_days ?? [1, 2, 3, 4, 5])],
            };

            if (this.currentUser) {
                this.profileForm.name = this.currentUser.name;
                this.profileForm.email = this.currentUser.email;
            }

            if (this.route.name === 'postsCreate') {
                this.postForm = { judul: '', deskripsi: '' };
            } else if (this.route.name === 'postsEdit') {
                const target = this.currentPost();
                this.postForm = {
                    judul: target?.judul ?? '',
                    deskripsi: target?.deskripsi ?? '',
                };
            }
        },

        // Presentation Demo Switcher
        switchPersona(target) {
            this.flashStatus = '';
            this.flashError = '';
            if (target === 'guest') {
                this.state.currentUserId = null;
                saveState(this.state);
                this.navigate('/');
                return;
            }
            const user = this.state.users.find((u) => u.email === target);
            if (!user) return;
            this.state.currentUserId = user.id;
            saveState(this.state);
            this.navigate(isAlphaRole(user.role) ? '/alpha/settings' : '/dashboard');
        },

        resetDemo() {
            this.state = resetState();
            this.leaveReviewModal = { open: false, leave: null, note: '' };
            this.flashError = '';
            this.flashStatus = 'Demo data reset to clean initial presentation state.';
            this.navigate('/', { keepFlash: true });
        },

        // Auth actions
        fillEmployeeDemo(email) {
            this.loginForm.email = email;
            this.loginForm.password = 'password123';
            this.loginForm.errors = {};
        },

        fillAdminDemo(email) {
            this.adminLoginForm.email = email;
            this.adminLoginForm.password = 'password123';
            this.adminLoginForm.errors = {};
        },

        submitEmployeeLogin() {
            const res = performLogin(this.state, {
                email: this.loginForm.email,
                password: this.loginForm.password,
                loginType: 'employee',
            });
            if (!res.ok) {
                this.loginForm.errors = res.errors;
                return;
            }
            this.loginForm = { email: '', password: '', showPassword: false, errors: {} };
            this.navigate(res.redirect);
        },

        submitAdminLogin() {
            const res = performLogin(this.state, {
                email: this.adminLoginForm.email,
                password: this.adminLoginForm.password,
                loginType: 'admin',
            });
            if (!res.ok) {
                this.adminLoginForm.errors = res.errors;
                return;
            }
            this.adminLoginForm = { email: '', password: '', showPassword: false, errors: {} };
            this.navigate(res.redirect);
        },

        submitRegister() {
            const res = performRegister(this.state, this.registerForm);
            if (!res.ok) {
                this.registerForm.errors = res.errors;
                return;
            }
            this.registerForm = { name: '', username: '', password: '', confirmation: '', errors: {} };
            this.flashStatus = res.message;
            this.navigate('/login', { keepFlash: true });
        },

        submitForgotPassword() {
            this.forgotForm.sentMessage = `Demo mode: If ${this.forgotForm.email} exists, a password reset link has been simulated.`;
        },

        logout() {
            this.state.currentUserId = null;
            saveState(this.state);
            this.navigate('/');
        },

        // Dashboard & Attendance
        employeeDashboard() {
            if (!this.currentUser) return { statusMeta: STATUS_META.not_yet_clocked_in, metrics: [], recentRows: [] };
            const stats = computeEmployeeDashboardStats(this.state, this.currentUser);
            return {
                ...stats,
                statusMeta: STATUS_META[stats.statusKey] ?? STATUS_META.not_yet_clocked_in,
            };
        },

        adminStats() {
            return computeAdminStats(this.state);
        },

        clockIn() {
            const res = performClockIn(this.state, this.currentUser);
            if (res.ok) {
                this.flashError = '';
                this.flashStatus = res.message;
            } else {
                this.flashStatus = '';
                this.flashError = res.error;
            }
        },

        clockOut() {
            const res = performClockOut(this.state, this.currentUser);
            if (res.ok) {
                this.flashError = '';
                this.flashStatus = res.message;
            } else {
                this.flashStatus = '';
                this.flashError = res.error;
            }
        },

        attendanceHistoryData() {
            if (!this.currentUser) return { items: [], total: 0, totalPages: 1, startIndex: 0, endIndex: 0 };
            const all = this.state.attendances
                .filter((a) => a.user_id === this.currentUser.id)
                .sort((a, b) => {
                    if (a.tanggal !== b.tanggal) return b.tanggal.localeCompare(a.tanggal);
                    return String(b.waktu_masuk ?? '').localeCompare(String(a.waktu_masuk ?? ''));
                });
            const perPage = Number(this.attendancePagination.perPage) || 10;
            const total = all.length;
            const totalPages = Math.max(1, Math.ceil(total / perPage));
            const page = Math.min(Math.max(1, this.attendancePagination.page), totalPages);
            const startIndex = (page - 1) * perPage;
            return {
                items: all.slice(startIndex, startIndex + perPage),
                total,
                totalPages,
                startIndex,
                endIndex: Math.min(startIndex + perPage, total),
            };
        },

        approvalTodayPage() {
            const all = this.adminStats().todayAttendances;
            const perPage = this.approvalTodayPagination.perPage;
            const total = all.length;
            const totalPages = Math.max(1, Math.ceil(total / perPage));
            const page = Math.min(Math.max(1, this.approvalTodayPagination.page), totalPages);
            const startIndex = (page - 1) * perPage;
            return {
                items: all.slice(startIndex, startIndex + perPage),
                total,
                totalPages,
                startIndex,
                endIndex: Math.min(startIndex + perPage, total),
            };
        },

        // Leaves
        leaveQuotaInfo() {
            if (!this.currentUser) return { total: 8, used: 0, remaining: 8 };
            const total = this.currentUser.leave_quota ?? 8;
            const used = getUsedLeaveDays(this.state, this.currentUser.id);
            const remaining = getRemainingLeaveDays(this.state, this.currentUser);
            return { total, used, remaining };
        },

        filteredUserLeaves() {
            if (!this.currentUser) return [];
            return this.state.leaves
                .filter((l) => {
                    if (l.user_id !== this.currentUser.id) return false;
                    if (this.leaveFilterStatus && l.status_approval !== this.leaveFilterStatus) return false;
                    return true;
                })
                .sort((a, b) => String(b.created_at ?? '').localeCompare(String(a.created_at ?? '')));
        },

        handleLeaveFile(file) {
            if (!file) return;
            const validTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type) || file.size > 5 * 1024 * 1024) {
                this.leaveForm.errors = ['Please upload a valid PDF, JPG, or PNG file under 5MB.'];
                return;
            }
            this.leaveForm.errors = [];
            const meta = { name: file.name, type: file.type, size: file.size, previewUrl: null };
            if (file.type.startsWith('image/') && file.size <= 1.5 * 1024 * 1024) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    meta.previewUrl = e.target?.result ?? null;
                    this.leaveForm.attachment = meta;
                };
                reader.readAsDataURL(file);
            } else {
                this.leaveForm.attachment = meta;
            }
        },

        submitLeaveForm() {
            const res = performSubmitLeave(this.state, this.currentUser, this.leaveForm);
            if (!res.ok) {
                this.leaveForm.errors = res.errors;
                return;
            }
            this.leaveForm = {
                jenis_izin: '',
                tanggal_mulai: '',
                tanggal_selesai: '',
                alasan: '',
                attachment: null,
                dragging: false,
                errors: [],
            };
            this.flashStatus = res.message;
            this.navigate('/leaves', { keepFlash: true });
        },

        // Admin Approvals & Settings
        filteredPendingRegistrations() {
            const q = this.registrationSearch.trim().toLowerCase();
            return this.adminStats().pendingUsers.filter((u) => {
                if (!q) return true;
                return u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q);
            });
        },

        approveUser(userId) {
            const user = this.state.users.find((u) => u.id === userId);
            if (!user) return;
            user.is_approved = true;
            user.role = user.role || 'user';
            saveState(this.state);
            this.flashError = '';
            this.flashStatus = `Employee account "${user.name}" approved successfully.`;
        },

        rejectUser(userId) {
            const user = this.state.users.find((u) => u.id === userId);
            if (!user || user.is_approved) return;
            this.state.users = this.state.users.filter((u) => u.id !== userId);
            saveState(this.state);
            this.flashError = '';
            this.flashStatus = `Employee registration request for "${user.name}" rejected.`;
        },

        approveEarlyCheckout(attId) {
            const att = this.state.attendances.find((a) => a.id === attId);
            if (!att || att.early_checkout_status !== 'pending') return;
            const reqDate = att.early_checkout_requested_at ? new Date(att.early_checkout_requested_at) : new Date();
            att.waktu_keluar = reqDate.toTimeString().slice(0, 8);
            att.status = 'Pulang Cepat';
            att.early_checkout_status = 'approved';
            att.early_checkout_reviewed_at = new Date().toISOString();
            att.early_checkout_reviewed_by = this.currentUser?.id ?? 1;
            saveState(this.state);
            this.flashError = '';
            this.flashStatus = 'Early checkout request approved successfully.';
        },

        rejectEarlyCheckout(attId) {
            const att = this.state.attendances.find((a) => a.id === attId);
            if (!att || att.early_checkout_status !== 'pending') return;
            att.early_checkout_status = 'rejected';
            att.early_checkout_reviewed_at = new Date().toISOString();
            att.early_checkout_reviewed_by = this.currentUser?.id ?? 1;
            saveState(this.state);
            this.flashError = '';
            this.flashStatus = 'Early checkout request rejected.';
        },

        openLeaveReview(leave) {
            this.leaveReviewModal = { open: true, leave, note: '' };
        },

        closeLeaveReview() {
            this.leaveReviewModal = { open: false, leave: null, note: '' };
        },

        approveLeave(leaveId) {
            const leave = this.state.leaves.find((l) => l.id === leaveId);
            if (!leave) return;
            leave.status_approval = 'Approved';
            saveState(this.state);
            this.closeLeaveReview();
            this.flashError = '';
            this.flashStatus = 'Leave request approved successfully.';
        },

        rejectLeave(leaveId, note) {
            const leave = this.state.leaves.find((l) => l.id === leaveId);
            if (!leave) return;
            leave.status_approval = 'Rejected';
            leave.rejection_reason = String(note ?? '').trim() || null;
            saveState(this.state);
            this.closeLeaveReview();
            this.flashError = '';
            this.flashStatus = 'Leave request rejected.';
        },

        toggleWorkDay(dayNum) {
            const exists = this.settingsForm.work_days.includes(dayNum);
            if (exists) {
                this.settingsForm.work_days = this.settingsForm.work_days.filter((d) => d !== dayNum);
            } else {
                this.settingsForm.work_days = [...this.settingsForm.work_days, dayNum].sort((a, b) => a - b);
            }
        },

        saveQuickHours() {
            this.saveSettingsForm();
        },

        saveSettingsForm() {
            const res = performUpdateSettings(this.state, this.settingsForm);
            if (res.ok) {
                this.flashError = '';
                this.flashStatus = res.message;
            } else {
                this.flashStatus = '';
                this.flashError = res.error;
            }
        },

        exportExcel(period, month = null, year = null) {
            const { filename, count } = exportAttendanceReport({
                attendances: this.state.attendances,
                users: this.state.users,
                period,
                month,
                year,
            });
            this.flashError = '';
            this.flashStatus = `Exported ${count} attendance record(s) to ${filename}.`;
        },

        // Posts CRUD
        sortedPosts() {
            return [...this.state.posts].sort((a, b) => String(b.created_at).localeCompare(String(a.created_at)));
        },

        currentPost() {
            if (!this.route.id) return null;
            return this.state.posts.find((p) => p.id === this.route.id) ?? null;
        },

        submitPostForm() {
            const judul = this.postForm.judul.trim();
            const deskripsi = this.postForm.deskripsi.trim();
            if (!judul || !deskripsi) {
                this.flashError = 'Judul and Deskripsi are required.';
                return;
            }
            if (this.route.name === 'postsEdit' && this.route.id) {
                const post = this.currentPost();
                if (post) {
                    post.judul = judul;
                    post.deskripsi = deskripsi;
                    saveState(this.state);
                    this.flashStatus = 'Post updated successfully.';
                }
            } else {
                this.state.posts.unshift({
                    id: this.state.nextIds.post++,
                    judul,
                    deskripsi,
                    created_at: new Date().toISOString(),
                });
                saveState(this.state);
                this.flashStatus = 'Post created successfully.';
            }
            this.navigate('/posts', { keepFlash: true });
        },

        deletePost(postId) {
            this.state.posts = this.state.posts.filter((p) => p.id !== postId);
            saveState(this.state);
            this.flashStatus = 'Post deleted successfully.';
        },

        // Profile
        submitProfileUpdate() {
            if (!this.currentUser) return;
            this.currentUser.name = this.profileForm.name.trim() || this.currentUser.name;
            this.currentUser.email = this.profileForm.email.trim() || this.currentUser.email;
            saveState(this.state);
            this.profileForm.saved = true;
            setTimeout(() => {
                this.profileForm.saved = false;
            }, 2000);
        },

        submitPasswordUpdate() {
            this.passwordForm.error = '';
            if (this.passwordForm.current_password !== this.currentUser.password) {
                this.passwordForm.error = 'Current password is incorrect.';
                return;
            }
            if (this.passwordForm.password.length < 8) {
                this.passwordForm.error = 'New password must be at least 8 characters.';
                return;
            }
            if (this.passwordForm.password !== this.passwordForm.password_confirmation) {
                this.passwordForm.error = 'Password confirmation does not match.';
                return;
            }
            this.currentUser.password = this.passwordForm.password;
            saveState(this.state);
            this.passwordForm = { current_password: '', password: '', password_confirmation: '', error: '', saved: true };
            setTimeout(() => {
                this.passwordForm.saved = false;
            }, 2000);
        },

        submitDeleteAccount(closeModal) {
            this.deleteAccountForm.error = '';
            if (this.deleteAccountForm.password !== this.currentUser?.password) {
                this.deleteAccountForm.error = 'Password does not match.';
                return;
            }
            const id = this.currentUser.id;
            this.state.users = this.state.users.filter((u) => u.id !== id);
            this.state.currentUserId = null;
            saveState(this.state);
            this.deleteAccountForm = { password: '', error: '' };
            closeModal?.();
            this.flashStatus = 'Demo account deleted.';
            this.navigate('/', { keepFlash: true });
        },

        // Alpha Panel
        saveAlphaSettings() {
            this.saveSettingsForm();
        },

        alphaResetToday() {
            const today = todayDateString();
            const before = this.state.attendances.length;
            this.state.attendances = this.state.attendances.filter((a) => a.tanggal !== today);
            saveState(this.state);
            const deleted = before - this.state.attendances.length;
            this.flashStatus = `Attendance reset successful! ${deleted} record(s) cleared for today. All users can now clock in again.`;
        },

        alphaGenerateTesters() {
            const res = performGenerateTesters(this.state, this.alphaDaysInput);
            if (res.ok) {
                this.flashError = '';
                this.flashStatus = res.message;
            } else {
                this.flashError = res.error;
            }
        },

        alphaTesterSnapshot() {
            return getTesterAccountsSnapshot(this.state);
        },

        alphaResetTesters() {
            const res = performResetTesters(this.state);
            this.flashStatus = res.message;
        },

        // Shared Display Helpers
        userNameById(userId) {
            return this.state.users.find((u) => u.id === userId)?.name ?? '-';
        },

        userEmailById(userId) {
            return this.state.users.find((u) => u.id === userId)?.email ?? '-';
        },

        formatEmployeeCode,
        formatDate: formatDayMonthYear,
        formatWeekdayDate: formatWeekdayDayMonthYear,
        formatMonthDay,
        formatMonthDayYear,
        formatDateTime: formatDateTimeDisplay,
        formatTimeAmPm,
        inclusiveDays: inclusiveDaysBetween,

        formatBytes(bytes) {
            if (!bytes) return '0 Bytes';
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return `${parseFloat((bytes / 1024 ** i).toFixed(2))} ${sizes[i]}`;
        },

        resolveAttendanceBadge(att) {
            const raw = String(att.status ?? '').toLowerCase();
            const early = String(att.early_checkout_status ?? '').toLowerCase();
            if (early === 'pending') return { label: 'Pending Checkout', tone: 'amber' };
            if (att.waktu_keluar) {
                return raw === 'pulang cepat'
                    ? { label: 'Early Checkout', tone: 'amber' }
                    : { label: 'Checked Out', tone: 'slate' };
            }
            if (raw === 'terlambat' || raw === 'late') return { label: 'Late', tone: 'rose' };
            if (raw === 'hadir') return { label: 'Present', tone: 'emerald' };
            return { label: 'Checked In', tone: 'emerald' };
        },

        adminAttendanceBadge(att) {
            const raw = String(att.status ?? '').toLowerCase();
            const early = String(att.early_checkout_status ?? '').toLowerCase();
            if (early === 'pending') return { label: 'Pending Checkout', classes: 'bg-amber-100 text-amber-700' };
            if (att.waktu_keluar) {
                return raw === 'pulang cepat'
                    ? { label: 'Early Checkout', classes: 'bg-orange-100 text-orange-700' }
                    : { label: 'Checked Out', classes: 'bg-slate-100 text-slate-600' };
            }
            if (raw === 'terlambat') return { label: 'Late', classes: 'bg-rose-100 text-rose-700' };
            return { label: 'Checked In', classes: 'bg-emerald-100 text-emerald-700' };
        },

        statusPillBadgeClass(tone) {
            return {
                emerald: 'border-[#0B4A85]/30 bg-[#E7EFF6] text-[#063157]',
                rose: 'border-rose-300 bg-rose-50 text-rose-700',
                slate: 'border-dark-slate/25 bg-dark-slate/10 text-dark-slate',
                amber: 'border-[#0B4A85]/30 bg-[#E7EFF6] text-[#0B4A85]',
                sky: 'border-sky-300 bg-sky-50 text-sky-700',
            }[tone] ?? 'border-dark-slate/25 bg-dark-slate/10 text-dark-slate';
        },

        statusPillDotClass(tone) {
            return {
                emerald: 'bg-[#0B4A85]',
                rose: 'bg-rose-600',
                slate: 'bg-dark-slate',
                amber: 'bg-[#0B4A85]',
                sky: 'bg-sky-500',
            }[tone] ?? 'bg-dark-slate';
        },

        metricTrendClass(dir) {
            return {
                up: 'text-emerald-600 bg-emerald-50 border-emerald-100',
                down: 'text-rose-600 bg-rose-50 border-rose-100',
                neutral: 'text-slate-500 bg-slate-100 border-slate-200',
            }[dir] ?? 'text-slate-500 bg-slate-100 border-slate-200';
        },

        leaveStatusBadgeClass(status) {
            const s = String(status ?? '').toLowerCase();
            if (s === 'approved') return 'bg-[#E7EFF6] text-[#063157] border-[#0B4A85]/30';
            if (s === 'rejected') return 'bg-rose-50 text-rose-700 border-rose-300';
            return 'bg-[#E7EFF6] text-[#0B4A85] border-[#0B4A85]/30';
        },

        leaveStatusDotClass(status) {
            const s = String(status ?? '').toLowerCase();
            if (s === 'rejected') return 'bg-rose-600';
            if (s === 'pending') return 'bg-[#0B4A85] animate-pulse';
            return 'bg-[#0B4A85]';
        },
    };
}
