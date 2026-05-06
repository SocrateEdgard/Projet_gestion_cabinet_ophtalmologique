<?php

/**
 * Liste d'attente des patients - SGH Kolwezi
 * Chemin : projet_celestine/reception/liste_attente.php
 */
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['id_role']) || (int)$_SESSION['id_role'] !== 2) {
    header('Location: ../auth/login.php');
    exit();
}

try {
    /**
     * Requête de diagnostic : 
     * On retire le WHERE sur le statut pour éviter l'erreur 1054.
     * On récupère les malades liés aux consultations.
     */
    $query = "SELECT 
                m.num_fiche, 
                m.nom_mal, 
                m.postnom_mal, 
                m.sexe_mal,
                e.nom_empl
              FROM consultations c
              JOIN malades m ON c.num_fiche = m.num_fiche
              LEFT JOIN employeurs e ON m.code_empl = e.code_empl
              LIMIT 10";

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
    <title>File d'Attente</title>
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
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight italic">File d'Attente</h1>

            </div>
            <div class="bg-blue-100 text-blue-700 px-5 py-2 rounded-2xl font-bold text-xs border border-blue-200">
                Mode Diagnostic : <?= count($patients) ?> Patient(s) affiché(s)
            </div>
        </header>

        <div class="grid grid-cols-1 gap-4">
            <?php if (empty($patients)): ?>
                <div class="bg-white border-2 border-dashed border-slate-200 rounded-[32px] p-20 text-center text-slate-400">
                    Aucune donnée trouvée dans la table consultations.
                </div>
            <?php else: ?>
                <?php foreach ($patients as $index => $p): ?>
                    <div class="bg-white p-6 rounded-[28px] border border-slate-200 shadow-sm flex flex-col md:flex-row justify-between items-center group hover:border-emerald-400 transition-all">
                        <div class="flex items-center gap-6">
                            <div class="text-2xl font-black text-slate-200"><?= $index + 1 ?></div>
                            <div>
                                <h3 class="font-bold text-slate-900 uppercase">
                                    <?= htmlspecialchars($p['nom_mal'] . ' ' . $p['postnom_mal']) ?>
                                </h3>
                                <p class="text-xs text-slate-400 font-medium">
                                    Fiche : #<?= str_pad($p['num_fiche'], 4, '0', STR_PAD_LEFT) ?> |
                                    <?= htmlspecialchars($p['nom_empl'] ?? 'Individuel') ?>
                                </p>
                            </div>
                        </div>
                        <a href="fiche_patient.php?id=<?= $p['num_fiche'] ?>" class="bg-slate-900 text-white px-8 py-3.5 rounded-2xl font-bold text-xs hover:bg-emerald-600 transition-all shadow-lg shadow-slate-100">
                            Gérer la fiche
                        </a>
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