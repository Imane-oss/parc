<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    require_once 'includes/db_connection.php';
    header('Content-Type: application/json');

    $action = $_POST['action'];
    $userId = $_SESSION['user_id'] ?? 1; // Fallback to 1 if not set

    if ($action === 'update_profile') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        try {
            $stmt = $pdo->prepare("UPDATE users SET fullname = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $userId]);
            $_SESSION['full_name'] = $name;
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }

    if ($action === 'update_password') {
        $currentPwd = $_POST['current_pwd'] ?? '';
        $newPwd = $_POST['new_pwd'] ?? '';
        
        try {
            $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $userDb = $stmt->fetch();
            
            if ($userDb && (password_verify($currentPwd, $userDb['password']) || $currentPwd === $userDb['password'])) {
                $newHash = password_hash($newPwd, PASSWORD_DEFAULT);
                $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updateStmt->execute([$newHash, $userId]);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Mot de passe actuel incorrect.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
}

$activePage = 'PARAMÈTRES';
$userName = $_SESSION['full_name'] ?? $_SESSION['name'] ?? 'Admin';
$userRole = $_SESSION['role'] ?? 'ADMIN';

include 'includes/layout.php';
?>

<style>
    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        width: 52px;
        height: 28px;
        flex-shrink: 0;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        border-radius: 28px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .toggle-slider::before {
        content: "";
        position: absolute;
        height: 22px;
        width: 22px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .toggle-switch input:checked + .toggle-slider {
        background: #0066cc;
    }
    .toggle-switch input:checked + .toggle-slider::before {
        transform: translateX(24px);
    }

    /* Photo upload overlay */
    .photo-upload-btn {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 32px;
        height: 32px;
        background: #0066cc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 3px solid white;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,102,204,0.3);
    }
    .photo-upload-btn:hover {
        background: #0052a3;
        transform: scale(1.1);
    }

    /* Card hover animation */
    .settings-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .settings-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }

    /* Input field styling */
    .info-field {
        position: relative;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 16px 14px 44px;
        transition: all 0.2s ease;
    }
    .info-field:hover {
        border-color: #cbd5e1;
        background: #f1f5f9;
    }
    .info-field .field-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    /* Password change button */
    .pwd-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.2s ease;
        width: 100%;
    }
    .pwd-btn:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .pwd-btn .pwd-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
    }

    /* Toast notification */
    .toast-settings {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 100;
        transform: translateY(80px);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
    }
    .toast-settings.show {
        transform: translateY(0);
        opacity: 1;
    }

    /* Password modal */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.5);
        backdrop-filter: blur(4px);
        z-index: 50;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .modal-overlay.active {
        display: flex;
    }
    .modal-box {
        background: white;
        border-radius: 24px;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        overflow: hidden;
        animation: modalIn 0.3s ease-out;
    }
    @keyframes modalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
    .modal-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 14px;
        color: #1e293b;
        background: #f8fafc;
        outline: none;
        transition: all 0.2s;
    }
    .modal-input:focus {
        border-color: #0066cc;
        background: white;
        box-shadow: 0 0 0 3px rgba(0,102,204,0.1);
    }

    /* Preference row hover */
    .pref-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.2s ease;
    }
    .pref-row:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
</style>

