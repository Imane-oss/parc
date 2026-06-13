<?php
require_once __DIR__.'/db_connection.php';

// Fetch logged-in user info
$user = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT fullname AS name, email, role FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}

// Automatic page detector
$current_script = basename($_SERVER['PHP_SELF']);
if ($current_script === 'demandes.php') {
    $activePage = 'demandes';
} elseif ($current_script === 'paramètres.php') {
    $activePage = 'PARAMÈTRES';
} elseif ($current_script === 'vehicles.php') {
    $activePage = 'vehicles';
} else {
    $activePage = 'dashboard';
}

$userName  = $user['name'] ?? $_SESSION['full_name'] ?? 'Admin';
$userRole  = $user['role'] ?? $_SESSION['role'] ?? 'ADMIN';

function isActive(string $page, string $activePage): bool
{
    return $page === $activePage;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>parc-automobile</title>

    <link href="css/tailwind.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
        /* Style dyal l-indicateur azraq sabet */
        .sidebar-indicator {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 32px;
            width: 4px;
            background-color: #0052a3;
            border-top-right-radius: 6px;
            border-bottom-right-radius: 6px;
            z-index: 50;
        }
        
        /* Animation professional dyal l-profile dropdown */
        .profile-dropdown {
            opacity: 0;
            transform: scale(0.95) translateY(-10px);
            transition: opacity 0.2s ease-out, transform 0.2s ease-out;
            pointer-events: none;
        }
        .profile-dropdown.show {
            opacity: 1;
            transform: scale(1) translateY(0);
            pointer-events: auto;
        }
        /* Stats mini cards inside dropdown */
        .pd-stat-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e4ebf4;
            transition: background 0.15s;
        }
        .pd-stat-card:hover { background: #f0f4fa; }
        .pd-stat-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 14px;
        }
        .pd-stat-label { font-size: 10px; font-weight: 700; color: #8799ae; text-transform: uppercase; letter-spacing: 0.05em; }
        .pd-stat-value { font-size: 18px; font-weight: 800; color: #001737; line-height: 1.1; }
        @keyframes pdCountUp {
            from { opacity: 0; transform: translateY(4px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .pd-stat-value { animation: pdCountUp 0.4s ease-out; }
    </style>
</head>

<body class="flex h-screen overflow-hidden text-slate-800">

    <aside class="w-64 flex-shrink-0 bg-white border-r border-slate-100 flex flex-col h-screen sticky top-0 left-0 z-20">

        <div class="h-20 flex items-center px-6">
            <a href="dashboard.php" class="flex items-center gap-3">
                <div class="flex items-center justify-center rounded-lg overflow-hidden flex-shrink-0" style="width: 44px !important; height: 44px !important; min-width: 44px !important; min-height: 44px !important;">
                    <img src="/parc_auto/logo.png" alt="Logo" style="width: 44px !important; height: 44px !important; min-width: 44px !important; min-height: 44px !important; display: block !important; object-fit: contain !important;">
                </div>
                <span class="text-xl font-bold text-slate-900 tracking-tight flex-shrink-0">
                    parc <span class="text-[#0066cc]">automobile</span>
                </span>
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto py-4">

            <div class="px-6 mb-2">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    MENU PRINCIPAL
                </h3>
            </div>

            <ul class="space-y-1 mb-8 pr-4">

                <li class="relative">
                    <?php if (isActive('dashboard', $activePage)): ?>
                        <div class="sidebar-indicator"></div>
                    <?php endif; ?>
                    <a href="dashboard.php" class="flex items-center gap-3 ml-4 px-4 py-2.5 rounded-r-full font-medium text-sm transition-colors <?= isActive('dashboard', $activePage) ? 'bg-[#0066cc] text-white shadow-sm shadow-blue-500/20' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                        <span>DASHBOARD</span>
                    </a>
                </li>

                <li class="relative">
                    <?php if (isActive('demandes', $activePage)): ?>
                        <div class="sidebar-indicator"></div>
                    <?php endif; ?>
                    <a href="demandes.php" class="flex items-center justify-between ml-4 px-4 py-2.5 rounded-r-full font-medium text-sm transition-colors group <?= isActive('demandes', $activePage) ? 'bg-[#0066cc] text-white shadow-sm shadow-blue-500/20' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' ?>">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 <?= isActive('demandes', $activePage) ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' ?>"><rect width="16" height="18" x="4" y="4" rx="2"/><path d="M8 9h8"/><path d="M8 13h8"/><path d="M8 17h4"/></svg>
                            <span>MES DEMANDES</span>
                        </div>
                        <span class="<?= isActive('demandes', $activePage) ? 'bg-white/20 text-white' : 'bg-blue-50 text-[#0066cc]' ?> flex h-5 w-5 items-center justify-center rounded-full text-[10px] font-bold flex-shrink-0">
                            2
                        </span>
                    </a>
                </li>

                <li class="relative">
                    <?php if (isActive('PARAMÈTRES', $activePage)): ?>
                        <div class="sidebar-indicator"></div>
                    <?php endif; ?>
                    <a href="paramètres.php" class="flex items-center gap-3 ml-4 px-4 py-2.5 rounded-r-full font-medium text-sm transition-colors group <?= isActive('PARAMÈTRES', $activePage) ? 'bg-[#0066cc] text-white shadow-sm shadow-blue-500/20' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 <?= isActive('PARAMÈTRES', $activePage) ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' ?>"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                        <span>PARAMÈTRES</span>
                    </a>
                </li>

            </ul>

            <div class="px-6 mb-2">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                    GESTION
                </h3>
            </div>

            <ul class="space-y-1 pr-4">
                <li class="relative">
                    <?php if (isActive('vehicles', $activePage)): ?>
                        <div class="sidebar-indicator"></div>
                    <?php endif; ?>
                    <a href="vehicles.php" class="flex items-center gap-3 ml-4 px-4 py-2.5 rounded-r-full font-medium text-sm transition-colors group <?= isActive('vehicles', $activePage) ? 'bg-[#0066cc] text-white shadow-sm shadow-blue-500/20' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-50' ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 <?= isActive('vehicles', $activePage) ? 'text-white' : 'text-slate-400 group-hover:text-slate-600' ?>"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-5.13a1 1 0 0 0-.29-.71l-3.3-3.3a1 1 0 0 0-.71-.29H14"/><circle cx="7" cy="18" r="2"/><circle cx="17" cy="18" r="2"/></svg>
                        <span>PARC AUTOMOBILE</span>
                    </a>
                </li>
            </ul>

        </nav>

        <div class="p-6 border-t border-slate-50">
            <a href="logout.php" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-800 text-xs font-bold uppercase tracking-wider transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                QUITTER L'ADMIN
            </a>
        </div>

    </aside>

    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">

        <header class="h-20 bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-8 border-b border-slate-100">
            <div class="flex items-center text-[11px] font-bold text-slate-400 tracking-[0.2em]"></div>
            
            <div class="flex items-center gap-6 relative">
                <!-- Notifications -->
                <div class="relative">
                    <button id="notif-btn" type="button" class="relative flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                        <span class="absolute top-2.5 right-2.5 h-2 w-2 rounded-full bg-red-500 border border-white"></span>
                    </button>
                    
                    <div id="notif-menu" class="profile-dropdown absolute right-0 top-full mt-2 bg-white border border-slate-100 rounded-2xl shadow-xl z-50 overflow-hidden" style="width: 320px;">
                        
                        <!-- Header -->
                        <div class="px-5 py-4 border-b border-slate-50 flex items-center justify-between bg-white">
                            <h3 class="text-[13px] font-extrabold text-[#001737] tracking-tight uppercase">NOTIFICATIONS</h3>
                            <span class="bg-[#f0f7ff] text-[#0066cc] text-[10px] font-bold px-2 py-1 rounded-md uppercase tracking-wider">2 NOUVELLES</span>
                        </div>
                        
                        <!-- List -->
                        <div class="max-h-[28rem] overflow-y-auto">
                            <!-- Item 1 -->
                            <a href="demandes.php" class="flex items-start gap-4 p-5 border-b border-slate-50 hover:bg-slate-50 transition-colors bg-white">
                                <div class="w-10 h-10 rounded-2xl bg-[#fffbeb] text-[#f59e0b] flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M9 14h6"/><path d="M9 10h6"/><path d="M9 18h6"/></svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-slate-800 mb-0.5">Nouvelle Demande</h4>
                                    <p class="text-xs font-medium text-slate-500 leading-relaxed mb-2">Ahmed Alami a soumis une demande pour Casablanca.</p>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">IL Y A 2 MIN</span>
                                </div>
                            </a>
                            
                            <!-- Item 2 -->
                            <a href="demandes.php" class="flex items-start gap-4 p-5 border-b border-slate-50 hover:bg-slate-50 transition-colors bg-white">
                                <div class="w-10 h-10 rounded-2xl bg-[#fffbeb] text-[#f59e0b] flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/><path d="M9 14h6"/><path d="M9 10h6"/><path d="M9 18h6"/></svg>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-bold text-slate-800 mb-0.5">Nouvelle Demande</h4>
                                    <p class="text-xs font-medium text-slate-500 leading-relaxed mb-2">Fatima Zahra a soumis une demande pour Rabat.</p>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">IL Y A 1 HEURE</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="h-8 w-px bg-slate-200"></div>
                
                <button id="profile-btn" type="button" class="flex items-center gap-3 focus:outline-none group">
                    <div class="text-right hidden sm:block">
                        <p id="header-user-name" class="text-sm font-bold text-slate-900 leading-tight"><?php echo htmlspecialchars($userName); ?></p>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider"><?php echo htmlspecialchars($userRole); ?></p>
                    </div>
                    <div id="avatar-container" class="h-10 w-10 rounded-2xl overflow-hidden bg-slate-200 border-2 border-transparent transition-colors">
                        <img id="header-avatar-img" src="https://ui-avatars.com/api/?name=<?= urlencode($userName) ?>&background=e2e8f0&color=475569" alt="Avatar" class="h-full w-full object-cover">
                    </div>
                </button>

                <div id="profile-menu" class="profile-dropdown absolute right-0 top-full mt-2 bg-white border border-slate-100 rounded-2xl shadow-xl p-2 z-50" style="width:280px">

                    <!-- User info header -->
                    <div class="px-4 py-3 border-b border-slate-50 text-center">
                        <div id="dropdown-user-initial" class="w-10 h-10 rounded-xl bg-blue-50 text-[#0066cc] flex items-center justify-center font-extrabold text-base mx-auto mb-2 overflow-hidden">
                            <?php echo strtoupper(mb_substr($userName, 0, 1)); ?>
                        </div>
                        <p id="dropdown-user-name" class="text-sm font-bold text-slate-800"><?php echo htmlspecialchars($userName); ?></p>
                        <p class="text-[10px] font-bold text-[#0066cc] bg-blue-50 px-2 py-0.5 rounded-full inline-block mt-1 tracking-wider"><?php echo htmlspecialchars($userRole); ?></p>
                    </div>


                    <!-- Settings & Logout -->
                    <div class="space-y-0.5 mt-1">
                        <a href="paramètres.php" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-slate-600 rounded-xl hover:bg-slate-50 hover:text-slate-900 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-400"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.1a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
                            <span>Paramètres</span>
                        </a>
                        <a href="logout.php" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-red-500 rounded-xl hover:bg-red-50 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            <span>Déconnexion</span>
                        </a>
                    </div>
                </div>

            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Load global profile settings if saved
                const savedName = localStorage.getItem('profile_name');
                if (savedName) {
                    const hn = document.getElementById('header-user-name');
                    const dn = document.getElementById('dropdown-user-name');
                    const di = document.getElementById('dropdown-user-initial');
                    if (hn) hn.textContent = savedName;
                    if (dn) dn.textContent = savedName;
                    if (di && savedName.length > 0) di.textContent = savedName.charAt(0).toUpperCase();
                }
                const savedPhoto = localStorage.getItem('profile_photo');
                if (savedPhoto) {
                    const hImg = document.getElementById('header-avatar-img');
                    if (hImg) hImg.src = savedPhoto;
                    
                    const dropdownInitialBox = document.getElementById('dropdown-user-initial');
                    if (dropdownInitialBox) {
                        dropdownInitialBox.innerHTML = '<img src="' + savedPhoto + '" alt="Avatar" class="w-full h-full object-cover">';
                    }
                }

                const profileBtn = document.getElementById('profile-btn');
                const profileMenu = document.getElementById('profile-menu');
                
                const notifBtn = document.getElementById('notif-btn');
                const notifMenu = document.getElementById('notif-menu');

                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    profileMenu.classList.toggle('show');
                    if(notifMenu) notifMenu.classList.remove('show');
                });
                
                if (notifBtn) {
                    notifBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        notifMenu.classList.toggle('show');
                        profileMenu.classList.remove('show');
                    });
                }

                document.addEventListener('click', function(event) {
                    if (!profileBtn.contains(event.target) && !profileMenu.contains(event.target)) {
                        profileMenu.classList.remove('show');
                    }
                    if (notifBtn && !notifBtn.contains(event.target) && !notifMenu.contains(event.target)) {
                        notifMenu.classList.remove('show');
                    }
                });

                /* ------------------------------------------------
                 * syncProfileCounts()
                 * Reads the live badges from the Mes Demandes page
                 * if we are on it, otherwise uses the last known
                 * value stored in localStorage.
                 * ------------------------------------------------ */
                function syncProfileCounts() {
                    // Try to read live counts from the demandes page DOM
                    const badgeAttente   = document.getElementById('badge-attente-top');
                    const badgeHistorique = document.getElementById('badge-historique-top');

                    if (badgeAttente && badgeHistorique) {
                        // We ARE on the demandes page – use live values
                        const attente  = parseInt(badgeAttente.textContent)   || 0;
                        const historique = parseInt(badgeHistorique.textContent) || 0;

                        // Heuristic: count refused rows in historique
                        const refuseRows = document.querySelectorAll('.hist-row[data-refused="1"]').length;
                        const validee    = historique - refuseRows;

                        setProfileStat('pd-count-attente', attente);
                        setProfileStat('pd-count-validee', Math.max(0, validee));
                        setProfileStat('pd-count-refuse',  refuseRows);

                        // Persist for other pages
                        localStorage.setItem('pd_attente',  attente);
                        localStorage.setItem('pd_validee',  Math.max(0, validee));
                        localStorage.setItem('pd_refuse',   refuseRows);

                    } else {
                        // Another page – read from localStorage
                        setProfileStat('pd-count-attente', localStorage.getItem('pd_attente')  ?? '—');
                        setProfileStat('pd-count-validee', localStorage.getItem('pd_validee')  ?? '—');
                        setProfileStat('pd-count-refuse',  localStorage.getItem('pd_refuse')   ?? '—');
                    }
                }

                function setProfileStat(id, value) {
                    const el = document.getElementById(id);
                    if (!el) return;
                    el.textContent = value;
                    // retrigger animation
                    el.style.animation = 'none';
                    el.offsetHeight;   // reflow
                    el.style.animation = '';
                }

                // If on demandes page, keep localStorage updated whenever counts change
                const observer = typeof MutationObserver !== 'undefined'
                    ? new MutationObserver(() => {
                        const b1 = document.getElementById('badge-attente-top');
                        const b2 = document.getElementById('badge-historique-top');
                        if (b1 && b2) {
                            localStorage.setItem('pd_attente', parseInt(b1.textContent) || 0);
                            localStorage.setItem('pd_validee', parseInt(b2.textContent) || 0);
                            localStorage.setItem('pd_refuse',  0);
                        }
                    })
                    : null;

                const b1 = document.getElementById('badge-attente-top');
                const b2 = document.getElementById('badge-historique-top');
                if (observer && b1) observer.observe(b1, { childList: true, characterData: true, subtree: true });
                if (observer && b2) observer.observe(b2, { childList: true, characterData: true, subtree: true });

                // Initial sync on page load
                syncProfileCounts();
            });
        </script>