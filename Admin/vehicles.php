<?php
$activePage = 'vehicles';
require_once 'includes/db_connection.php';

// ── Ajouter un véhicule (POST) ────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $marque    = trim($_POST['marque']    ?? '');
    $matricule = trim($_POST['matricule'] ?? '');
    $statut    = trim($_POST['statut']    ?? 'disponible');
    $allowed   = ['disponible', 'en mission', 'en maintenance'];
    $isAjax    = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

    if ($marque && $matricule && in_array($statut, $allowed)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO vehicles (marque, matricule, statut) VALUES (?, ?, ?)");
            $stmt->execute([$marque, $matricule, $statut]);
            $newId = $pdo->lastInsertId();

            if ($isAjax) {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    'id'        => $newId,
                    'marque'    => $marque,
                    'matricule' => $matricule,
                    'statut'    => $statut
                ]);
                exit();
            }

            header('Location: ' . $_SERVER['PHP_SELF'] . '?ok=1');
            exit();
        } catch (PDOException $e) {
            $errMsg = 'Ce matricule existe déjà ou une erreur est survenue.';
        }
    } else {
        $errMsg = 'Veuillez remplir la marque, le matricule et le statut.';
    }
}

// ── Changer statut (POST) ─────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'status') {
    $id     = intval($_POST['id']     ?? 0);
    $statut = trim($_POST['statut']   ?? '');
    $allowed = ['disponible', 'en mission', 'en maintenance'];
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    
    if ($id && in_array($statut, $allowed)) {
        $pdo->prepare("UPDATE vehicles SET statut=? WHERE id=?")->execute([$statut, $id]);
        if ($isAjax) {
            http_response_code(200);
            exit();
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']); exit();
}

// ── Supprimer (POST) ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = intval($_POST['id'] ?? 0);
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';
    
    if ($id) {
        $pdo->prepare("DELETE FROM vehicles WHERE id=?")->execute([$id]);
        if ($isAjax) {
            http_response_code(200);
            exit();
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']); exit();
}

// ── Lecture depuis la base de données ─────────────────────────────────────────
$vehicles = $pdo->query("SELECT * FROM vehicles ORDER BY id DESC")->fetchAll();

include 'includes/layout.php';
?>

<div id="toast-container" class="fixed bottom-5 right-5 z-[99999] flex flex-col gap-2"></div>

<?php if (isset($_GET['ok'])): ?>
<div class="flex items-center justify-between gap-3 bg-green-50 border border-green-200 text-green-700 rounded-2xl px-5 py-3 mb-6 text-sm font-semibold shadow-sm overflow-hidden">
    <div class="flex items-center gap-3">
        <div class="w-6 h-6 rounded-full bg-green-500/10 flex items-center justify-center text-green-600 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <span>Véhicule ajouté avec succès dans la base de données.</span>
    </div>
    <button onclick="this.parentElement.remove()" class="text-green-500 hover:text-green-700 cursor-pointer shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
</div>
<?php endif; ?>

<?php if (!empty($errMsg)): ?>
<div class="flex items-center justify-between gap-3 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-5 py-3 mb-6 text-sm font-semibold shadow-sm overflow-hidden">
    <div class="flex items-center gap-3">
        <div class="w-6 h-6 rounded-full bg-red-500/10 flex items-center justify-center text-red-600 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <span><?= htmlspecialchars($errMsg) ?></span>
    </div>
    <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-700 cursor-pointer shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
</div>
<?php endif; ?>

<div id="vp-filters-container" class="bg-white border-2 border-slate-200 shadow-sm rounded-full p-2.5 flex flex-row flex-wrap items-center justify-between gap-2 mb-8">
    
    <div class="flex items-center gap-2 flex-1 min-w-0">
        <div class="relative flex items-center gap-2 flex-1 min-w-0 bg-slate-50 border-2 border-slate-200 rounded-full px-3 h-11 shadow-sm">
            <span class="flex items-center justify-center text-slate-500 pl-1">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </span>
            <input id="vp-search" type="text" placeholder="Rechercher..."
                   oninput="vpFilter()"
                   class="flex-1 min-w-0 bg-transparent border-none pl-0 pr-3 py-2 text-sm text-slate-600 placeholder-slate-400 focus:outline-none focus:ring-0">
        </div>
        
        <div class="relative min-w-[150px]">
            <select id="vp-status" onchange="vpFilter()"
                    class="w-full py-2.5 pl-3 pr-8 bg-slate-50 border-2 border-slate-200 rounded-full text-sm font-medium text-slate-600 focus:border-blue-500 focus:bg-white focus:outline-none cursor-pointer shadow-sm appearance-none focus:ring-2 focus:ring-blue-500/10"
                    style="background-image:url('data:image/svg+xml;charset=utf-8,%3Csvg  width=%2214%22 height=%2214%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%2394a3b8%22 stroke-width=%222%22%3E%3Cpolyline points=%226 9 12 15 18 9%22/%3E%3C/svg%3E');background-repeat:no-repeat;background-position:right 12px center;">
                <option value="all">Tous les statuts</option>
                <option value="disponible">Disponible</option>
                <option value="en mission">En mission</option>
                <option value="en maintenance">En maintenance</option>
            </select>
        </div>
    </div>
    
    <button onclick="vpOpenModal()"
            class="flex items-center justify-center gap-2 px-4 py-2 bg-[#0066cc] hover:bg-[#0055b3] text-white text-sm font-semibold uppercase rounded-full transition shadow-md shadow-blue-500/10 cursor-pointer tracking-wider shrink-0">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Ajouter un Véhicule
    </button>
</div>

<div id="vp-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

    <?php foreach ($vehicles as $v):
        $id        = $v['id'];
        $marque    = htmlspecialchars($v['marque']);
        $matricule = htmlspecialchars($v['matricule']);
        $statut    = strtolower(trim($v['statut'] ?? 'disponible'));

        if ($statut === 'disponible') {
            $borderColor = '#00b074'; 
            $badgeBg   = '#e6f7f1';
            $badgeText = '#00b074';
            $badgeBorder = '#cdedd3';
            $dotColor  = '#00b074';
            $badgeTxt  = 'DISPONIBLE';
        } elseif ($statut === 'en mission') {
            $borderColor = '#2f80ed'; 
            $badgeBg   = '#edf4fe';
            $badgeText = '#2f80ed';
            $badgeBorder = '#d2e4fd';
            $dotColor  = '#2f80ed';
            $badgeTxt  = 'EN MISSION';
        } else {
            $borderColor = '#ef4444'; 
            $badgeBg   = '#fef2f2';
            $badgeText = '#ef4444';
            $badgeBorder = '#fee2e2';
            $dotColor  = '#ef4444';
            $badgeTxt  = 'MAINTENANCE';
        }
    ?>
    <div class="vehicle-card bg-white border border-slate-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-200"
         style="border-top: 4px solid <?= $borderColor ?>;"
         data-name="<?= strtolower($marque) ?>"
         data-plate="<?= strtolower($matricule) ?>"
         data-status="<?= $statut ?>"
         id="vehicle-card-<?= $id ?>">

        <div class="flex items-center justify-between mb-5">
            <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1 .4-1 1v11h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
            </div>
            <span class="vehicle-badge text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full border" 
                  style="background-color: <?= $badgeBg ?>; color: <?= $badgeText ?>; border-color: <?= $badgeBorder ?>;"
                  data-badge-type="<?= $statut ?>"><?= $badgeTxt ?></span>
        </div>

        <h3 class="text-xl font-black text-slate-800 tracking-tight mb-4 uppercase truncate"><?= $marque ?></h3>

        <div class="flex items-center justify-between bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 mb-6">
            <span class="font-mono text-base font-bold text-slate-700 tracking-widest"><?= $matricule ?></span>
            <span class="flex items-center gap-2 text-xs font-bold text-slate-400">
                MA
                <span class="vehicle-dot inline-block w-2.5 h-2.5 rounded-full" 
                      style="background-color: <?= $dotColor ?>;"
                      data-dot-type="<?= $statut ?>"></span>
            </span>
        </div>

        <div class="border-t border-slate-100 pt-4 flex items-center justify-between">
            <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-[#00b074]"></span>
                IOT Connecté
            </div>

            <div class="flex items-center gap-3">
                <div class="relative w-7 h-7 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:text-blue-500 hover:border-blue-300 hover:bg-slate-100 transition-all duration-200 transform hover:-translate-y-0.5 hover:scale-105" title="Changer le statut">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="2" y1="14" x2="6" y2="14"/><line x1="10" y1="8" x2="14" y2="8"/><line x1="18" y1="16" x2="22" y2="16"/></svg>
                    <select onchange="updateVehicleStatus(<?= $id ?>, this.value)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                        <option value="disponible"     <?= $statut==='disponible'     ? 'selected':'' ?>>Disponible</option>
                        <option value="en mission"     <?= $statut==='en mission'     ? 'selected':'' ?>>En mission</option>
                        <option value="en maintenance" <?= $statut==='en maintenance' ? 'selected':'' ?>>Maintenance</option>
                    </select>
                </div>

                <button type="button" onclick="deleteVehicle(<?= $id ?>)" class="w-7 h-7 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-blue-50 hover:text-blue-500 hover:border-blue-200 transition-all duration-200 transform hover:-translate-y-0.5 hover:scale-105 cursor-pointer" title="Supprimer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

    </div>
    <?php endforeach; ?>

</div>

<div id="vp-no-results" class="hidden flex-col items-center justify-center py-24 text-center max-w-sm mx-auto">
    <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-slate-300 mb-4 border border-slate-100 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/><path d="M8 11h6"/></svg>
    </div>
    <h3 class="text-sm font-extrabold text-slate-800 mb-1 uppercase tracking-tight">AUCUN VÉHICULE TROUVÉ</h3>
    <p class="text-xs font-medium text-slate-400 leading-relaxed">Ajustez vos filtres ou effectuez une nouvelle recherche pour voir la flotte active.</p>
</div>

<!-- ========================================== -->
<!-- MODAL STRUCTURE (Z-INDEX SUPER MAX & FIXED)-->
<!-- ========================================== -->
<div id="vp-overlay"
     class="fixed inset-0 z-[99999] hidden items-center justify-center p-4 bg-black/40 backdrop-blur-sm opacity-0 transition-opacity duration-300 pointer-events-none"
     onclick="vpOverlayClick(event)">

    <div id="vp-modal" 
         class="relative w-full max-w-md bg-white rounded-3xl p-6 shadow-2xl border border-slate-100 transition-all duration-300 transform scale-95 opacity-0 pointer-events-auto"
         onclick="event.stopPropagation()">
        
        <div class="flex items-center justify-between mb-6 pb-2 border-b border-slate-50">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#0066cc] flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                </div>
                <h3 class="text-md font-black text-slate-800 uppercase tracking-tight">Ajouter un véhicule</h3>
            </div>
            <button type="button" onclick="vpCloseModal()" class="w-7 h-7 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form method="POST" onsubmit="return vpAddVehicle(event)" class="space-y-4">
            <input type="hidden" name="action" value="add">
            
            <div>
                <label for="marque-input" class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Marque / Modèle</label>
                <input type="text" name="marque" id="marque-input" placeholder="Ex: Peugeot Partner, Dacia Logan..." required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-700 outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all font-medium">
            </div>

            <div>
                <label for="matricule-input" class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">N° Matricule</label>
                <input type="text" name="matricule" id="matricule-input" placeholder="Ex: 14285-A-26, etc." required
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-blue-500 rounded-xl text-sm text-slate-700 outline-none focus:bg-white focus:ring-2 focus:ring-blue-500/10 transition-all font-mono font-bold uppercase tracking-wider">
                <p class="text-[10px] text-slate-400 mt-1 font-medium">Format officiel de la plaque marocaine.</p>
            </div>

            <div>
                <label for="statut-input" class="block text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-1.5">Statut du véhicule</label>
                <select name="statut" id="statut-input" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-slate-700 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/10 transition-all font-medium">
                    <option value="disponible">Disponible</option>
                    <option value="en mission">En mission</option>
                    <option value="en maintenance">En maintenance</option>
                </select>
            </div>

            <div class="flex gap-2.5 pt-4">
                <button type="button" onclick="vpCloseModal()"
                        class="flex-1 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-600 rounded-xl text-xs font-bold uppercase tracking-wider transition-colors cursor-pointer">
                    Annuler
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 bg-[#0066cc] hover:bg-[#0055b3] text-white rounded-xl text-xs font-bold uppercase tracking-wider transition-colors cursor-pointer">
                    Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const vpOverlay = document.getElementById('vp-overlay');
const vpModal   = document.getElementById('vp-modal');
const noResults = document.getElementById('vp-no-results');

// Body injection container standard dyal page structural wrapper
let initialParent = vpOverlay.parentElement;

function showToast(message, borderClass = 'border-slate-200') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    
    toast.className = `flex items-center gap-3 px-4 py-2.5 bg-white border-2 ${borderClass} rounded-full shadow-md text-xs font-bold tracking-wide uppercase text-slate-700 transition-all duration-300 transform translate-y-4 opacity-0`;
    toast.innerHTML = `
        <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
        <span class="truncate">${message}</span>
    `;
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('translate-y-4', 'opacity-0');
    }, 10);
    
    setTimeout(() => {
        toast.classList.add('translate-y-4', 'opacity-0');
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

// ── NEW BODY-LEVEL RENDERING TO AVOID OVERFLOW CLIPPING ──
function vpOpenModal(){
    // On déplace l'overlay direct f la fin dial document body bach maybqa ta7t ta 7aja
    document.body.appendChild(vpOverlay);
    
    vpOverlay.classList.remove('hidden');
    vpOverlay.classList.add('flex');
    
    setTimeout(()=>{
        vpOverlay.classList.remove('pointer-events-none', 'opacity-0');
        vpOverlay.classList.add('opacity-100');
        
        vpModal.classList.remove('scale-95','opacity-0');
        vpModal.classList.add('scale-100','opacity-100');
    }, 20);
}

function vpCloseModal(){
    vpOverlay.classList.add('pointer-events-none');
    vpOverlay.classList.remove('opacity-100');
    vpOverlay.classList.add('opacity-0');

    vpModal.classList.remove('scale-100','opacity-100');
    vpModal.classList.add('scale-95','opacity-0');

    setTimeout(()=>{
        vpOverlay.classList.add('hidden');
        vpOverlay.classList.remove('flex');
        
        // Nraj3oh l blasto l-asliya f form ghir bach mayقعch chi mouchkil f PHP reload
        if(initialParent) {
            initialParent.appendChild(vpOverlay);
        }
    }, 300);
}

function vpOverlayClick(e) {
    if (e.target === vpOverlay) vpCloseModal();
}

function escapeHtml(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function vpAddVehicle(event) {
    event.preventDefault();
    const modalForm = vpModal.querySelector('form');
    const formData = new FormData(modalForm);
    const body = new URLSearchParams(formData);

    fetch('<?= $_SERVER['PHP_SELF'] ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body
    })
    .then(response => {
        if (!response.ok) return Promise.reject('Erreur ajout');
        return response.headers.get('content-type')?.includes('application/json')
            ? response.json()
            : response.text().then(text => text ? JSON.parse(text) : {});
    })
    .then(data => {
        appendVehicleCard(data);
        modalForm.reset();
        vpCloseModal();
        showToast('Véhicule ajouté avec succès !', 'border-emerald-200');
    })
    .catch(err => {
        console.error(err);
        showToast('Impossible d\'ajouter le véhicule.', 'border-rose-200');
    });
}

function appendVehicleCard(vehicle) {
    const config = statusConfig[vehicle.statut] || statusConfig.disponible;
    const safeMarque = escapeHtml(vehicle.marque);
    const safePlate = escapeHtml(vehicle.matricule);
    const safeStatus = escapeHtml(vehicle.statut);
    const cardHtml = `
        <div class="vehicle-card bg-white border border-slate-100 rounded-3xl p-6 shadow-sm hover:shadow-md transition-all duration-200" style="border-top: 4px solid ${config.borderColor};" data-name="${safeMarque.toLowerCase()}" data-plate="${safePlate.toLowerCase()}" data-status="${safeStatus}" id="vehicle-card-${vehicle.id}">
            <div class="flex items-center justify-between mb-5">
                <div class="w-9 h-9 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1 .4-1 1v11h2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>
                </div>
                <span class="vehicle-badge text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full border" style="background-color: ${config.badgeBg}; color: ${config.badgeText}; border-color: ${config.badgeBorder};" data-badge-type="${safeStatus}">${config.text}</span>
            </div>
            <h3 class="text-xl font-black text-slate-800 tracking-tight mb-4 uppercase truncate">${safeMarque}</h3>
            <div class="flex items-center justify-between bg-slate-50 border border-slate-100 rounded-2xl px-4 py-3 mb-6">
                <span class="font-mono text-base font-bold text-slate-700 tracking-widest">${safePlate}</span>
                <span class="flex items-center gap-2 text-xs font-bold text-slate-400">MA<span class="vehicle-dot inline-block w-2.5 h-2.5 rounded-full" style="background-color: ${config.dotColor};" data-dot-type="${vehicle.statut}"></span></span>
            </div>
            <div class="border-t border-slate-100 pt-4 flex items-center justify-between">
                <div class="flex items-center gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider"><span class="w-2 h-2 rounded-full bg-[#00b074]"></span>IOT Connecté</div>
                <div class="flex items-center gap-1.5">
                    <div class="relative w-7 h-7 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:text-blue-500 hover:border-blue-300 hover:bg-slate-100 transition-all duration-200 transform hover:-translate-y-0.5 hover:scale-105" title="Changer le statut">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="2" y1="14" x2="6" y2="14"/><line x1="10" y1="8" x2="14" y2="8"/><line x1="18" y1="16" x2="22" y2="16"/></svg>
                        <select onchange="updateVehicleStatus(${vehicle.id}, this.value)" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full">
                            <option value="disponible" ${vehicle.statut==='disponible' ? 'selected' : ''}>Disponible</option>
                            <option value="en mission" ${vehicle.statut==='en mission' ? 'selected' : ''}>En mission</option>
                            <option value="en maintenance" ${vehicle.statut==='en maintenance' ? 'selected' : ''}>En maintenance</option>
                        </select>
                    </div>
                    <button type="button" onclick="deleteVehicle(${vehicle.id})" class="w-7 h-7 rounded-full bg-slate-50 border border-slate-200 flex items-center justify-center text-slate-400 hover:bg-blue-50 hover:text-blue-500 hover:border-blue-200 transition-all duration-200 transform hover:-translate-y-0.5 hover:scale-105 cursor-pointer" title="Supprimer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>
            </div>
        </div>
    `;
    const grid = document.getElementById('vp-grid');
    if (grid) grid.insertAdjacentHTML('afterbegin', cardHtml);
    if (noResults) noResults.classList.add('hidden');
}

const statusConfig = {
    disponible: {
        borderColor: '#00b074',
        badgeBg: '#e6f7f1',
        badgeText: '#00b074',
        badgeBorder: '#cdedd3',
        dotColor: '#00b074',
        text: 'DISPONIBLE'
    },
    'en mission': {
        borderColor: '#2f80ed',
        badgeBg: '#edf4fe',
        badgeText: '#2f80ed',
        badgeBorder: '#d2e4fd',
        dotColor: '#2f80ed',
        text: 'EN MISSION'
    },
    'en maintenance': {
        borderColor: '#ef4444',
        badgeBg: '#fef2f2',
        badgeText: '#ef4444',
        badgeBorder: '#fee2e2',
        dotColor: '#ef4444',
        text: 'MAINTENANCE'
    }
};

function updateVehicleStatus(id, newStatus) {
    fetch('<?= $_SERVER['PHP_SELF'] ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'action=status&id=' + id + '&statut=' + encodeURIComponent(newStatus)
    })
    .then(response => {
        if (response.ok) {
            updateCardUI(id, newStatus);
            showToast('Statut mis à jour.', 'border-slate-200');
        }
    })
    .catch(err => console.error('Erreur:', err));
}

function updateCardUI(id, newStatus) {
    const card = document.getElementById('vehicle-card-' + id);
    if (!card) return;

    const config = statusConfig[newStatus];
    if (!config) return;

    card.setAttribute('data-status', newStatus);
    card.style.borderTop = '4px solid ' + config.borderColor;

    const badge = card.querySelector('.vehicle-badge');
    if (badge) {
        badge.setAttribute('data-badge-type', newStatus);
        badge.textContent = config.text;
        badge.style.backgroundColor = config.badgeBg;
        badge.style.color = config.badgeText;
        badge.style.borderColor = config.badgeBorder;
    }

    const dot = card.querySelector('.vehicle-dot');
    if (dot) {
        dot.setAttribute('data-dot-type', newStatus);
        dot.style.backgroundColor = config.dotColor;
    }
}

function deleteVehicle(id) {
    if (!confirm('Voulez-vous vraiment supprimer définitivement ce véhicule ?')) return;

    fetch('<?= $_SERVER['PHP_SELF'] ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'action=delete&id=' + id
    })
    .then(response => {
        if (response.ok) {
            const card = document.getElementById('vehicle-card-' + id);
            if (card) {
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.remove();
                    vpFilter();
                    showToast('Véhicule supprimé.', 'border-rose-200');
                }, 300);
            }
        }
    })
    .catch(err => console.error('Erreur:', err));
}

function vpFilter() {
    const q  = document.getElementById('vp-search').value.toLowerCase();
    const st = document.getElementById('vp-status').value;
    let visibleCount = 0;

    document.querySelectorAll('.vehicle-card').forEach(c => {
        const name     = c.getAttribute('data-name');
        const plate    = c.getAttribute('data-plate');
        const status   = c.getAttribute('data-status');
        
        const nameOk   = name.includes(q) || plate.includes(q);
        const statusOk = st === 'all' || status === st;
        
        if (nameOk && statusOk) {
            c.style.display = '';
            visibleCount++;
        } else {
            c.style.display = 'none';
        }
    });

    if (visibleCount === 0) {
        noResults.classList.remove('hidden');
        noResults.classList.add('flex');
    } else {
        noResults.classList.remove('flex');
        noResults.classList.add('hidden');
    }
}

window.addEventListener('DOMContentLoaded', vpFilter);
</script>

<?php echo '</main></div></div></body></html>'; ?>