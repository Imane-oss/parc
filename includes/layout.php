<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FleetSync - Dashboard</title>
    
    <!-- Tailwind CSS -->
    <link href="css/tailwind.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="flex h-screen overflow-hidden text-slate-800">

    <!-- Sidebar -->
    <aside class="w-64 flex-shrink-0 bg-white border-r border-slate-100 flex flex-col h-screen sticky top-0 left-0 z-20">
        <!-- Logo -->
        <div class="h-20 flex items-center px-6">
            <a href="index.php" class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#0066cc] text-white">
                    <i data-lucide="car-front" class="h-5 w-5"></i>
                </div>
                <span class="text-xl font-bold text-slate-900 tracking-tight">
                    parc <span class="text-[#0066cc]">automobile</span>
                </span>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4">
            <!-- Menu Principal -->
            <div class="px-6 mb-2">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">MENU PRINCIPAL</h3>
            </div>
            
            <ul class="space-y-1 mb-8 pr-4">
                <li class="relative">
                    <div class="absolute left-0 top-1/2 -translate-y-1/2 h-8 w-1 bg-[#0052a3] rounded-r-md"></div>
                    <a href="#" class="flex items-center gap-3 ml-4 px-4 py-2.5 bg-[#0066cc] text-white rounded-r-full font-medium text-sm transition-colors shadow-sm shadow-blue-500/20">
                        <i data-lucide="layout-dashboard" class="h-4.5 w-4.5"></i>
                        DASHBOARD
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center justify-between ml-4 px-4 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-full font-medium text-sm transition-colors group">
                        <div class="flex items-center gap-3">
                            <i data-lucide="clipboard-list" class="h-4.5 w-4.5 text-slate-400 group-hover:text-slate-600"></i>
                            MES DEMANDES
                        </div>
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-50 text-[10px] font-bold text-[#0066cc]">2</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center gap-3 ml-4 px-4 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-full font-medium text-sm transition-colors group">
                        <i data-lucide="settings" class="h-4.5 w-4.5 text-slate-400 group-hover:text-slate-600"></i>
                        SETTINGS
                    </a>
                </li>
            </ul>

            <!-- Gestion -->
            <div class="px-6 mb-2">
                <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">GESTION</h3>
            </div>
            
            <ul class="space-y-1 pr-4">
                <li>
                    <a href="#" class="flex items-center gap-3 ml-4 px-4 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-r-full font-medium text-sm transition-colors group">
                        <i data-lucide="truck" class="h-4.5 w-4.5 text-slate-400 group-hover:text-slate-600"></i>
                        PARC AUTOMOBILE
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Bottom Actions -->
        <div class="p-6 border-t border-slate-50">
            <a href="#" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-slate-50 text-slate-500 hover:bg-slate-100 hover:text-slate-800 text-xs font-bold uppercase tracking-wider transition-colors">
                <i data-lucide="log-out" class="h-4 w-4"></i>
                QUITTER L'ADMIN
            </a>
        </div>
    </aside>

    <!-- Main Content Area Wrapper Start -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden relative">
        
        <!-- Topbar -->
        <header class="h-20 bg-white/80 backdrop-blur-md sticky top-0 z-10 flex items-center justify-between px-8 border-b border-transparent">
            
            <!-- Breadcrumb -->
            <div class="flex items-center text-[11px] font-bold text-slate-400 tracking-[0.2em]">
               
            </div>

            <!-- Right Side Profile & Actions -->
            <div class="flex items-center gap-6 relative">
                
                <!-- Notification Bell -->
                <button type="button" class="relative flex h-10 w-10 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="bell" class="h-5 w-5"></i>
                    <span class="absolute top-2.5 right-2.5 h-2 w-2 rounded-full bg-red-500 border border-white"></span>
                </button>

                <!-- Divider -->
                <div class="h-8 w-px bg-slate-200"></div>

                <!-- User Profile -->
                <button id="profile-btn" type="button" class="flex items-center gap-3 focus:outline-none group">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-slate-900 leading-tight">Yassine Chebakou</p>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider">ADMIN</p>
                    </div>
                    <!-- Avatar with ring on active -->
                    <div id="avatar-container" class="h-10 w-10 rounded-2xl overflow-hidden bg-slate-200 border-2 border-transparent transition-colors">
                        <img src="https://ui-avatars.com/api/?name=Yassine+Chebakou&background=e2e8f0&color=475569" alt="Avatar" class="h-full w-full object-cover">
                    </div>
                </button>

                <!-- Profile Dropdown Menu (Hidden by default) -->
                <div id="profile-dropdown" class="absolute right-0 top-14 mt-2 w-64 rounded-3xl bg-white shadow-2xl shadow-slate-200/50 border border-slate-100 hidden opacity-0 translate-y-2 transition-all duration-200 z-50 overflow-hidden">
                    <div class="p-5 text-center border-b border-slate-50">
                        <p class="text-base font-bold text-slate-900">Yassine Chebakou</p>
                        <p class="text-[10px] font-bold text-slate-400 tracking-widest uppercase mt-1">ADMIN@PARC-AUTO.FR</p>
                    </div>
                    <div class="p-2">
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-50 rounded-2xl transition-colors">
                            <i data-lucide="user" class="h-4.5 w-4.5"></i>
                            Paramètres
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-rose-600 hover:bg-rose-50 rounded-2xl transition-colors mt-1">
                            <i data-lucide="log-out" class="h-4.5 w-4.5"></i>
                            Déconnexion
                        </a>
                    </div>
                </div>

            </div>
        </header>
