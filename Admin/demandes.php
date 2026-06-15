<?php
$activePage = 'demandes';
include 'includes/layout.php'; 

// Récupérer les véhicules disponibles pour l'affectation
$availableVehicles = $pdo->prepare("SELECT marque, matricule FROM vehicles WHERE statut = 'disponible' ORDER BY id DESC");
$availableVehicles->execute();
$availableVehicles = $availableVehicles->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les demandes de mission
$demandesStmt = $pdo->prepare("SELECT * FROM demandes_mission ORDER BY created_at DESC");
$demandesStmt->execute();
$demandes = $demandesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>

<div class="max-w-7xl mx-auto flex flex-col gap-6 p-4 md:p-6 font-sans">
    
    <div class="bg-white border border-[#e4ebf4] border-l-4 border-blue-600 rounded-3xl p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 shadow-sm">
        <div>
            <h1 class="text-xl font-bold text-[#001737] tracking-tight mb-1">MES DEMANDES</h1>
            <p class="text-xs text-[#8799ae]">Validez les missions, gérez l'historique et générez les ordres de mission.</p>
        </div>

        <div class="flex items-center bg-[#f4f7fa] border border-[#e4ebf4] p-1.5 rounded-2xl self-start md:self-center">
            <button onclick="switchTab('attente')" id="btn-tab-attente" 
                class="tab-btn bg-white text-[#001737] font-medium text-xs tracking-wide px-5 py-2 rounded-xl shadow-sm border border-[#e4ebf4]/60 flex items-center gap-2 transition-all cursor-pointer">
                EN ATTENTE 
                <span id="badge-attente-top" class="w-5 h-5 rounded-full bg-[#f59e0b] text-white flex items-center justify-center text-[10px] font-medium">2</span>
            </button>
            <button onclick="switchTab('historique')" id="btn-tab-historique" 
                class="tab-btn text-[#8799ae] hover:text-[#001737] font-medium text-xs tracking-wide px-5 py-2 rounded-xl flex items-center gap-2 transition-all cursor-pointer">
                HISTORIQUE 
                <span id="badge-historique-top" class="w-5 h-5 rounded-full bg-[#001737] text-white flex items-center justify-center text-[10px] font-medium">1</span>
            </button>
            <button onclick="switchTab('combines')" id="btn-tab-combines" 
                class="tab-btn text-[#8799ae] hover:text-[#001737] font-medium text-xs tracking-wide px-5 py-2 rounded-xl flex items-center gap-2 transition-all cursor-pointer">
                DEMANDES COMBINÉS 
                <span id="badge-combines-top" class="w-5 h-5 rounded-full bg-purple-600 text-white flex items-center justify-center text-[10px] font-medium">0</span>
            </button>
        </div>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full px-1">
        <div class="flex flex-wrap items-center gap-3 flex-1">
            <div class="relative w-full max-w-xs">
                <input id="search-input" type="text" placeholder="Rechercher un collaborateur, une destination..."
                    class="w-full pl-4 pr-4 py-2 bg-white border border-[#e4ebf4] rounded-xl focus:outline-none focus:ring-2 focus:ring-[#0066cc]/10 text-sm text-[#001737] placeholder-[#8799ae] transition-all"
                    oninput="filterTable()">
            </div>
        </div>
        <div class="text-[11px] font-medium text-[#8799ae] tracking-wider uppercase">
            TOTAL ENREGISTRÉ : <span id="total-count" class="text-[#001737] font-bold ml-1">2</span>
        </div>
    </div>

    <div id="tab-attente" class="w-full bg-white rounded-3xl border border-[#e4ebf4] overflow-hidden shadow-sm transition-all">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#e4ebf4] bg-[#fcfdfe]">
                        <th class="py-4 px-6 text-xs font-semibold text-[#8799ae] tracking-wider">COLLABORATEUR</th>
                        <th class="py-4 px-6 text-xs font-semibold text-[#8799ae] tracking-wider">DESTINATION / MOTIF</th>
                        <th class="py-4 px-6 text-xs font-semibold text-[#8799ae] tracking-wider">PÉRIODE</th>
                        <th class="py-4 px-6 text-xs font-semibold text-[#8799ae] tracking-wider">STATUS</th>
                        <th class="py-4 px-6 text-xs font-semibold text-[#8799ae] tracking-wider text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="attente-tbody" class="divide-y divide-[#e4ebf4]/60">
                    <?php if (count($demandes) > 0): ?>
                        <?php foreach ($demandes as $demande): 
                            $name = htmlspecialchars($demande['nom'] . ' ' . $demande['prenom']);
                            $dest = htmlspecialchars($demande['destination']);
                            $motif = htmlspecialchars($demande['motif_mission']);
                            $role = htmlspecialchars($demande['direction']);
                            $service = "PARC AUTO";
                            $direction = htmlspecialchars($demande['direction']);
                            $date = htmlspecialchars($demande['date_depart']);
                            
                            // Calculer la durée
                            $dateDepart = new DateTime($demande['date_depart']);
                            $dateRetour = new DateTime($demande['date_retour']);
                            $interval = $dateDepart->diff($dateRetour);
                            $days = $interval->days > 0 ? $interval->days : 1;
                            
                            $initial = strtoupper(substr($demande['nom'], 0, 1));
                            $id = $demande['id'];
                        ?>
                        <tr id="row-demand-<?= $id ?>" class="attente-row row-animate hover:bg-slate-50/50" data-name="<?= $name ?>" data-dest="<?= $dest ?>" data-role="<?= $role ?>" data-motif="<?= $motif ?>" data-date="<?= $date ?>" data-days="<?= $days ?>" data-service="<?= $service ?>" data-direction="<?= $direction ?>" data-matricule="<?= htmlspecialchars($demande['matricule'] ?? '') ?>">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#0066cc] flex items-center justify-center font-bold text-sm"><?= $initial ?></div>
                                    <div>
                                        <div class="text-sm font-medium text-[#001737]"><?= $name ?></div>
                                        <div class="text-[11px] text-[#8799ae] tracking-wide uppercase"><?= $role ?></div>
                                        <div class="text-[10px] text-[#8799ae] mt-1 uppercase">Service: <?= $service ?> &bull; Direction: <?= $direction ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm font-medium text-[#001737]"><?= $dest ?></div>
                                <div class="text-xs text-[#8799ae]">"<?= $motif ?>"</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-sm text-[#001737]"><?= $date ?></div>
                                <div class="text-[11px] font-medium text-[#8799ae]"><?= $days ?> JOURS</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-medium bg-amber-50 text-amber-600 border border-amber-200/40">
                                    EN ATTENTE
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button onclick="openAffectationModal('<?= addslashes($name) ?>', '<?= addslashes($dest) ?>', 'row-demand-<?= $id ?>')" class="px-4 py-2 bg-[#0066cc] text-white text-xs font-medium tracking-wide uppercase rounded-xl hover:bg-[#0055b3] transition-all shadow-sm cursor-pointer">
                                        ACCEPTER
                                    </button>
                                    <button onclick="refuseDemandeDirect('<?= addslashes($name) ?>', '<?= addslashes($dest) ?>', 'row-demand-<?= $id ?>')" class="p-2 text-slate-400 hover:text-red-500 rounded-xl transition-all cursor-pointer" title="Refuser la demande">
                                        ✕
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="py-8 text-center text-[#8799ae]">Aucune demande en attente</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="tab-historique" class="hidden w-full bg-white rounded-3xl border border-[#e4ebf4] overflow-hidden shadow-sm transition-all">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[#e4ebf4] bg-[#fcfdfe]">
                        <th class="py-4 px-6 text-xs font-semibold text-[#8799ae] tracking-wider">COLLABORATEUR</th>
                        <th class="py-4 px-6 text-xs font-semibold text-[#8799ae] tracking-wider">DESTINATION / MOTIF</th>
                        <th class="py-4 px-6 text-xs font-semibold text-[#8799ae] tracking-wider">PÉRIODE</th>
                        <th class="py-4 px-6 text-xs font-semibold text-[#8799ae] tracking-wider">STATUS</th>
                        <th class="py-4 px-6 text-xs font-semibold text-[#8799ae] tracking-wider">AFFECTATION / DÉCISION</th>
                        <th class="py-4 px-6 text-xs font-semibold text-[#8799ae] tracking-wider">RAPPORT</th>
                        <th class="py-4 px-6 text-xs font-semibold text-[#8799ae] tracking-wider text-center">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="historique-tbody" class="divide-y divide-[#e4ebf4]/60">
                </tbody>
            </table>
        </div>
    </div>

    <div id="tab-combines" class="hidden w-full bg-transparent overflow-hidden transition-all flex flex-col gap-4">
        <!-- Contenu généré en JS -->
        <div id="combines-container" class="flex flex-col gap-4"></div>
    </div>

    <div id="empty-state" class="hidden bg-white border border-[#e4ebf4] rounded-3xl p-16 text-center flex-col items-center justify-center min-h-[300px]">
        <h3 class="text-sm font-medium text-[#001737] uppercase tracking-wider">Aucune demande trouvée</h3>
    </div>

</div>

<div id="affectation-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl w-full max-w-2xl shadow-2xl border border-[#e4ebf4] overflow-hidden transform transition-all flex flex-col">
        
        <div class="p-6 md:p-8 border-b border-[#e4ebf4]">
            <h2 class="text-lg font-bold text-[#001737] tracking-tight mb-1">AFFECTATION VÉHICULE</h2>
            <p class="text-xs text-[#8799ae]">Mission: <span id="modal-mission-dest" class="text-[#001737] font-medium"></span> pour <span id="modal-collaborator-name" class="text-[#001737] font-medium"></span></p>
        </div>

        <div class="p-6 md:p-8">
            <div class="relative">
                <div id="available-vehicles-list" class="space-y-3 max-h-[340px] overflow-y-auto pr-2 scroll-smooth">
                    <?php if (!empty($availableVehicles)): ?>
                        <?php foreach ($availableVehicles as $vehicle): ?>
                            <?php
                                $vehicleName = htmlspecialchars($vehicle['marque'], ENT_QUOTES);
                                $vehiclePlate = htmlspecialchars($vehicle['matricule'], ENT_QUOTES);
                                $jsName = str_replace("'", "\\'", $vehicleName);
                                $jsPlate = str_replace("'", "\\'", $vehiclePlate);
                            ?>
                            <div onclick="selectVehicle(this, '<?= $jsName ?>', '<?= $jsPlate ?>')" class="vehicle-card border border-[#e4ebf4] rounded-2xl p-4 flex items-center justify-between cursor-pointer transition-all bg-white hover:bg-slate-50">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-[#f4f7fa] rounded-xl flex items-center justify-center text-[10px] text-[#8799ae]">VOIT</div>
                                    <div>
                                        <h4 class="text-sm font-medium text-[#001737]"><?= $vehicleName ?></h4>
                                        <span class="text-[11px] text-[#8799ae]">Matricule</span>
                                    </div>
                                </div>
                                <span class="bg-[#f4f7fa] border border-[#e4ebf4] text-[#001737] px-2.5 py-1 rounded-lg text-xs font-medium"><?= $vehiclePlate ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-12 text-sm text-[#8799ae] bg-slate-50 border border-[#e4ebf4] rounded-3xl">
                            Aucun véhicule disponible pour l'instant.
                        </div>
                    <?php endif; ?>
                </div>
                <div class="absolute right-0 top-4 flex flex-col gap-2">
                    <button type="button" onclick="scrollAvailableVehicles(-1)" class="w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-500 hover:text-slate-800 hover:border-slate-300 transition">
                        ▲
                    </button>
                    <button type="button" onclick="scrollAvailableVehicles(1)" class="w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center text-slate-500 hover:text-slate-800 hover:border-slate-300 transition">
                        ▼
                    </button>
                </div>
            </div>
        </div>

        <div class="p-6 md:p-8 bg-slate-50 border-t border-[#e4ebf4] flex justify-between items-center">
            <button onclick="closeAffectationModal()" class="text-xs text-[#8799ae] hover:text-[#001737] uppercase cursor-pointer">Annuler</button>
            <button id="btn-submit-affectation" onclick="confirmAndGenerate()" disabled
                class="px-6 py-3 rounded-2xl text-xs font-medium tracking-wide uppercase transition-all bg-[#f4f7fa] text-[#8799ae] border border-[#e4ebf4] cursor-not-allowed">
                CONFIRMER ET GÉNÉRER PDF
            </button>
        </div>
    </div>
</div>

<div id="toast-action" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
    <div class="bg-white border border-[#e4ebf4] rounded-2xl p-4 shadow-xl flex items-center gap-3 max-w-md">
        <div id="toast-icon-box" class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0"></div>
        <div>
            <p id="toast-txt" class="text-xs text-[#001737]"></p>
        </div>
    </div>
</div>

<!-- Modal Aperçu PDF -->
<div id="apercu-pdf-modal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[60] hidden flex flex-col p-4 md:p-8">
    <div class="flex justify-end gap-3 mb-4 max-w-4xl w-full mx-auto">
        <button onclick="closeApercuModal()" class="px-5 py-2 bg-slate-700 text-white text-xs font-bold tracking-wider uppercase rounded-xl hover:bg-slate-600 transition-all cursor-pointer">Retour</button>
        <button onclick="printCombinedDoc()" class="px-5 py-2 bg-blue-600 text-white text-xs font-bold tracking-wider uppercase rounded-xl hover:bg-blue-500 shadow-lg flex items-center gap-2 transition-all cursor-pointer">
            🖨 Imprimer Direct
        </button>
    </div>
    <div id="apercu-content" class="bg-white mx-auto max-w-4xl w-full h-full overflow-hidden rounded-md shadow-2xl">
        <iframe id="pdf-preview-iframe" class="w-full h-full border-0"></iframe>
    </div>
</div>

<style>
    .row-animate { transition: all 0.3s ease-out; }
    .row-fade-out { opacity: 0; transform: translateX(30px); }
    .row-fade-in { animation: fadeInRight 0.4s ease-out forwards; }
    @keyframes fadeInRight {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
</style>

<script>
    let currentTargetRowId = null;
    let selectedVehicleName = "";
    let selectedVehiclePlate = "";

    document.addEventListener("DOMContentLoaded", () => {
        loadHistoriqueFromStorage();
        updateCounts();
    });

    // ─── Persistance historique via localStorage ──────────────────────────────
    const HIST_KEY = 'parc_historique_rows';

    function saveHistoriqueToStorage() {
        const rows = document.querySelectorAll('.hist-row');
        const data = [];
        rows.forEach(row => {
            data.push({
                id:        row.id,
                name:      row.dataset.name      || '',
                dest:      row.dataset.dest      || '',
                role:      row.dataset.role      || '',
                motif:     row.dataset.motif     || '',
                date:      row.dataset.date      || '',
                days:      row.dataset.days      || '1',
                service:   row.dataset.service   || '',
                direction: row.dataset.direction || '',
                matricule: row.dataset.matricule || '',
                vehicle:   row.dataset.vehicle   || '',
                plate:     row.dataset.plate     || ''
            });
        });
        localStorage.setItem(HIST_KEY, JSON.stringify(data));
    }

    function buildHistRowHtml(d) {
        return `
            <tr id="${d.id}" class="hist-row row-animate hover:bg-slate-50/50"
                data-name="${d.name}" data-dest="${d.dest}" data-role="${d.role}"
                data-motif="${d.motif}" data-date="${d.date}" data-days="${d.days}"
                data-service="${d.service}" data-direction="${d.direction}"
                data-matricule="${d.matricule}" data-vehicle="${d.vehicle}" data-plate="${d.plate}">
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#10b981] flex items-center justify-center font-bold text-sm">${d.name[0] || '?'}</div>
                        <div>
                            <div class="text-sm font-medium text-[#001737]">${d.name}</div>
                            <div class="text-[11px] text-[#8799ae] tracking-wide uppercase">${d.role}</div>
                            <div class="text-[10px] text-[#8799ae] mt-1 uppercase">Service: ${d.service} &bull; Direction: ${d.direction}</div>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <div class="text-sm font-medium text-[#001737]">${d.dest}</div>
                    <div class="text-xs text-[#8799ae]">&quot;${d.motif}&quot;</div>
                </td>
                <td class="py-4 px-6">
                    <div class="text-sm text-[#001737]">${d.date}</div>
                    <div class="text-[11px] font-medium text-[#8799ae] uppercase">${d.days} JOURS</div>
                </td>
                <td class="py-4 px-6">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-200/30">
                        VALID&Eacute;E
                    </span>
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500 text-xs">&rarr;</div>
                        <div>
                            <div class="text-xs font-medium text-[#001737]">${d.vehicle}</div>
                            <div class="text-[10px] text-[#8799ae]">${d.plate}</div>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <a href="#" onclick="voirPdfHistorique('${d.id}'); return false;" class="inline-flex items-center text-xs font-medium text-[#0066cc] hover:underline uppercase cursor-pointer">Voir PDF</a>
                </td>
                <td class="py-4 px-6 text-center">
                    <div class="flex items-center justify-center gap-3">
                        <button onclick="retablirRow('${d.id}','${d.name}','${d.dest}','${d.role}','${d.motif}','${d.date}','${d.days}','${d.service}','${d.direction}','${d.matricule}')"
                            class="p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-xl text-base transform hover:scale-115 transition-all font-medium cursor-pointer" title="R&eacute;tablir la demande">&x021F2;</button>
                        <button onclick="deleteHistoriqueRow('${d.id}')"
                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all font-medium cursor-pointer" title="Supprimer d&eacute;finitivement">&#128465;</button>
                    </div>
                </td>
            </tr>`;
    }

    function loadHistoriqueFromStorage() {
        const stored = localStorage.getItem(HIST_KEY);
        if (!stored) return;
        try {
            const rows = JSON.parse(stored);
            const tbody = document.getElementById('historique-tbody');
            rows.forEach(d => {
                tbody.insertAdjacentHTML('beforeend', buildHistRowHtml(d));
            });
        } catch(e) { console.warn('Historique storage error', e); }
    }

    function openAffectationModal(collaborator, destination, rowId) {
        currentTargetRowId = rowId;
        document.getElementById('modal-collaborator-name').textContent = collaborator;
        document.getElementById('modal-mission-dest').textContent = destination;
        
        document.querySelectorAll('.vehicle-card').forEach(card => {
            card.className = "vehicle-card border border-[#e4ebf4] rounded-2xl p-4 flex items-center justify-between cursor-pointer transition-all bg-white hover:bg-slate-50";
        });
        
        const submitBtn = document.getElementById('btn-submit-affectation');
        submitBtn.disabled = true;
        submitBtn.className = "px-6 py-3 rounded-2xl text-xs font-medium tracking-wide uppercase transition-all bg-[#f4f7fa] text-[#8799ae] border border-[#e4ebf4] cursor-not-allowed";

        document.getElementById('affectation-modal').classList.remove('hidden');
    }

    function closeAffectationModal() {
        document.getElementById('affectation-modal').classList.add('hidden');
    }

    function scrollAvailableVehicles(direction) {
        const list = document.getElementById('available-vehicles-list');
        if (!list) return;
        const amount = 120 * direction;
        list.scrollBy({ top: amount, behavior: 'smooth' });
    }

    function selectVehicle(element, name, plate) {
        document.querySelectorAll('.vehicle-card').forEach(card => {
            card.className = "vehicle-card border border-[#e4ebf4] rounded-2xl p-4 flex items-center justify-between cursor-pointer transition-all bg-white hover:bg-slate-50";
        });

        element.className = "vehicle-card border-2 border-[#0066cc] rounded-2xl p-4 flex items-center justify-between cursor-pointer transition-all bg-[#f0f6ff]/40";
        
        selectedVehicleName = name;
        selectedVehiclePlate = plate;

        const submitBtn = document.getElementById('btn-submit-affectation');
        submitBtn.disabled = false;
        submitBtn.className = "px-6 py-3 rounded-2xl text-xs font-medium tracking-wide uppercase transition-all bg-[#0066cc] hover:bg-[#0055b3] text-white shadow-md cursor-pointer";
    }

    async function confirmAndGenerate() {
        if (!currentTargetRowId) return;
        const originalRow = document.getElementById(currentTargetRowId);
        if (!originalRow) return;

        const dbId = currentTargetRowId.replace('row-demand-', '');

        const name = originalRow.dataset.name;
        const role = originalRow.dataset.role;
        const dest = originalRow.dataset.dest;
        const motif = originalRow.dataset.motif;
        const date = originalRow.dataset.date;
        const days = originalRow.dataset.days;
        const service = originalRow.dataset.service || 'PARC AUTO';
        const direction = originalRow.dataset.direction || 'DIRECTION GÉNÉRALE';
        const matricule = originalRow.dataset.matricule || '';

        const submitBtn = document.getElementById('btn-submit-affectation');
        const oldText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'GÉNÉRATION...';

        try {
            const response = await fetch('accept_mission.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: dbId,
                    vehicle_name: selectedVehicleName,
                    vehicle_plate: selectedVehiclePlate
                })
            });
            const result = await response.json();

            if (result.success) {
                const uniqueId = 'hist-row-' + Date.now();
                const histTbody = document.getElementById('historique-tbody');
                const newRowHtml = `
                    <tr id="${uniqueId}" class="hist-row row-animate row-fade-in hover:bg-slate-50/50" data-name="${name}" data-dest="${dest}" data-role="${role}" data-motif="${motif}" data-date="${date}" data-days="${days}" data-service="${service}" data-direction="${direction}" data-matricule="${matricule}" data-vehicle="${selectedVehicleName}" data-plate="${selectedVehiclePlate}">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-[#10b981] flex items-center justify-center font-bold text-sm">${name[0]}</div>
                                <div>
                                    <div class="text-sm font-medium text-[#001737]">${name}</div>
                                    <div class="text-[11px] text-[#8799ae] tracking-wide uppercase">${role}</div>
                                    <div class="text-[10px] text-[#8799ae] mt-1 uppercase">Service: ${service} • Direction: ${direction}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="text-sm font-medium text-[#001737]">${dest}</div>
                            <div class="text-xs text-[#8799ae]">"${motif}"</div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="text-sm text-[#001737]">${date}</div>
                            <div class="text-[11px] font-medium text-[#8799ae] uppercase">${days} JOURS</div>
                        </td>
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-medium bg-emerald-50 text-emerald-600 border border-emerald-200/30">
                                VALIDÉE
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500 text-xs">➔</div>
                                <div>
                                    <div class="text-xs font-medium text-[#001737]">${selectedVehicleName}</div>
                                    <div class="text-[10px] text-[#8799ae]">${selectedVehiclePlate}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <a href="#" onclick="voirPdfHistorique('${uniqueId}'); return false;" class="inline-flex items-center text-xs font-medium text-[#0066cc] hover:underline uppercase cursor-pointer">Voir PDF</a>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <div class="flex items-center justify-center gap-3">
                                <button onclick="retablirRow('${uniqueId}', '${name}', '${dest}', '${role}', '${motif}', '${date}', '${days}', '${service}', '${direction}', '${matricule}')" 
                                    class="p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-xl text-base transform hover:scale-115 transition-all font-medium cursor-pointer" title="Rétablir la demande">⟲</button>
                                <button onclick="deleteHistoriqueRow('${uniqueId}')" 
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all font-medium cursor-pointer" title="Supprimer définitivement">🗑</button>
                            </div>
                        </td>
                    </tr>
                `;
                histTbody.insertAdjacentHTML('beforeend', newRowHtml);
                saveHistoriqueToStorage();

                originalRow.classList.add('row-fade-out');
                setTimeout(() => {
                    originalRow.remove();
                    updateCounts();
                    showToast(result.message, "bg-emerald-50 text-emerald-600");
                    if (result.message.includes('Failed')) {
                        alert(result.message); // Show prominent error if email failed
                    }
                }, 300);

                closeAffectationModal();
            } else {
                alert("Erreur: " + result.message);
            }
        } catch (e) {
            console.error(e);
            alert("Erreur de connexion au serveur.");
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = oldText;
        }
    }

    function refuseDemandeDirect(collaborator, destination, rowId) {
        const row = document.getElementById(rowId);
        if(row) {
            row.classList.add('row-fade-out');
            setTimeout(() => {
                row.remove();
                updateCounts();
                showToast(`Demande de ${collaborator} refusée avec succès.`, "bg-rose-50 text-rose-600");
            }, 300);
        }
    }

    function deleteHistoriqueRow(rowId) {
        const targetRow = document.getElementById(rowId);
        if (targetRow) {
            targetRow.classList.add('row-fade-out');
            setTimeout(() => {
                targetRow.remove();
                saveHistoriqueToStorage();
                updateCounts();
                showToast("Mission supprim\u00e9e d\u00e9finitivement.", "bg-slate-100 text-slate-600");
            }, 300);
        }
    }

    function retablirRow(rowId, name, dest, role, motif, date, days, service = 'PARC AUTO', direction = 'DIRECTION GÉNÉRALE', matricule = '') {
        const targetRow = document.getElementById(rowId);
        if (!targetRow) return;

        const attenteId = 'row-demand-' + Date.now();
        const attenteTbody = document.getElementById('attente-tbody');
        
        const restoredHtml = `
            <tr id="${attenteId}" class="attente-row row-animate row-fade-in hover:bg-slate-50/50" data-name="${name}" data-dest="${dest}" data-role="${role}" data-motif="${motif}" data-date="${date}" data-days="${days}" data-service="${service}" data-direction="${direction}" data-matricule="${matricule}">
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#0066cc] flex items-center justify-center font-bold text-sm">${name[0]}</div>
                        <div>
                            <div class="text-sm font-medium text-[#001737]">${name}</div>
                            <div class="text-[11px] text-[#8799ae] tracking-wide uppercase">${role}</div>
                            <div class="text-[10px] text-[#8799ae] mt-1 uppercase">Service: ${service} • Direction: ${direction}</div>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <div class="text-sm font-medium text-[#001737]">${dest}</div>
                    <div class="text-xs text-[#8799ae]">"${motif}"</div>
                </td>
                <td class="py-4 px-6">
                    <div class="text-sm text-[#001737]">${date}</div>
                    <div class="text-[11px] font-medium text-[#8799ae] uppercase">${days} JOURS</div>
                </td>
                <td class="py-4 px-6">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-medium bg-amber-50 text-amber-600 border border-amber-200/40">
                        EN ATTENTE
                    </span>
                </td>
                <td class="py-4 px-6 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <button onclick="openAffectationModal('${name}', '${dest}', '${attenteId}')" class="px-3.5 py-2 bg-[#0066cc] text-white text-xs font-medium tracking-wide uppercase rounded-xl hover:bg-[#0055b3] transition-all shadow-sm cursor-pointer">
                            ACCEPTER
                        </button>
                        <button onclick="refuseDemandeDirect('${name}', '${dest}', '${attenteId}')" class="p-2 text-slate-400 hover:text-red-500 rounded-xl transition-all cursor-pointer" title="Refuser la demande">
                            ✕
                        </button>
                    </div>
                </td>
            </tr>
        `;
        attenteTbody.insertAdjacentHTML('beforeend', restoredHtml);

        targetRow.classList.add('row-fade-out');
        setTimeout(() => {
            targetRow.remove();
            updateCounts();
            showToast("Mission renvoyée en attente !", "bg-amber-50 text-amber-600");
        }, 300);
    }

    function showToast(msg, classes) {
        const toast = document.getElementById('toast-action');
        const box = document.getElementById('toast-icon-box');
        document.getElementById('toast-txt').textContent = msg;
        
        box.className = `w-8 h-8 rounded-xl flex items-center justify-center shrink-0 ${classes}`;
        box.innerHTML = `✓`;

        toast.classList.remove('translate-y-20', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');

        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 3500);
    }

    function updateCounts() {
        const attenteRows = document.querySelectorAll('.attente-row').length;
        const fontRows = document.querySelectorAll('.hist-row').length;
        
        document.getElementById('badge-attente-top').textContent = attenteRows;
        document.getElementById('badge-historique-top').textContent = fontRows;

        generateCombinedOrdersUI();
        const combinesGroups = document.querySelectorAll('.combines-group-card').length;
        if(document.getElementById('badge-combines-top')) {
            document.getElementById('badge-combines-top').textContent = combinesGroups;
        }

        const isHistoriqueActive = !document.getElementById('tab-historique').classList.contains('hidden');
        const isCombinesActive = !document.getElementById('tab-combines').classList.contains('hidden');
        const activeCount = isHistoriqueActive ? fontRows : attenteRows;
        
        // Mettre à jour le compteur en prenant en compte le filtre actuel
        let visibleCount = 0;
        const rows = document.querySelectorAll(isHistoriqueActive ? '.hist-row' : '.attente-row');
        rows.forEach(row => {
            if (row.style.display !== 'none') visibleCount++;
        });
        
        if (isCombinesActive) {
            visibleCount = combinesGroups;
        }
        document.getElementById('total-count').textContent = visibleCount;

        const activeContainer = isHistoriqueActive ? document.getElementById('tab-historique') : 
                                (isCombinesActive ? document.getElementById('tab-combines') : document.getElementById('tab-attente'));
        const emptyState = document.getElementById('empty-state');

        if (visibleCount === 0) {
            if(activeContainer) activeContainer.classList.add('hidden');
            emptyState.classList.remove('hidden');
        } else {
            if(activeContainer) activeContainer.classList.remove('hidden');
            emptyState.classList.add('hidden');
        }
    }

    function switchTab(tab) {
        const btnAttente = document.getElementById('btn-tab-attente');
        const btnHistorique = document.getElementById('btn-tab-historique');
        const btnCombines = document.getElementById('btn-tab-combines');
        
        document.getElementById('tab-attente').classList.add('hidden');
        document.getElementById('tab-historique').classList.add('hidden');
        document.getElementById('tab-combines').classList.add('hidden');
        document.getElementById('search-input').value = '';

        // Reset display of all rows on tab switch
        document.querySelectorAll('.attente-row, .hist-row').forEach(row => row.style.display = '');

        const defaultClass = "tab-btn text-[#8799ae] hover:text-[#001737] font-medium text-xs tracking-wide px-5 py-2 rounded-xl flex items-center gap-2 transition-all cursor-pointer";
        const activeClass = "tab-btn bg-white text-[#001737] font-medium text-xs tracking-wide px-5 py-2 rounded-xl shadow-sm border border-[#e4ebf4]/60 flex items-center gap-2 transition-all cursor-pointer";

        btnAttente.className = defaultClass;
        btnHistorique.className = defaultClass;
        if(btnCombines) btnCombines.className = defaultClass;

        if (tab === 'attente') {
            btnAttente.className = activeClass;
            document.getElementById('tab-attente').classList.remove('hidden');
        } else if (tab === 'historique') {
            btnHistorique.className = activeClass;
            document.getElementById('tab-historique').classList.remove('hidden');
        } else if (tab === 'combines') {
            if(btnCombines) btnCombines.className = activeClass;
            document.getElementById('tab-combines').classList.remove('hidden');
        }
        updateCounts();
    }

    function filterTable() {
        const q = document.getElementById('search-input').value.toLowerCase();
        const isHist = !document.getElementById('tab-historique').classList.contains('hidden');
        const rows = document.querySelectorAll(isHist ? '.hist-row' : '.attente-row');

        rows.forEach(row => {
            const text = (row.dataset.name + ' ' + row.dataset.dest).toLowerCase();
            row.style.display = text.includes(q) ? '' : 'none';
        });

        updateCounts(); // recalculer le total après filtrage
    }

    let globalGroupedOrders = {};

    function generateCombinedOrdersUI() {
        const histRows = document.querySelectorAll('.hist-row');
        const groups = {};

        histRows.forEach(row => {
            if (row.style.display === 'none') return; // on ignore ce qui est caché par recherche
            const date = row.dataset.date;
            if(!groups[date]) groups[date] = [];
            
            // Extracting vehicle info: it's inside the row
            const vehNameElem = row.querySelector('td:nth-child(5) .text-xs.font-medium');
            const vehPlateElem = row.querySelector('td:nth-child(5) .text-\\[10px\\]');
            
            const vehName = vehNameElem ? vehNameElem.textContent.trim() : 'Non affecté';
            const vehPlate = vehPlateElem ? vehPlateElem.textContent.trim() : '';

            groups[date].push({
                name: row.dataset.name,
                role: row.dataset.role,
                dest: row.dataset.dest,
                motif: row.dataset.motif,
                days: row.dataset.days,
                service: row.dataset.service || 'PARC AUTO',
                direction: row.dataset.direction || 'DIRECTION GÉNÉRALE',
                matricule: row.dataset.matricule || '',
                vehicle: vehName,
                plate: vehPlate
            });
        });

        globalGroupedOrders = groups;
        const container = document.getElementById('combines-container');
        if(!container) return;
        container.innerHTML = '';

        const dates = Object.keys(groups).sort((a,b) => new Date(a) - new Date(b));

        dates.forEach(date => {
            const missions = groups[date];
            
            let tableRows = '';
            missions.forEach(m => {
                tableRows += `
                    <tr class="border-t border-[#e4ebf4] bg-white">
                        <td class="py-3 px-4 text-sm font-medium text-[#001737]">${m.name} <span class="block text-[10px] font-normal text-[#8799ae] uppercase">${m.role}</span></td>
                        <td class="py-3 px-4 text-sm text-[#001737]">${m.dest}</td>
                        <td class="py-3 px-4 text-sm italic text-[#8799ae]">"${m.motif}"</td>
                        <td class="py-3 px-4">
                            <span class="inline-block bg-slate-100 text-slate-700 px-2 py-1 rounded text-xs font-medium">${m.vehicle}</span>
                            <span class="block text-[10px] text-[#8799ae] mt-1">${m.plate}</span>
                        </td>
                    </tr>
                `;
            });

            const card = `
                <div class="combines-group-card bg-white rounded-3xl border border-[#e4ebf4] shadow-sm overflow-hidden mb-2">
                    <div class="bg-[#fcfdfe] px-6 py-4 border-b border-[#e4ebf4] flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-bold text-[#001737] flex items-center gap-2">
                                📅 Date du déplacement : <span class="text-purple-700">${date}</span>
                            </h3>
                            <p class="text-xs text-[#8799ae] mt-1">${missions.length} mission(s) enregistrée(s) pour cette journée.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="previewCombinedDoc('${date}')" class="px-4 py-2 bg-white text-blue-600 border border-blue-200 text-xs font-bold tracking-wide uppercase rounded-xl hover:bg-blue-50 transition-all cursor-pointer">
                                Voir PDF
                            </button>
                            <button onclick="directPrintDoc('${date}')" class="px-4 py-2 bg-blue-600 text-white text-xs font-bold tracking-wide uppercase rounded-xl hover:bg-blue-500 shadow-sm transition-all flex items-center gap-2 cursor-pointer">
                                🖨 Imprimer
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead class="bg-[#f4f7fa]">
                                <tr>
                                    <th class="py-3 px-4 text-[10px] font-bold text-[#8799ae] uppercase tracking-wider">Agent Engagé</th>
                                    <th class="py-3 px-4 text-[10px] font-bold text-[#8799ae] uppercase tracking-wider">Destination</th>
                                    <th class="py-3 px-4 text-[10px] font-bold text-[#8799ae] uppercase tracking-wider">Motif</th>
                                    <th class="py-3 px-4 text-[10px] font-bold text-[#8799ae] uppercase tracking-wider">Véhicule Affecté</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${tableRows}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', card);
        });
    }

    // ─── buildPdfBlob : pour l'onglet DEMANDES COMBINÉS ──────────────────────
    async function buildPdfBlob(date) {
        const missions = globalGroupedOrders[date];
        if (!missions || missions.length === 0) return null;
        try {
            return await buildSinglePdfBlob({
                name:      missions[0].name,
                matricule: missions[0].matricule,
                role:      missions[0].role,
                service:   missions[0].service,
                direction: missions[0].direction,
                dest:      missions[0].dest,
                motif:     missions[0].motif,
                vehicle:   missions[0].vehicle,
                plate:     missions[0].plate,
                days:      missions[0].days,
                date:      date
            });
        } catch (e) {
            console.error(e);
            alert("Erreur lors de la g\u00e9n\u00e9ration du PDF.");
            return null;
        }
    }

    // ─── buildSinglePdfBlob : remplit le template PDF original ───────────────
    async function buildSinglePdfBlob(rowData) {
        try {
            const url = 'Demande ordre mission conbiné.pdf';
            const res = await fetch(url);
            if(!res.ok) throw new Error("Erreur chargement PDF");
            const existingPdfBytes = await res.arrayBuffer();

            const finalPdf = await PDFLib.PDFDocument.create();
            const templateDoc = await PDFLib.PDFDocument.load(existingPdfBytes);
            
            const [page1] = await finalPdf.copyPages(templateDoc, [0]);
            finalPdf.addPage(page1);

            const fontSize = 10;
            const textColor = PDFLib.rgb(0, 0, 0);
            const font = await finalPdf.embedFont(PDFLib.StandardFonts.Helvetica);

            const P1_NOM_X = 200;        const P1_NOM_Y = 725;
            const P1_PRENOM_X = 367;     const P1_PRENOM_Y = 725;
            const P1_MATRIC_X = 180;     const P1_MATRIC_Y = 687;
            const P1_FONCT_X = 328;      const P1_FONCT_Y = 687;
            const P1_SERVICE_X = 180;    const P1_SERVICE_Y = 650;
            const P1_DIRECT_X = 180;     const P1_DIRECT_Y = 613;
            const P1_ACCOMP_X = 180;     const P1_ACCOMP_Y = 577;
            const P1_TRANS_X = 225;      const P1_TRANS_Y = 541;
            const P1_VEHIC_X = 225;      const P1_VEHIC_Y = 505;
            const P1_LIEU_X = 225;       const P1_LIEU_Y = 468;
            const P1_OBJET_X = 180;      const P1_OBJET_Y = 434;
            const P1_DATE_D_X = 245;     const P1_DATE_D_Y = 399;
            const P1_DATE_R_X = 245;     const P1_DATE_R_Y = 363;

            const writeField = (page, text, x, y) => {
                page.drawText(text || '', { x, y, size: fontSize, font, color: textColor });
            };

            const nameParts = rowData.name.trim().split(' ');
            const nom = nameParts.length > 1 ? nameParts.slice(-1)[0].toUpperCase() : rowData.name.toUpperCase();
            const prenom = nameParts.length > 1 ? nameParts.slice(0, -1).join(' ') : '';
            const matriculeVal = rowData.matricule || ("M" + String(Math.floor(Math.random() * 10000)).padStart(4, '0'));

            writeField(page1, nom, P1_NOM_X, P1_NOM_Y);
            writeField(page1, prenom, P1_PRENOM_X, P1_PRENOM_Y);
            writeField(page1, matriculeVal, P1_MATRIC_X, P1_MATRIC_Y);
            writeField(page1, rowData.role || '', P1_FONCT_X, P1_FONCT_Y);
            writeField(page1, rowData.service || 'PARC AUTO', P1_SERVICE_X, P1_SERVICE_Y);
            writeField(page1, rowData.direction || 'DIRECTION GÉNÉRALE', P1_DIRECT_X, P1_DIRECT_Y);
            writeField(page1, '-', P1_ACCOMP_X, P1_ACCOMP_Y);
            writeField(page1, "Véhicule de service", P1_TRANS_X, P1_TRANS_Y);
            writeField(page1, (rowData.vehicle || '') + ' / ' + (rowData.plate || ''), P1_VEHIC_X, P1_VEHIC_Y);
            writeField(page1, rowData.dest || '', P1_LIEU_X, P1_LIEU_Y);
            writeField(page1, rowData.motif || '', P1_OBJET_X, P1_OBJET_Y);
            writeField(page1, rowData.date || '', P1_DATE_D_X, P1_DATE_D_Y);

            let dateRetour = rowData.date || '';
            if (rowData.days && parseInt(rowData.days) > 1) {
                const d = new Date(rowData.date);
                d.setDate(d.getDate() + parseInt(rowData.days) - 1);
                dateRetour = d.toISOString().split('T')[0];
            }
            writeField(page1, dateRetour, P1_DATE_R_X, P1_DATE_R_Y);

            const pdfBytes = await finalPdf.save();
            const blob = new Blob([pdfBytes], { type: 'application/pdf' });
            return URL.createObjectURL(blob);
        } catch (e) {
            console.error(e);
            alert("Erreur lors de la génération du PDF.");
            return null;
        }
    }

    async function voirPdfHistorique(rowId) {
        const row = document.getElementById(rowId);
        if (!row) return;
        const rowData = {
            name: row.dataset.name || '',
            role: row.dataset.role || '',
            dest: row.dataset.dest || '',
            motif: row.dataset.motif || '',
            date: row.dataset.date || '',
            days: row.dataset.days || '1',
            service: row.dataset.service || 'PARC AUTO',
            direction: row.dataset.direction || 'DIRECTION GÉNÉRALE',
            matricule: row.dataset.matricule || '',
            vehicle: row.dataset.vehicle || '',
            plate: row.dataset.plate || ''
        };
        const blobUrl = await buildSinglePdfBlob(rowData);
        if (blobUrl) {
            currentPdfBlobUrl = blobUrl;
            document.getElementById('pdf-preview-iframe').src = blobUrl;
            document.getElementById('apercu-pdf-modal').classList.remove('hidden');
        }
    }

    let currentPdfBlobUrl = null;

    async function previewCombinedDoc(date) {
        const blobUrl = await buildPdfBlob(date);
        if(blobUrl) {
            currentPdfBlobUrl = blobUrl;
            document.getElementById('pdf-preview-iframe').src = blobUrl;
            document.getElementById('apercu-pdf-modal').classList.remove('hidden');
        }
    }

    function closeApercuModal() {
        document.getElementById('apercu-pdf-modal').classList.add('hidden');
    }

    function printCombinedDoc() {
        if(currentPdfBlobUrl) {
            // Ouvrir le blob dans un nouvel onglet pour l'imprimer
            window.open(currentPdfBlobUrl, '_blank');
        }
    }

    async function directPrintDoc(date) {
        const blobUrl = await buildPdfBlob(date);
        if(blobUrl) {
            window.open(blobUrl, '_blank');
        }
    }
</script>

<?php 
echo '</main></div></div></body></html>'; 
?>