<div class="max-w-5xl mx-auto space-y-6 p-4 md:p-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight leading-none mb-1">
                Paramètres du compte
            </h1>
            <p class="text-sm text-slate-400 font-medium">Gérez vos informations personnelles et vos préférences de sécurité.</p>
        </div>
        <button id="btn-save-settings" onclick="saveSettings()" class="flex items-center gap-2 px-6 py-3 bg-[#0066cc] text-white text-sm font-semibold rounded-2xl hover:bg-[#0055b3] transition-all shadow-md shadow-blue-500/20 cursor-pointer whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Enregistrer les modifications
        </button>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Column: Profile Card -->
        <div class="lg:col-span-1">
            <div class="settings-card bg-white rounded-[28px] border border-slate-100 shadow-sm p-8 flex flex-col items-center text-center">
                <!-- Profile Photo -->
                <div class="relative mb-5">
                    <div class="w-[120px] h-[120px] rounded-full overflow-hidden border-4 border-blue-50 shadow-lg">
                        <?php if(!empty($user['profile_photo'])): ?>
                            <img id="profile-photo" src="<?php echo htmlspecialchars($user['profile_photo']); ?>" alt="Photo de profil" class="w-full h-full object-cover">
                        <?php else: ?>
                            <img id="profile-photo" src="https://ui-avatars.com/api/?name=<?= urlencode($userName) ?>&background=e2e8f0&color=475569&size=200" alt="Photo de profil" class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <label class="photo-upload-btn" title="Changer la photo">
                        <input type="file" accept="image/*" onchange="uploadProfilePhoto(this)" class="hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    </label>
                </div>
                <!-- Name & Role -->
                <h2 class="text-lg font-bold text-slate-900" id="display-name"><?php echo htmlspecialchars($userName); ?></h2>
                <span class="inline-block mt-1 px-3 py-1 bg-blue-50 text-[#0066cc] text-[11px] font-bold uppercase tracking-wider rounded-full">
                    <?php echo htmlspecialchars($userRole); ?>
                </span>
            </div>

            <!-- Security Section -->
            <div class="settings-card bg-white rounded-[28px] border border-slate-100 shadow-sm p-6 mt-6">
                <div class="flex items-center gap-2 mb-5">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0066cc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    <h3 class="text-base font-bold text-slate-900">Sécurité</h3>
                </div>
                <button onclick="openPasswordModal()" class="pwd-btn">
                    <div class="pwd-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                    <div class="text-left flex-1">
                        <div class="text-sm font-semibold text-slate-700">Changer le mot de passe</div>
                        <div class="text-xs text-slate-400 mt-0.5">Dernière modification il y a 30 jours</div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>
        </div>

        <!-- Right Column: Info + Preferences -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Informations Générales -->
            <div class="settings-card bg-white rounded-[28px] border border-slate-100 shadow-sm p-8">
                <h3 class="text-lg font-bold text-slate-900 mb-6">Informations Générales</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nom Complet -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 ml-1">Nom complet</label>
                        <div class="info-field">
                            <div class="field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <input type="text" id="input-name" value="<?php echo htmlspecialchars($userName); ?>" class="w-full bg-transparent text-sm font-medium text-slate-800 outline-none placeholder:text-slate-300" oninput="updateName(this.value)">
                        </div>
                    </div>

                    <!-- Rôle Administratif -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 ml-1">Rôle administratif</label>
                        <div class="info-field" style="background: #f1f5f9;">
                            <div class="field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            </div>
                            <span class="text-sm font-medium text-slate-500"><?php echo htmlspecialchars($userRole === 'ADMIN' ? 'Super Admin' : $userRole); ?></span>
                        </div>
                    </div>

                    <!-- Adresse E-mail -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 ml-1">Adresse e-mail</label>
                        <div class="info-field">
                            <div class="field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </div>
                            <input type="email" id="input-email" value="<?php echo htmlspecialchars($user['email'] ?? 'admin@fleetsync.fr'); ?>" class="w-full bg-transparent text-sm font-medium text-slate-800 outline-none placeholder:text-slate-300">
                        </div>
                    </div>

                    <!-- Numéro de Téléphone -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2 ml-1">Numéro de téléphone</label>
                        <div class="info-field">
                            <div class="field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            <input type="tel" id="input-phone" value="+33 1 23 45 67 89" class="w-full bg-transparent text-sm font-medium text-slate-800 outline-none placeholder:text-slate-300">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences Section -->
            <div class="space-y-4">
                <!-- Archivage automatique -->
                <div class="settings-card bg-white rounded-[28px] border border-slate-100 shadow-sm">
                    <div class="pref-row">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0066cc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8v13H3V8"/><path d="M1 3h22v5H1z"/><path d="M10 12h4"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Archivage automatique</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Garder les rapports générés pendant 12 mois</p>
                            </div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="toggle-archive" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Notifications système -->
                <div class="settings-card bg-white rounded-[28px] border border-slate-100 shadow-sm">
                    <div class="pref-row">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0066cc" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-800">Notifications système</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Recevoir les alertes de maintenance et mises à jour</p>
                            </div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="toggle-notifications" checked>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Password Change Modal -->
<div id="password-modal" class="modal-overlay">
    <div class="modal-box">
        <div class="p-6 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-900">Changer le mot de passe</h3>
            <p class="text-xs text-slate-400 mt-1">Choisissez un mot de passe fort et unique.</p>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Mot de passe actuel</label>
                <input type="password" id="pwd-current" class="modal-input" placeholder="••••••••">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Nouveau mot de passe</label>
                <input type="password" id="pwd-new" class="modal-input" placeholder="••••••••">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Confirmer le nouveau mot de passe</label>
                <input type="password" id="pwd-confirm" class="modal-input" placeholder="••••••••">
            </div>
        </div>
        <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-between items-center">
            <button onclick="closePasswordModal()" class="text-sm text-slate-400 hover:text-slate-700 font-medium cursor-pointer transition-colors">Annuler</button>
            <button onclick="changePassword()" class="px-6 py-2.5 bg-[#0066cc] text-white text-sm font-semibold rounded-xl hover:bg-[#0055b3] transition-all shadow-sm cursor-pointer">
                Mettre à jour
            </button>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="settings-toast" class="toast-settings">
    <div class="bg-white border border-slate-100 rounded-2xl p-4 shadow-xl flex items-center gap-3 max-w-sm">
        <div id="toast-s-icon" class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <p id="toast-s-text" class="text-sm font-medium text-slate-700"></p>
    </div>
</div>

