<?php include 'includes/layout.php'; ?>

        <!-- Dashboard Content -->
        <main class="flex-1 overflow-y-auto p-8">
            <div class="max-w-7xl mx-auto space-y-8">
                
                <!-- Page Header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-[32px] font-extrabold text-slate-900 tracking-tight leading-none mb-2">
                            DASHBOARD
                        </h1>
                        <p class="text-slate-500 font-medium">Gestion intelligente du <span class="text-[#0066cc]">parc automobile</span></p>
                    </div>
                    <button class="inline-flex items-center gap-2 bg-[#0066cc] text-white px-5 py-3 rounded-2xl font-bold text-sm shadow-lg shadow-blue-500/25 hover:bg-[#0052a3] hover:shadow-blue-500/40 transition-all">
                        <i data-lucide="plus" class="h-4.5 w-4.5"></i>
                        RAPPORT(PDF)
                    </button>
                </div>

                <!-- 4 Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    
                    <!-- Card 1 -->
                    <div class="bg-white rounded-[32px] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between h-48 relative overflow-hidden group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                        <div class="flex justify-between items-start">
                            <div class="h-12 w-12 rounded-full bg-blue-50 text-[#0066cc] flex items-center justify-center">
                                <i data-lucide="car-front" class="h-5 w-5"></i>
                            </div>
                            <button class="text-slate-300 hover:text-slate-500"><i data-lucide="more-vertical" class="h-5 w-5"></i></button>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">VÉHICULES ACTIFS</p>
                            <h2 class="text-4xl font-extrabold text-slate-900">124</h2>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <div class="h-1 w-1/2 bg-[#0066cc] rounded-full"></div>
                            <span class="text-[10px] font-bold text-slate-400">+3% month</span>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white rounded-[32px] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between h-48 relative overflow-hidden group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                        <div class="flex justify-between items-start">
                            <div class="h-12 w-12 rounded-full bg-orange-50 text-orange-500 flex items-center justify-center">
                                <i data-lucide="clock" class="h-5 w-5"></i>
                            </div>
                            <button class="text-slate-300 hover:text-slate-500"><i data-lucide="more-vertical" class="h-5 w-5"></i></button>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">DEMANDES</p>
                            <h2 class="text-4xl font-extrabold text-slate-900">12</h2>
                        </div>
                        <div class="flex items-center justify-end mt-2 relative">
                            <div class="absolute left-0 right-16 top-1/2 h-0.5 bg-slate-100"></div>
                            <span class="text-[10px] font-bold text-slate-400 bg-white pl-2 z-10">En attente</span>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-[32px] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between h-48 relative overflow-hidden group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                        <div class="flex justify-between items-start">
                            <div class="h-12 w-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center">
                                <i data-lucide="arrow-right" class="h-5 w-5"></i>
                            </div>
                            <button class="text-slate-300 hover:text-slate-500"><i data-lucide="more-vertical" class="h-5 w-5"></i></button>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">EN MISSION</p>
                            <h2 class="text-4xl font-extrabold text-slate-900">42</h2>
                        </div>
                        <div class="flex items-center justify-end mt-2 relative">
                            <div class="absolute left-0 right-20 top-1/2 h-0.5 bg-slate-100"></div>
                            <span class="text-[10px] font-bold text-slate-400 bg-white pl-2 z-10">Sur le terrain</span>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="bg-white rounded-[32px] p-6 shadow-xl shadow-slate-200/40 border border-slate-100 flex flex-col justify-between h-48 relative overflow-hidden group hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
                        <div class="flex justify-between items-start">
                            <div class="h-12 w-12 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center">
                                <i data-lucide="settings" class="h-5 w-5"></i>
                            </div>
                            <button class="text-slate-300 hover:text-slate-500"><i data-lucide="more-vertical" class="h-5 w-5"></i></button>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 tracking-wider uppercase mb-1">MAINTENANCE</p>
                            <h2 class="text-4xl font-extrabold text-slate-900">5</h2>
                        </div>
                        <div class="flex items-center justify-end mt-2 relative">
                            <div class="absolute left-0 right-12 top-1/2 h-0.5 bg-slate-100"></div>
                            <span class="text-[10px] font-bold text-slate-400 bg-white pl-2 z-10">Alertes</span>
                        </div>
                    </div>

                </div>

                <!-- Lower Layout: Lists & Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-4">
                    
                    <!-- Flux de demandes (Left Column) -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-[32px] p-6 shadow-xl shadow-slate-200/40 border border-slate-100">
                            
                            <!-- Header inside the card -->
                            <div class="flex items-center justify-between mb-5">
                                <h3 class="text-[14px] font-extrabold text-slate-900 tracking-wide uppercase">FLUX DE DEMANDES</h3>
                                <a href="#" class="text-xs font-bold text-[#0066cc] hover:text-[#0052a3]">VOIR TOUTES</a>
                            </div>
                            
                            <div class="space-y-3">
                            
                            <!-- Request Item 1 -->
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

                            <!-- Request Item 2 -->
                            <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/50 hover:bg-slate-50 transition-colors">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-full border border-slate-200 bg-white flex items-center justify-center text-sm font-bold text-slate-700">Y</div>
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">Youssef Benani</h4>
                                        <p class="text-xs font-medium text-slate-400">Tanger - Port • 2024-05-26</p>
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

                    <!-- Santé du parc (Right Column) -->
                    <div class="lg:col-span-1">
                        
                        <div class="bg-[#0f172a] rounded-[32px] p-8 shadow-2xl shadow-slate-900/40 text-white relative overflow-hidden">
                            <!-- Subtle glow background effect -->
                            <div class="absolute -top-24 -right-24 h-48 w-48 bg-[#0066cc] rounded-full blur-[80px] opacity-40"></div>
                            
                            <h3 class="text-[14px] font-extrabold tracking-wide uppercase mb-1">SANTÉ DU PARC</h3>
                            <p class="text-slate-400 text-xs font-medium mb-8">Disponibilité temps réel</p>
                            
                            <div class="space-y-6">
                                <!-- Progress SUV -->
                                <div>
                                    <div class="flex justify-between text-xs font-bold mb-2">
                                        <span>SUV</span>
                                        <span class="text-slate-300">85%</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-[#0066cc] rounded-full" style="width: 85%"></div>
                                    </div>
                                </div>

                                <!-- Progress Utilitaires -->
                                <div>
                                    <div class="flex justify-between text-xs font-bold mb-2">
                                        <span>UTILITAIRES</span>
                                        <span class="text-slate-300">45%</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-emerald-500 rounded-full" style="width: 45%"></div>
                                    </div>
                                </div>

                                <!-- Progress Berlines -->
                                <div>
                                    <div class="flex justify-between text-xs font-bold mb-2">
                                        <span>BERLINES</span>
                                        <span class="text-slate-300">92%</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-slate-800 rounded-full overflow-hidden">
                                        <div class="h-full bg-amber-500 rounded-full" style="width: 92%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- Javascript -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Profile Dropdown Toggle
        const profileBtn = document.getElementById('profile-btn');
        const profileDropdown = document.getElementById('profile-dropdown');
        const avatarContainer = document.getElementById('avatar-container');

        if (profileBtn && profileDropdown) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                const isHidden = profileDropdown.classList.contains('hidden');
                
                if (isHidden) {
                    profileDropdown.classList.remove('hidden');
                    // Add slight delay for animation
                    setTimeout(() => {
                        profileDropdown.classList.remove('opacity-0', 'translate-y-2');
                        profileDropdown.classList.add('opacity-100', 'translate-y-0');
                    }, 10);
                    avatarContainer.classList.add('border-[#0066cc]');
                    avatarContainer.classList.remove('border-transparent');
                } else {
                    profileDropdown.classList.add('opacity-0', 'translate-y-2');
                    profileDropdown.classList.remove('opacity-100', 'translate-y-0');
                    setTimeout(() => {
                        profileDropdown.classList.add('hidden');
                    }, 200);
                    avatarContainer.classList.remove('border-[#0066cc]');
                    avatarContainer.classList.add('border-transparent');
                }
            });

            // Close when clicking outside
            document.addEventListener('click', (e) => {
                if (!profileBtn.contains(e.target) && !profileDropdown.contains(e.target)) {
                    if (!profileDropdown.classList.contains('hidden')) {
                        profileDropdown.classList.add('opacity-0', 'translate-y-2');
                        profileDropdown.classList.remove('opacity-100', 'translate-y-0');
                        setTimeout(() => {
                            profileDropdown.classList.add('hidden');
                        }, 200);
                        avatarContainer.classList.remove('border-[#0066cc]');
                        avatarContainer.classList.add('border-transparent');
                    }
                }
            });
        }
    </script>
</body>
</html>
