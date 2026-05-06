<?php

/**
 * Page de visualisation de l'historique complet - hospital_kolwezi_db
 */
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. Sécurité : Seul le médecin (rôle 1) accède à l'historique
if (!isset($_SESSION['id_role']) || (int)$_SESSION['id_role'] !== 1) {
    header('Location: ../auth/login.php');
    exit();
}

try {
    // 2. Requête SQL adaptée à ta structure réelle (hospital_kolwezi_db)
    // Jointure avec 'employeurs' pour récupérer le nom de l'entreprise (GCM, etc.)
    $query = "SELECT c.*, 
                     m.nom_mal, m.postnom_mal, m.sexe_mal, 
                     m.date_naiss, m.adresse_mal, m.num_matr_ag,
                     e.nom_empl
              FROM consultations c
              JOIN malades m ON c.num_fiche = m.num_fiche
              LEFT JOIN employeurs e ON m.code_empl = e.code_empl
              ORDER BY c.date_diag DESC";

    $stmt = $pdo->query($query);
    $consultations = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur de récupération : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Consultations</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] min-h-screen flex flex-row">

    <!-- Sidebar Dynamique -->
    <?php
    $sidebarPath = __DIR__ . '/../includes/sidebar.php';
    if (file_exists($sidebarPath)) include $sidebarPath;
    ?>

    <div class="flex-1 flex flex-col min-h-screen pb-24 md:pb-0">

        <div class="p-6 md:p-10">
            <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Historique des Consultations</h1>
                    <p class="text-slate-500 text-sm">Gestion des archives de l'hôpital de Kolwezi</p>
                </div>
            </header>

            <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm overflow-hidden mb-10">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50/50 text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-5">Date & Patient</th>
                                <th class="px-6 py-5">Identité & Contact</th>
                                <th class="px-6 py-5">Diagnostic</th>
                                <th class="px-6 py-5">Traitement Prescrit</th>
                                <th class="px-6 py-5 text-right">Frais</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($consultations)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-20 text-center text-slate-400 italic font-medium">
                                        Aucun historique de consultation trouvé.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($consultations as $c): ?>
                                    <tr class="hover:bg-slate-50/30 transition-all">
                                        <!-- DATE ET NOM -->
                                        <td class="px-6 py-6">
                                            <div class="text-[10px] font-bold text-blue-500 mb-1 uppercase tracking-tighter">
                                                <?= date('d/m/Y à H:i', strtotime($c['date_diag'])) ?>
                                            </div>
                                            <div class="font-bold text-slate-900 uppercase text-sm leading-tight">
                                                <?= htmlspecialchars($c['nom_mal'] . ' ' . $c['postnom_mal']) ?>
                                            </div>
                                            <div class="text-[10px] text-slate-400 mt-1">Fiche : <span class="text-slate-600 font-bold">#<?= $c['num_fiche'] ?></span></div>
                                        </td>

                                        <!-- INFOS PATIENT (Basé sur tes colonnes SQL) -->
                                        <td class="px-6 py-6">
                                            <div class="text-xs text-slate-600 font-bold mb-1">
                                                Sexe : <?= $c['sexe_mal'] ?> | Naiss. : <?= $c['date_naiss'] ?? 'Non spécifiée' ?>
                                            </div>
                                            <div class="text-[11px] text-slate-500 mb-1">
                                                📍 <?= htmlspecialchars($c['adresse_mal'] ?? 'Kolwezi') ?>
                                            </div>
                                            <div class="inline-block bg-slate-100 text-slate-600 text-[9px] px-2 py-0.5 rounded font-black uppercase tracking-widest">
                                                🏢 <?= htmlspecialchars($c['nom_empl'] ?? 'Privé') ?>
                                                (<?= htmlspecialchars($c['code_empl'] ?? 'N/A') ?>)
                                            </div>
                                        </td>

                                        <!-- CLINIQUE -->
                                        <td class="px-6 py-6">
                                            <div class="text-[11px] text-slate-400 italic mb-1 line-clamp-1" title="<?= htmlspecialchars($c['plainte']) ?>">
                                                "<?= htmlspecialchars($c['plainte']) ?>"
                                            </div>
                                            <span class="bg-orange-50 text-orange-700 px-3 py-1 rounded-lg font-bold text-[10px] uppercase border border-orange-100">
                                                <?= htmlspecialchars($c['diagnostic']) ?>
                                            </span>
                                        </td>

                                        <!-- TRAITEMENT -->
                                        <td class="px-6 py-6">
                                            <div class="text-xs text-slate-700 leading-relaxed max-w-xs">
                                                <?= nl2br(htmlspecialchars($c['traitement'])) ?>
                                            </div>
                                        </td>

                                        <!-- MONTANT -->
                                        <td class="px-6 py-6 text-right font-black text-slate-900 text-sm">
                                            <?= number_format($c['montant'], 0, ',', ' ') ?> <span class="text-[9px] font-bold text-slate-400 ml-0.5">FC</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>

</html>