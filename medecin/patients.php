<?php

/**
 * Répertoire des Patients en attente - SGH-K
 * Un patient disparaît de cette liste après sa consultation
 */
session_start();
require_once __DIR__ . '/../config/db.php';

// Protection : Accès réservé au médecin (Rôle 1)
if (!isset($_SESSION['id_role']) || (int)$_SESSION['id_role'] !== 1) {
    header('Location: ../auth/login.php');
    exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Requête SQL : On utilise NOT IN pour exclure les patients qui ont déjà une consultation
$query = "SELECT m.*, c.nom_cat, e.nom_empl 
          FROM malades m
          LEFT JOIN categories c ON m.code_cat = c.code_cat
          LEFT JOIN employeurs e ON m.code_empl = e.code_empl
          WHERE m.num_fiche NOT IN (SELECT num_fiche FROM consultations)";

// Gestion de la recherche
if (!empty($search)) {
    $query .= " AND (m.nom_mal LIKE ? OR m.postnom_mal LIKE ? OR m.num_fiche LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
} else {
    $query .= " ORDER BY m.num_fiche DESC";
    $params = [];
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$patients = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Répertoire des Patients</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-slate-50 flex flex-row min-h-screen">

    <?php include(__DIR__ . '/../includes/sidebar.php'); ?>

    <main class="flex-1 p-8 overflow-y-auto">
        <div class="max-w-6xl mx-auto">

            <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Répertoire des Patients</h1>
                    <p class="text-slate-500 text-sm font-medium">Liste des dossiers en attente de consultation à Kolwezi.</p>
                </div>

                <form action="" method="GET" class="flex gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                            placeholder="Rechercher un patient..."
                            class="px-5 py-3 pl-12 rounded-2xl border border-slate-200 focus:ring-4 focus:ring-blue-50 outline-none w-72 shadow-sm transition-all text-sm">
                        <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 text-sm">
                        Filtrer
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 text-slate-400 text-[11px] font-bold uppercase tracking-[0.2em] border-b border-slate-50">
                                <th class="px-8 py-5">N° Fiche</th>
                                <th class="px-8 py-5">Identité du Patient</th>
                                <th class="px-8 py-5">Sexe & Âge</th>
                                <th class="px-8 py-5">Catégorie</th>
                                <th class="px-8 py-5 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if (empty($patients)): ?>
                                <tr>
                                    <td colspan="5" class="p-24 text-center">
                                        <div class="flex flex-col items-center justify-center text-slate-400">
                                            <svg class="w-12 h-12 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <p class="italic text-sm">Aucun patient en attente pour le moment.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($patients as $p): ?>
                                    <tr class="hover:bg-slate-50/50 transition-all group">
                                        <td class="px-8 py-6">
                                            <span class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-xl font-bold text-[11px]">
                                                #<?= str_pad($p['num_fiche'], 4, '0', STR_PAD_LEFT) ?>
                                            </span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors uppercase text-sm tracking-tight">
                                                <?= htmlspecialchars($p['nom_mal'] . ' ' . $p['postnom_mal']) ?>
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-medium uppercase tracking-tight mt-1">
                                                <?= htmlspecialchars($p['adresse_mal'] ?? 'Kolwezi / Lualaba') ?>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-sm">
                                            <span class="font-bold text-slate-700"><?= $p['sexe_mal'] ?></span>
                                            <span class="text-slate-300 mx-2">•</span>
                                            <span class="text-slate-500 font-medium text-xs">
                                                <?php
                                                $dateNaiss = new DateTime($p['date_naiss']);
                                                $age = (new DateTime())->diff($dateNaiss)->y;
                                                echo $age . " ans";
                                                ?>
                                            </span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="text-[11px] font-bold text-slate-600 mb-1"><?= htmlspecialchars($p['nom_cat'] ?? 'Standard') ?></div>
                                            <div class="text-[10px] text-emerald-600 font-black uppercase tracking-widest bg-emerald-50 px-2 py-0.5 rounded-md inline-block">
                                                <?= htmlspecialchars($p['nom_empl'] ?? 'Individuel') ?>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <a href="consulter.php?num_fiche=<?= $p['num_fiche'] ?>"
                                                class="bg-slate-900 text-white text-[10px] font-black px-5 py-2.5 rounded-xl hover:bg-blue-600 transition-all uppercase tracking-widest shadow-xl shadow-slate-100">
                                                Consulter
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <footer class="mt-12 text-center text-slate-400 text-[10px] font-bold uppercase tracking-[0.4em]">
                Système de Gestion Hospitalière — Kolwezi
            </footer>
        </div>
    </main>

</body>

</html>