<script>
    // Load settings from localStorage
    document.addEventListener('DOMContentLoaded', () => {
        const savedName = localStorage.getItem('profile_name');
        if (savedName) {
            document.getElementById('input-name').value = savedName;
            document.getElementById('display-name').textContent = savedName;
        }
        
        const savedEmail = localStorage.getItem('profile_email');
        if (savedEmail) document.getElementById('input-email').value = savedEmail;
        
        const savedPhone = localStorage.getItem('profile_phone');
        if (savedPhone) document.getElementById('input-phone').value = savedPhone;
        
        const savedPhoto = localStorage.getItem('profile_photo');
        if (savedPhoto) document.getElementById('profile-photo').src = savedPhoto;
        
        const savedArchive = localStorage.getItem('settings_archive');
        if (savedArchive !== null) document.getElementById('toggle-archive').checked = (savedArchive === 'true');
        
        const savedNotif = localStorage.getItem('settings_notif');
        if (savedNotif !== null) document.getElementById('toggle-notifications').checked = (savedNotif === 'true');
    });

    // Update name dynamically
    function updateName(newName) {
        document.getElementById('display-name').textContent = newName || ' ';
        
        const headerName = document.getElementById('header-user-name');
        if (headerName) headerName.textContent = newName || ' ';
        
        const dropdownName = document.getElementById('dropdown-user-name');
        if (dropdownName) dropdownName.textContent = newName || ' ';
        
        const dropdownInitial = document.getElementById('dropdown-user-initial');
        if (dropdownInitial && newName.length > 0 && !dropdownInitial.querySelector('img')) {
            dropdownInitial.textContent = newName.charAt(0).toUpperCase();
        }
    }

    // Save settings
    function saveSettings() {
        // Save to localStorage
        const name = document.getElementById('input-name').value;
        const photoSrc = document.getElementById('profile-photo').src;
        const email = document.getElementById('input-email').value;
        const phone = document.getElementById('input-phone').value;
        const archive = document.getElementById('toggle-archive').checked;
        const notif = document.getElementById('toggle-notifications').checked;

        localStorage.setItem('profile_name', name);
        localStorage.setItem('profile_email', email);
        localStorage.setItem('profile_phone', phone);
        localStorage.setItem('settings_archive', archive);
        localStorage.setItem('settings_notif', notif);

        const btn = document.getElementById('btn-save-settings');
        btn.innerHTML = `
            <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
            Enregistrement...
        `;
        btn.disabled = true;
        btn.style.opacity = '0.7';

        const formData = new FormData();
        formData.append('action', 'update_profile');
        formData.append('name', name);
        formData.append('email', email);

        fetch('paramètres.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    Enregistré !
                `;
                btn.style.opacity = '1';
                showSettingsToast('Modifications enregistrées avec succès !');

                setTimeout(() => {
                    btn.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                        Enregistrer les modifications
                    `;
                    btn.disabled = false;
                }, 2000);
            } else {
                btn.innerHTML = `Enregistrer les modifications`;
                btn.disabled = false;
                btn.style.opacity = '1';
                showSettingsToast(data.message || 'Erreur lors de la sauvegarde', true);
            }
        })
        .catch(err => {
            btn.innerHTML = `Enregistrer les modifications`;
            btn.disabled = false;
            btn.style.opacity = '1';
            showSettingsToast('Erreur de connexion', true);
        });
    }

    // Password modal
    function openPasswordModal() {
        document.getElementById('password-modal').classList.add('active');
    }
    function closePasswordModal() {
        document.getElementById('password-modal').classList.remove('active');
        document.getElementById('pwd-current').value = '';
        document.getElementById('pwd-new').value = '';
        document.getElementById('pwd-confirm').value = '';
    }
    function changePassword() {
        const currentPwd = document.getElementById('pwd-current').value;
        const newPwd = document.getElementById('pwd-new').value;
        const confirmPwd = document.getElementById('pwd-confirm').value;
        
        if (!currentPwd || !newPwd || !confirmPwd) {
            showSettingsToast('Veuillez remplir tous les champs.', true);
            return;
        }
        if (newPwd !== confirmPwd) {
            showSettingsToast('Les mots de passe ne correspondent pas.', true);
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'update_password');
        formData.append('current_pwd', currentPwd);
        formData.append('new_pwd', newPwd);

        fetch('paramètres.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                closePasswordModal();
                showSettingsToast('Mot de passe mis à jour avec succès !');
            } else {
                showSettingsToast(data.message || 'Erreur lors de la mise à jour.', true);
            }
        })
        .catch(err => {
            showSettingsToast('Erreur de connexion', true);
        });
    }

    // Toast
    function showSettingsToast(msg, isError = false) {
        const toast = document.getElementById('settings-toast');
        const icon = document.getElementById('toast-s-icon');
        document.getElementById('toast-s-text').textContent = msg;

        if (isError) {
            icon.className = 'w-8 h-8 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center shrink-0';
            icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>';
        } else {
            icon.className = 'w-8 h-8 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center shrink-0';
            icon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>';
        }

        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3500);
    }

    // Close modal on overlay click
    document.getElementById('password-modal').addEventListener('click', function(e) {
        if (e.target === this) closePasswordModal();
    });
</script>

<?php 
echo '</main></div></div></body></html>'; 
?>
