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

try {
    // 1. Calcul des statistiques réelles
    $today = date('Y-m-d');
    $stmt_today = $pdo->prepare("SELECT COUNT(*) FROM consultations WHERE medecin_id = ? AND DATE(date_diag) = ?");
    $stmt_today->execute([$id_medecin, $today]);
    $count_today = $stmt_today->fetchColumn();

    $stmt_total = $pdo->prepare("SELECT COUNT(DISTINCT num_fiche) FROM consultations WHERE medecin_id = ?");
    $stmt_total->execute([$id_medecin]);
    $total_patients = $stmt_total->fetchColumn();

    // 2. Récupération des dernières consultations avec jointures
    // CORRECTION : On utilise id_cons (nom exact dans votre table SQL)
    $query = "SELECT c.id_cons, c.num_fiche, c.date_diag, m.nom_mal, m.postnom_mal, e.nom_empl 
              FROM consultations c
              JOIN malades m ON c.num_fiche = m.num_fiche
              LEFT JOIN employeurs e ON m.code_empl = e.code_empl
              WHERE c.medecin_id = ? 
              ORDER BY c.date_diag DESC LIMIT 5";
    $stmt_list = $pdo->prepare($query);
    $stmt_list->execute([$id_medecin]);
    $consultations = $stmt_list->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Médecin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] min-h-screen flex text-slate-800">

    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="flex-1 flex flex-col">
        <header class="h-24 bg-white border-b border-slate-100 flex items-center justify-between px-6 md:px-10 sticky top-0 z-10">
            <div>
                <span class="text-[10px] font-black text-blue-500 uppercase tracking-[0.2em]">Espace Médical</span>
                <h1 class="text-2xl font-extrabold text-slate-900">Dr. <?= htmlspecialchars($nom_medecin) ?> ophtalmologue</h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="hidden md:block text-right">
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Localisation</p>
                    <p class="text-xs font-bold text-slate-700">Kolwezi, Lualaba</p>
                </div>
                <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 font-bold">
                    <?= strtoupper(substr($nom_medecin, 0, 1)) ?>
                </div>
            </div>
        </header>

        <div class="p-6 md:p-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                <div class="bg-white p-8 rounded-[35px] shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-xl hover:shadow-blue-500/5 transition-all">
                    <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform">
                        <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Consultations Jour</p>
                    <h3 class="text-5xl font-black mt-2 text-slate-900"><?= $count_today ?></h3>
                    <p class="text-xs font-bold text-blue-500 mt-2">Aujourd'hui, <?= date('d M') ?></p>
                </div>

                <div class="bg-white p-8 rounded-[35px] shadow-sm border border-slate-100 relative overflow-hidden group hover:shadow-xl hover:shadow-emerald-500/5 transition-all">
                    <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform">
                        <svg class="w-12 h-12 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Total Patients</p>
                    <h3 class="text-5xl font-black mt-2 text-slate-900"><?= $total_patients ?></h3>
                    <p class="text-xs font-bold text-emerald-500 mt-2">Dossiers suivis au total</p>
                </div>
            </div>

            <div class="bg-white rounded-[40px] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                    <div>
                        <h3 class="font-extrabold text-xl text-slate-900">Activité Récente</h3>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Vos 5 dernières interventions</p>
                    </div>
                    <a href="liste_consultations.php" class="bg-slate-100 text-slate-600 px-6 py-3 rounded-2xl text-xs font-black hover:bg-blue-600 hover:text-white transition-all uppercase">Historique Complet</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em]">
                                <th class="px-10 py-6">Patient & Fiche</th>
                                <th class="px-10 py-6">Date du Diagnostic</th>
                                <th class="px-10 py-6">Prise en charge</th>
                                <th class="px-10 py-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($consultations as $row): ?>
                                <tr class="hover:bg-blue-50/30 transition-all group">
                                    <td class="px-10 py-6">
                                        <div class="font-black text-slate-800 uppercase text-sm group-hover:text-blue-600 transition-colors">
                                            <?= htmlspecialchars($row['nom_mal'] . ' ' . $row['postnom_mal']) ?>
                                        </div>
                                        <div class="text-[10px] font-bold text-slate-400 mt-1">#<?= str_pad($row['num_fiche'], 4, '0', STR_PAD_LEFT) ?></div>
                                    </td>
                                    <td class="px-10 py-6">
                                        <div class="text-xs font-bold text-slate-700"><?= date('d M Y', strtotime($row['date_diag'])) ?></div>
                                        <div class="text-[10px] text-slate-400"><?= date('H:i', strtotime($row['date_diag'])) ?></div>
                                    </td>
                                    <td class="px-10 py-6">
                                        <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[9px] font-black rounded-lg uppercase tracking-widest">
                                            <?= htmlspecialchars($row['nom_empl'] ?? 'Tiers / Privé') ?>
                                        </span>
                                    </td>
                                    <td class="px-10 py-6 text-right">
                                        <a href="details_consultation.php?id=<?= $row['id_cons'] ?>"
                                            class="bg-slate-900 text-white text-[10px] font-black px-6 py-3 rounded-xl hover:bg-blue-600 hover:-translate-y-0.5 transition-all inline-block shadow-lg shadow-slate-100">
                                            CONSULTER
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (empty($consultations)): ?>
                                <tr>
                                    <td colspan="4" class="p-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 text-slate-200">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <p class="text-slate-400 font-bold text-sm italic uppercase tracking-widest">Aucune activité enregistrée</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <footer class="p-10 text-center">
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.5em]">SGH-K • Système de Gestion Hospitalière • Kolwezi 2026</p>
        </footer>
    </main>
</body>

</html>