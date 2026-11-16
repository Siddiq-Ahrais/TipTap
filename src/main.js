import '../resources/css/app.css';
import Alpine from 'alpinejs';
import { createTipTapApp } from './appController.js';

import demoBarHtml from './views/demoBar.html?raw';
import guestPagesHtml from './views/guestPages.html?raw';
import workspaceShellHtml from './views/workspaceShell.html?raw';
import dashboardHtml from './views/dashboardView.html?raw';
import attendanceHistoryHtml from './views/attendanceHistoryView.html?raw';
import leavesHtml from './views/leavesView.html?raw';
import approvalHtml from './views/approvalView.html?raw';
import postsHtml from './views/postsView.html?raw';
import profileHtml from './views/profileView.html?raw';
import alphaHtml from './views/alphaView.html?raw';

const workspacePages = [
    dashboardHtml,
    attendanceHistoryHtml,
    leavesHtml,
    approvalHtml,
    postsHtml,
    profileHtml,
    alphaHtml,
].join('\n');

const assembledWorkspace = workspaceShellHtml.replace('<!-- WORKSPACE_PAGES -->', workspacePages);

const appRoot = document.getElementById('app');
appRoot.innerHTML = `<div x-data="tiptapApp()">\n${demoBarHtml}\n${guestPagesHtml}\n${assembledWorkspace}\n</div>`;

Alpine.data('tiptapApp', createTipTapApp);
window.Alpine = Alpine;
Alpine.start();
