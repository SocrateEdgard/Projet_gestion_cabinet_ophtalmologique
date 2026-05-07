<?php

/**
 * Liste d'attente des patients - SGH Kolwezi
 * Affiche uniquement les patients n'ayant pas encore de consultation enregistrée
 */
session_start();
require_once __DIR__ . '/../config/db.php';

// Protection : Accès réservé à la réception (Rôle 2)
if (!isset($_SESSION['id_role']) || (int)$_SESSION['id_role'] !== 2) {
    header('Location: ../auth/login.php');
    exit();
}

try {
    /**
     * Requête : Sélectionner les malades qui ne sont PAS ENCORE dans la table consultations
     */
    $query = "SELECT 
                m.num_fiche, 
                m.nom_mal, 
                m.postnom_mal, 
                m.sexe_mal,
                e.nom_empl
              FROM malades m
              LEFT JOIN employeurs e ON m.code_empl = e.code_empl
              WHERE m.num_fiche NOT IN (SELECT num_fiche FROM consultations)
              ORDER BY m.num_fiche ASC";

    $stmt = $pdo->query($query);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("<div style='color:red; padding:20px; font-family:sans-serif;'>
            <strong>Erreur de base de données :</strong> " . htmlspecialchars($e->getMessage()) . "
         </div>");
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File d'Attente | Réception</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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

    <main class="flex-1 p-6 md:p-12 overflow-y-auto">

        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">File d'Attente</h1>
                <p class="text-slate-500 text-sm font-medium">Patients enregistrés en attente de consultation médicale.</p>
            </div>
            <div class="bg-emerald-100 text-emerald-700 px-5 py-2 rounded-2xl font-bold text-xs border border-emerald-200">
                <?= count($patients) ?> Patient(s) en attente
            </div>
        </header>

        <div class="grid grid-cols-1 gap-4">
            <?php if (empty($patients)): ?>
                <div class="bg-white border-2 border-dashed border-slate-200 rounded-[32px] p-20 text-center">
                    <div class="flex flex-col items-center">
                        <svg class="w-16 h-16 text-slate-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        <p class="text-slate-400 font-medium">Tous les patients ont été consultés ou la file est vide.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($patients as $index => $p): ?>
                    <div class="bg-white p-6 rounded-[28px] border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center group hover:border-blue-400 transition-all">
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center text-xl font-black text-slate-300 group-hover:bg-blue-50 group-hover:text-blue-500 transition-colors">
                                <?= $index + 1 ?>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 uppercase">
                                    <?= htmlspecialchars($p['nom_mal'] . ' ' . $p['postnom_mal']) ?>
                                </h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] bg-slate-100 text-slate-600 px-2 py-0.5 rounded-md font-bold uppercase">
                                        Fiche #<?= str_pad($p['num_fiche'], 4, '0', STR_PAD_LEFT) ?>
                                    </span>
                                    <span class="text-slate-300 text-xs">•</span>
                                    <p class="text-xs text-slate-400 font-medium italic">
                                        Prise en charge : <?= htmlspecialchars($p['nom_empl'] ?? 'Individuel') ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-4 md:mt-0">
                            <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full mr-4">En attente</span>
                            <a href="fiche_patient.php?id=<?= $p['num_fiche'] ?>"
                                class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold text-xs hover:bg-blue-600 transition-all shadow-lg shadow-slate-100">
                                Détails du dossier
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <footer class="mt-12 text-center text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em]">
            Service de Réception — Kolwezi Lualaba
        </footer>
    </main>
</body>

</html>