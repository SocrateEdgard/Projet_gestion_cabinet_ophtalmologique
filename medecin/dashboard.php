<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// Protection : Seul le médecin (ID rôle 1) peut accéder
if (!isset($_SESSION['id_role']) || (int)$_SESSION['id_role'] !== 1) {
    header('Location: ../auth/login.php');
    exit();
}

$id_medecin = $_SESSION['user_id'];
$nom_medecin = $_SESSION['nom_complet'];

// 1. Calcul des statistiques réelles
$today = date('Y-m-d');
$stmt_today = $pdo->prepare("SELECT COUNT(*) FROM consultations WHERE medecin_id = ? AND DATE(date_diag) = ?");
$stmt_today->execute([$id_medecin, $today]);
$count_today = $stmt_today->fetchColumn();

$stmt_total = $pdo->prepare("SELECT COUNT(DISTINCT num_fiche) FROM consultations WHERE medecin_id = ?");
$stmt_total->execute([$id_medecin]);
$total_patients = $stmt_total->fetchColumn();

// 2. Récupération des dernières consultations avec jointures
$query = "SELECT c.*, m.nom_mal, m.postnom_mal, e.nom_empl 
          FROM consultations c
          JOIN malades m ON c.num_fiche = m.num_fiche
          LEFT JOIN employeurs e ON m.code_empl = e.code_empl
          WHERE c.medecin_id = ? 
          ORDER BY c.date_diag DESC LIMIT 5";
$stmt_list = $pdo->prepare($query);
$stmt_list->execute([$id_medecin]);
$consultations = $stmt_list->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Médecin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] min-h-screen flex text-slate-800 pb-24 md:pb-0">

    <!-- Appel de la Sidebar dynamique -->
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col">
        <header class="h-20 bg-white border-b border-slate-100 flex items-center justify-between px-6 md:px-10 sticky top-0 z-10">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Bienvenue</span>
                <h1 class="text-xl font-bold text-slate-900">Dr. <?= htmlspecialchars($nom_medecin) ?></h1>
            </div>
            <div class="bg-blue-600 text-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-tighter">Kolwezi (Lualaba)</div>
        </header>

        <div class="p-6 md:p-10">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
                <div class="bg-white p-8 rounded-[32px] shadow-sm border border-slate-100 group hover:border-blue-200 transition-all">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Aujourd'hui</p>
                    <h3 class="text-4xl font-black mt-2 text-slate-900"><?= $count_today ?> <span class="text-lg font-medium text-slate-400">fiches</span></h3>
                </div>
                <div class="bg-white p-8 rounded-[32px] shadow-sm border border-slate-100 group hover:border-blue-200 transition-all">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Patients Uniques</p>
                    <h3 class="text-4xl font-black mt-2 text-slate-900"><?= $total_patients ?> <span class="text-lg font-medium text-slate-400">suivis</span></h3>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                    <h3 class="font-bold text-lg">Dernières Consultations</h3>
                    <a href="liste_consultations.php" class="text-blue-600 text-sm font-bold hover:underline">Voir tout</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                                <th class="px-8 py-5">Patient</th>
                                <th class="px-8 py-5">Date & Heure</th>
                                <th class="px-8 py-5">Employeur</th>
                                <th class="px-8 py-5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php foreach ($consultations as $row): ?>
                                <tr class="hover:bg-slate-50/30 transition-all">
                                    <td class="px-8 py-6">
                                        <div class="font-bold text-slate-700 uppercase"><?= htmlspecialchars($row['nom_mal'] . ' ' . $row['postnom_mal']) ?></div>
                                        <div class="text-[10px] text-blue-500 font-bold">FICHE #<?= $row['num_fiche'] ?></div>
                                    </td>
                                    <td class="px-8 py-6 text-sm text-slate-500">
                                        <?= date('d/m/Y à H:i', strtotime($row['date_diag'])) ?>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] font-bold rounded-lg uppercase">
                                            <?= htmlspecialchars($row['nom_empl'] ?? 'Privé/Tiers') ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <a href="details_consultation.php?id=<?= $row['id_consult'] ?>" class="bg-slate-900 text-white text-[11px] font-bold px-5 py-2.5 rounded-xl hover:bg-blue-600 transition-all inline-block">
                                            Détails
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($consultations)): ?>
                                <tr>
                                    <td colspan="4" class="p-16 text-center text-slate-400 italic">Aucune consultation enregistrée pour le moment.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

</body>

</html>