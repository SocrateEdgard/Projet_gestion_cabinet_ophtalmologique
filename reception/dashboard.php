<?php

/**
 * Dashboard Réception - SGH Kolwezi
 * Chemin : projet_celestine/reception/dashboard.php
 */
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. Protection de la page (Rôle 2 = Réception)
if (!isset($_SESSION['id_role']) || (int)$_SESSION['id_role'] !== 2) {
    header('Location: ../auth/login.php');
    exit();
}

$nom_agent = $_SESSION['nom_complet'] ?? 'Agent';

try {
    // 2. Statistiques en temps réel
    $total_malades = $pdo->query("SELECT COUNT(*) FROM malades")->fetchColumn();
    $total_employeurs = $pdo->query("SELECT COUNT(*) FROM employeurs")->fetchColumn();

    // 3. Les 5 dernières inscriptions avec jointure
    $query_recent = "SELECT m.num_fiche, m.nom_mal, m.postnom_mal, m.sexe_mal, e.nom_empl 
                     FROM malades m 
                     LEFT JOIN employeurs e ON m.code_empl = e.code_empl 
                     ORDER BY m.num_fiche DESC LIMIT 5";
    $recent_malades = $pdo->query($query_recent)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div style='color:red; padding:20px; font-family:sans-serif;'>
            <strong>Erreur critique de base de données :</strong> " . htmlspecialchars($e->getMessage()) . "
         </div>");
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réception | Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="bg-[#F8FAFC] min-h-screen flex flex-row">

    <!-- 1. SIDEBAR DYNAMIQUE -->
    <?php
    $sidebarPath = __DIR__ . '/../includes/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    } else {
    ?>
        <aside class="w-72 bg-white border-r border-slate-200 hidden lg:flex flex-col sticky top-0 h-screen">
            <div class="p-8 font-extrabold text-xl text-emerald-600">SGH-K</div>
            <nav class="flex-1 px-6 space-y-2">
                <a href="dashboard.php" class="bg-emerald-50 text-emerald-700 px-5 py-4 rounded-2xl font-bold block">Dashboard</a>
            </nav>
        </aside>
    <?php } ?>

    <!-- 2. CONTENU PRINCIPAL -->
    <main class="flex-1 p-6 md:p-12 overflow-y-auto">

        <!-- Header & Recherche Rapide -->
        <header class="flex flex-col xl:flex-row justify-between items-start xl:items-center gap-6 mb-12">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight italic">Bienvenue...</h1>
            </div>

            <form action="liste_malades.php" method="GET" class="relative group w-full xl:w-96">
                <input type="text" name="search" placeholder="Recherche rapide (Nom ou N°)..."
                    class="w-full bg-white border border-slate-200 pl-12 pr-4 py-4 rounded-2xl outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition-all shadow-sm">
                <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </form>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
            <div class="stat-card bg-white p-8 rounded-[32px] border border-slate-200 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-emerald-50 rounded-bl-[60px] -mr-8 -mt-8 transition-all group-hover:scale-110"></div>
                <p class="text-emerald-600 text-[10px] font-black uppercase tracking-[0.2em] mb-2 relative">Total Malades</p>
                <h3 class="text-5xl font-black text-slate-900 leading-none relative"><?= $total_malades ?></h3>
            </div>

            <div class="stat-card bg-white p-8 rounded-[32px] border border-slate-200 shadow-sm relative overflow-hidden group">
                <div class="absolute right-0 top-0 w-24 h-24 bg-blue-50 rounded-bl-[60px] -mr-8 -mt-8 transition-all group-hover:scale-110"></div>
                <p class="text-blue-600 text-[10px] font-black uppercase tracking-[0.2em] mb-2 relative">Partenaires</p>
                <h3 class="text-5xl font-black text-slate-900 leading-none relative"><?= $total_employeurs ?></h3>
            </div>
        </div>

        <!-- Table des inscriptions récentes -->
        <div class="bg-white rounded-[40px] border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-8 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/30">
                <div>
                    <h3 class="text-xl font-bold text-slate-900">Derniers Patients</h3>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Aperçu du registre récent</p>
                </div>

                <div class="flex gap-3">
                    <!-- BOUTON : MALADES EN ATTENTE -->
                    <a href="liste_attente.php" class="bg-amber-500 text-white px-6 py-4 rounded-2xl font-bold text-sm shadow-lg shadow-amber-100 hover:bg-amber-600 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        En attente
                    </a>

                    <a href="ajouter_malade.php" class="bg-emerald-600 text-white px-6 py-4 rounded-2xl font-bold text-sm shadow-lg shadow-emerald-100 hover:bg-emerald-700 hover:-translate-y-0.5 transition-all">
                        + Nouvelle Fiche
                    </a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                            <th class="px-8 py-6">ID Fiche</th>
                            <th class="px-8 py-6">Nom Complet</th>
                            <th class="px-8 py-6">Source/Employeur</th>
                            <th class="px-8 py-6 text-center">Sexe</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (empty($recent_malades)): ?>
                            <tr>
                                <td colspan="4" class="p-16 text-center text-slate-400 italic font-medium">
                                    Aucun enregistrement récent.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_malades as $m): ?>
                                <tr class="hover:bg-slate-50/50 transition-all group">
                                    <td class="px-8 py-6">
                                        <span class="bg-emerald-50 text-emerald-700 px-4 py-2 rounded-xl font-black text-xs">
                                            #<?= str_pad($m['num_fiche'], 4, '0', STR_PAD_LEFT) ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="font-bold text-slate-800 uppercase text-sm group-hover:text-emerald-600 transition-colors">
                                            <?= htmlspecialchars($m['nom_mal'] . ' ' . $m['postnom_mal']) ?>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <div class="w-1.5 h-1.5 rounded-full <?= $m['nom_empl'] ? 'bg-emerald-400' : 'bg-slate-300' ?>"></div>
                                            <span class="text-slate-500 italic text-xs font-medium">
                                                <?= htmlspecialchars($m['nom_empl'] ?? 'Individuel') ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="font-black text-[10px] px-3 py-1 rounded-lg <?= $m['sexe_mal'] == 'M' ? 'text-blue-500 bg-blue-50' : 'text-pink-500 bg-pink-50' ?>">
                                            <?= $m['sexe_mal'] == 'M' ? 'MASCULIN' : 'FÉMININ' ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-slate-50/50 border-t border-slate-100 text-center">
                <a href="liste_malades.php" class="text-emerald-600 font-black text-[11px] uppercase tracking-widest hover:underline">Voir le registre complet →</a>
            </div>
        </div>

        <footer class="mt-12 text-center text-slate-400 text-[10px] font-bold uppercase tracking-[0.4em]">
            Service de Réception — Kolwezi Lualaba
        </footer>
    </main>

</body>

</html>