<?php
$activePage = 'dashboard';
include 'includes/layout.php';

if (isset($pdo)) {
    $totalVehicules   = (int) $pdo->query("SELECT COUNT(*) FROM vehicles")->fetchColumn();
    $totalDemandes    = (int) $pdo->query("SELECT COUNT(*) FROM vehicles WHERE statut = 'disponible'")->fetchColumn();
    $totalMissions    = (int) $pdo->query("SELECT COUNT(*) FROM vehicles WHERE statut = 'en mission'")->fetchColumn();
    $totalMaintenance = (int) $pdo->query("SELECT COUNT(*) FROM vehicles WHERE statut = 'en maintenance'")->fetchColumn();
} else {
    $totalVehicules   = 0;
    $totalDemandes    = 0;
    $totalMissions    = 0;
    $totalMaintenance = 0;
}
?>

        <div class="max-w-7xl mx-auto space-y-8">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight leading-none mb-2">
                        DASHBOARD
                    </h1>
                    <p class="text-slate-500 font-medium">Gestion intelligente du <span class="text-[#0066cc]">parc automobile</span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- CARD : VÉHICULES ACTIFS -->
                <div class="bg-white rounded-[32px] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between h-48 relative overflow-hidden group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <div class="h-12 w-12 rounded-full bg-blue-50 text-[#0066cc] flex items-center justify-center">
                            <i data-lucide="car-front" class="h-5 w-5"></i>
                        </div>
                        <button class="text-slate-300 hover:text-slate-500"><i data-lucide="more-vertical" class="h-5 w-5"></i></button>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">VÉHICULES ACTIFS</p>
                        <!-- Affichage dynamique -->
                        <h2 class="text-4xl font-extrabold text-slate-900"><?= $totalVehicules; ?></h2>
                    </div>
                    <div class="flex items-center justify-between mt-2">
                        <div class="h-1 w-1/2 bg-[#0066cc] rounded-full"></div>
                        <span class="text-[10px] font-bold text-slate-400">+3% month</span>
                    </div>
                </div>

                <!-- CARD : DEMANDES -->
                <div class="bg-white rounded-[32px] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between h-48 relative overflow-hidden group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <div class="h-12 w-12 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center">
                            <i data-lucide="clock" class="h-5 w-5"></i>
                        </div>
                        <button class="text-slate-300 hover:text-slate-500"><i data-lucide="more-vertical" class="h-5 w-5"></i></button>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">DEMANDES</p>
                        <!-- Affichage dynamique -->
                        <h2 class="text-4xl font-extrabold text-slate-900"><?= $totalDemandes; ?></h2>
                    </div>
                    <div class="flex items-center justify-end mt-2 relative">
                        <div class="absolute left-0 right-16 top-1/2 h-0.5 bg-slate-100"></div>
                        <span class="text-[10px] font-bold text-slate-400 bg-white pl-2 z-10">En attente</span>
                    </div>
                </div>

                <!-- CARD : EN MISSION -->
                <div class="bg-white rounded-[32px] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between h-48 relative overflow-hidden group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <div class="h-12 w-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center">
                            <i data-lucide="arrow-right" class="h-5 w-5"></i>
                        </div>
                        <button class="text-slate-300 hover:text-slate-500"><i data-lucide="more-vertical" class="h-5 w-5"></i></button>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">EN MISSION</p>
                        <!-- Affichage dynamique -->
                        <h2 class="text-4xl font-extrabold text-slate-900"><?= $totalMissions; ?></h2>
                    </div>
                    <div class="flex items-center justify-end mt-2 relative">
                        <div class="absolute left-0 right-20 top-1/2 h-0.5 bg-slate-100"></div>
                        <span class="text-[10px] font-bold text-slate-400 bg-white pl-2 z-10">Sur le terrain</span>
                    </div>
                </div>

                <!-- CARD : MAINTENANCE -->
                <div class="bg-white rounded-[32px] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between h-48 relative overflow-hidden group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                    <div class="flex justify-between items-start">
                        <div class="h-12 w-12 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center">
                            <i data-lucide="settings" class="h-5 w-5"></i>
                        </div>
                        <button class="text-slate-300 hover:text-slate-500"><i data-lucide="more-vertical" class="h-5 w-5"></i></button>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">MAINTENANCE</p>
                        <!-- Affichage dynamique -->
                        <h2 class="text-4xl font-extrabold text-slate-900"><?= $totalMaintenance; ?></h2>
                    </div>
                    <div class="flex items-center justify-end mt-2 relative">
                        <div class="absolute left-0 right-12 top-1/2 h-0.5 bg-slate-100"></div>
                        <span class="text-[10px] font-bold text-slate-400 bg-white pl-2 z-10">Alertes</span>
                    </div>
                </div>

            </div>

            <!-- Reste du code (Flux de demandes, Santé du parc, Scripts...) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-4">
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-[32px] p-6 shadow-xl shadow-slate-200/40 border border-slate-100">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-[14px] font-extrabold text-slate-900 tracking-wide uppercase">FLUX DE DEMANDES</h3>
                            <a href="demandes.php" class="text-xs font-bold text-[#0066cc] hover:text-[#0052a3]">VOIR TOUTES</a>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/50 hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-sm font-bold text-slate-700">A</div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">Ahmed Alami</h4>
                                        <p class="text-xs font-medium text-slate-400">Casablanca - Centre • 2024-05-24</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button class="flex h-8 w-8 items-center justify-center rounded-xl bg-[#0066cc] text-white hover:bg-[#0052a3] transition-colors shadow-md shadow-blue-500/20">
                                        <i data-lucide="check" class="h-4 w-4"></i>
                                    </button>
                                    <button class="flex h-8 w-8 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 transition-colors">
                                        <i data-lucide="x" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1">
                    <div class="bg-[#0f172a] rounded-[32px] p-8 shadow-2xl shadow-slate-900/40 text-white relative overflow-hidden">
                        <div class="absolute -top-24 -right-24 h-48 w-48 bg-[#0066cc] rounded-full blur-[80px] opacity-40"></div>
                        <h3 class="text-[14px] font-extrabold tracking-wide uppercase mb-1">SANTÉ DU PARC</h3>
                        <p class="text-slate-400 text-xs font-medium mb-8">Disponibilité temps réel</p>
                        
                        <div class="space-y-6">
                            <div>
                                <div class="flex justify-between text-xs font-bold mb-2">
                                    <span>SUV</span>
                                    <span class="text-slate-300">85%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full bg-[#0066cc] rounded-full" style="width: 85%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main> 
</div> 

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
</body>
</html>