<?php

/**
 * Fiche Patient - SGH Kolwezi
 * Chemin : projet_celestine/reception/fiche_patient.php
 */
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. Vérification de l'accès
if (!isset($_SESSION['id_role']) || (int)$_SESSION['id_role'] !== 2) {
    header('Location: ../auth/login.php');
    exit();
}

// 2. Récupération de l'ID (num_fiche)
$num_fiche = isset($_GET['id']) ? $_GET['id'] : null;

if (!$num_fiche) {
    header('Location: liste_attente.php');
    exit();
}

try {
    // 3. Récupération des infos du patient
    $stmt = $pdo->prepare("SELECT m.*, e.nom_empl 
                           FROM malades m 
                           LEFT JOIN employeurs e ON m.code_empl = e.code_empl 
                           WHERE m.num_fiche = ?");
    $stmt->execute([$num_fiche]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patient) {
        die("Patient introuvable.");
    }

    // 4. Récupération de l'historique des consultations (Sans filtre de colonne pour éviter les erreurs)
    $stmtCons = $pdo->prepare("SELECT * FROM consultations WHERE num_fiche = ? ORDER BY num_fiche DESC");
    $stmtCons->execute([$num_fiche]);
    $historique = $stmtCons->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log($e->getMessage());
    $historique = [];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Patient #<?= $num_fiche ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] min-h-screen flex">

    <?php
    $sidebarPath = __DIR__ . '/../includes/sidebar.php';
    if (file_exists($sidebarPath)) include $sidebarPath;
    ?>

    <main class="flex-1 p-6 md:p-12">
        <!-- Barre de navigation haute -->
        <div class="flex justify-between items-center mb-10">
            <a href="liste_attente.php" class="flex items-center gap-2 text-slate-500 hover:text-slate-900 font-bold transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Retour à la liste
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Colonne Gauche : Infos Patient -->
            <div class="lg:col-span-1">
                <div class="bg-white p-8 rounded-[32px] border border-slate-200 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-6">
                        <span class="text-4xl font-black text-slate-50 opacity-10 uppercase">SGH-K</span>
                    </div>

                    <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center text-3xl font-bold mb-6">
                        <?= strtoupper(substr($patient['nom_mal'], 0, 1)) ?>
                    </div>

                    <h2 class="text-2xl font-extrabold text-slate-900 uppercase leading-tight">
                        <?= htmlspecialchars($patient['nom_mal'] . ' ' . $patient['postnom_mal']) ?>
                    </h2>
                    <p class="text-slate-400 font-medium mb-8 uppercase tracking-widest text-[10px]">Fiche n° <?= $num_fiche ?></p>

                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase">Sexe</p>
                            <p class="font-bold text-slate-700"><?= $patient['sexe_mal'] == 'M' ? 'Masculin' : 'Féminin' ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-slate-400 uppercase">Employeur / Entreprise</p>
                            <p class="font-bold text-slate-700"><?= htmlspecialchars($patient['nom_empl'] ?? 'Individuel') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne Droite : Historique & Actions -->
            <div class="lg:col-span-2 space-y-8">

                <!-- Section Historique -->
                <div class="bg-white p-8 rounded-[32px] border border-slate-200 shadow-sm">
                    <h3 class="text-xl font-extrabold text-slate-900 mb-6 italic">Historique des Consultations</h3>

                    <?php if (empty($historique)): ?>
                        <div class="py-10 text-center border-2 border-dashed border-slate-100 rounded-3xl">
                            <p class="text-slate-400 text-sm font-medium italic">Aucun antécédent enregistré pour ce patient.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($historique as $c): ?>
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex justify-between items-center">
                                    <div>
                                        <p class="text-xs font-bold text-slate-900 uppercase">Consultation Générale</p>
                                        <p class="text-[10px] text-slate-400 font-medium">Numéro de session : <?= $c['num_fiche'] ?></p>
                                    </div>
                                    <span class="px-3 py-1 bg-white rounded-lg text-[10px] font-black text-slate-500 border border-slate-200">
                                        ARCHIVE
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Section Action Rapide -->
                <div class="bg-slate-900 p-8 rounded-[32px] text-white flex justify-between items-center">
                    <div>
                        <h4 class="text-lg font-bold">Nouvelle Consultation</h4>
                        <p class="text-slate-400 text-xs">Envoyer ce patient vers le cabinet médical</p>
                    </div>
                    <a href="envoyer_medecin.php?id=<?= $num_fiche ?>" class="bg-emerald-500 hover:bg-emerald-400 text-white px-6 py-3 rounded-2xl font-bold text-xs transition-all shadow-lg shadow-emerald-900/20">
                        Lancer l'appel
                    </a>
                </div>

            </div>
        </div>
    </main>
</body>

</html>