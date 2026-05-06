<?php

/**
 * Registre de toutes les consultations effectuées
 * Projet : SGH-K (Kolwezi)
 * Emplacement : medecin/consultations.php
 */
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. Sécurité : Seul le médecin (Rôle 1) peut voir l'historique complet
if (!isset($_SESSION['id_role']) || (int)$_SESSION['id_role'] !== 1) {
    header('Location: ../auth/login.php');
    exit();
}

try {
    // 2. Requête SQL avec Jointure
    // On lie 'consultations' et 'malades' via 'num_fiche'
    $query = "SELECT c.*, m.nom_mal, m.postnom_mal 
              FROM consultations c
              INNER JOIN malades m ON c.num_fiche = m.num_fiche
              ORDER BY c.date_diag DESC";

    $stmt = $pdo->query($query);
    $consultations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En cas d'erreur, on affiche un message propre
    error_log($e->getMessage());
    $error = "Impossible de charger l'historique des consultations.";
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre des Consultations</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] min-h-screen flex flex-row">

    <?php
    $sidebarPath = __DIR__ . '/../includes/sidebar.php';
    if (file_exists($sidebarPath)) include $sidebarPath;
    ?>

    <div class="flex-1 flex flex-col min-h-screen">
        <div class="p-6 md:p-10">

            <header class="flex justify-between items-center mb-10">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Registre des Consultations</h1>
                    <p class="text-slate-500 italic uppercase text-[10px] tracking-widest font-bold mt-1">Historique des diagnostics — Kolwezi</p>
                </div>
                <a href="patients.php" class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 flex items-center gap-2 text-sm uppercase tracking-tighter">
                    <span>+</span> Nouvelle Consultation
                </a>
            </header>

            <?php if (isset($error)): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-2xl mb-6 font-bold text-sm">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm overflow-hidden mb-10">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-5">Date & Heure</th>
                                <th class="px-6 py-5">Patient</th>
                                <th class="px-6 py-5">Plainte</th>
                                <th class="px-6 py-5">Diagnostic</th>
                                <th class="px-6 py-5">Traitement</th>
                                <th class="px-6 py-5 text-right">Frais</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($consultations)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-24 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-200">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                            </div>
                                            <p class="text-slate-400 italic text-sm">Aucun historique de consultation trouvé.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($consultations as $c): ?>
                                    <tr class="hover:bg-slate-50/50 transition-all">
                                        <td class="px-6 py-5 text-[11px] font-bold text-slate-400">
                                            <?= date('d/m/Y', strtotime($c['date_diag'])) ?><br>
                                            <span class="text-slate-300 font-medium"><?= date('H:i', strtotime($c['date_diag'])) ?></span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="font-bold text-slate-800 uppercase text-xs tracking-tight">
                                                <?= htmlspecialchars($c['nom_mal'] . ' ' . $c['postnom_mal']) ?>
                                            </div>
                                            <div class="text-[9px] text-blue-500 font-black">FICHE #<?= str_pad($c['num_fiche'], 4, '0', STR_PAD_LEFT) ?></div>
                                        </td>
                                        <td class="px-6 py-5 text-xs text-slate-500 max-w-[200px]">
                                            <p class="truncate" title="<?= htmlspecialchars($c['plainte']) ?>">
                                                <?= htmlspecialchars($c['plainte']) ?>
                                            </p>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span class="bg-orange-50 text-orange-700 px-3 py-1.5 rounded-lg font-black text-[9px] uppercase border border-orange-100 shadow-sm shadow-orange-50">
                                                <?= htmlspecialchars($c['Diagnostic']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-[11px] text-slate-600 leading-relaxed italic">
                                            <?= htmlspecialchars($c['traitement']) ?>
                                        </td>
                                        <td class="px-6 py-5 text-right font-black text-slate-900 text-sm">
                                            <?= number_format($c['montant'], 0, ',', ' ') ?>
                                            <span class="text-[9px] text-slate-400 ml-1">FC</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <footer class="text-center text-slate-300 text-[10px] font-bold uppercase tracking-[0.4em] mt-10">
                Service Ophtalmologique — Lualaba
            </footer>
        </div>
    </div>
</body>

</html>