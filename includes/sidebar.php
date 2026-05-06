<?php

/**
 * Sidebar Latérale Dynamique - SGH-K (Kolwezi)
 * Gère l'affichage pour les rôles Médecin et Réception
 */
$role_id = isset($_SESSION['id_role']) ? (int)$_SESSION['id_role'] : 0;
$current_page = basename($_SERVER['PHP_SELF']);

// Définition du chemin du dashboard selon le rôle pour le lien principal
$dashboard_link = ($role_id === 1) ? "../medecin/dashboard.php" : "../reception/dashboard.php";
?>

<aside class="w-64 bg-white border-r border-slate-200 hidden md:flex flex-col sticky top-0 h-screen">
    <!-- Logo & Identité Visuelle -->
    <div class="p-8">
        <div class="flex items-center gap-3">
            <span class="text-blue-600 font-black text-2xl tracking-tighter">Système de gestion ophtalmologique</span>
        </div>
    </div>

    <!-- Navigation Principale -->
    <nav class="flex-1 px-4 space-y-1 overflow-y-auto">

        <!-- SECTION COMMUNE : DASHBOARD -->
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mb-2">Général</p>
        <a href="<?= $dashboard_link ?>" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition-all <?= $current_page == 'dashboard.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50' ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            Tableau de bord
        </a>

        <!-- ========================= MENU MÉDECIN (Rôle 1) ========================= -->
        <?php if ($role_id === 1): ?>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mt-6 mb-2">Cabinet Médical</p>

            <a href="../medecin/patients.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition-all <?= $current_page == 'patients.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Mes Patients
            </a>

            <a href="../medecin/consultations.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition-all <?= in_array($current_page, ['consultations.php', 'consulter.php', 'liste_consultation.php', 'details_consultations.php']) ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                Consultations
            </a>
        <?php endif; ?>

        <!-- ========================= MENU RÉCEPTION (Rôle 2) ========================= -->
        <?php if ($role_id === 2): ?>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mt-6 mb-2">Patients & Flux</p>

            <a href="../reception/ajouter_malade.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition-all <?= $current_page == 'ajouter_malade.php' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Inscrire un malade
            </a>

            <a href="../reception/liste_malades.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition-all <?= $current_page == 'liste_malades.php' || $current_page == 'fiche_patient.php' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Registre Patients
            </a>

            <a href="../reception/liste_attente.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition-all <?= $current_page == 'liste_attente.php' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                File d'Attente
            </a>

            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest px-4 mt-6 mb-2">Partenaires</p>
            <a href="../reception/employeurs.php" class="flex items-center gap-3 px-4 py-3 rounded-2xl font-semibold transition-all <?= $current_page == 'employeurs.php' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:bg-slate-50' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Employeurs & Agents
            </a>
        <?php endif; ?>

    </nav>

    <!-- Pied de Sidebar : Déconnexion -->
    <div class="p-4 border-t border-slate-100">
        <a href="../auth/logout.php" class="flex items-center gap-3 px-4 py-4 rounded-2xl font-bold text-red-500 hover:bg-red-50 transition-all group">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center group-hover:bg-red-100 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </div>
            <span>Déconnexion</span>
        </a>
    </div>
</aside>