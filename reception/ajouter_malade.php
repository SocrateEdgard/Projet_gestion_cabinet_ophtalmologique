<?php

/**
 * Ajouter un Malade - SGH Kolwezi
 * Chemin : projet_celestine/reception/ajouter_malade.php
 */
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. Sécurité : Vérification du rôle Réception (Rôle 2)[cite: 2]
if (!isset($_SESSION['id_role']) || (int)$_SESSION['id_role'] !== 2) {
    header('Location: ../auth/login.php');
    exit();
}

$message = "";

// 2. Traitement du formulaire[cite: 1, 2]
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = strtoupper(trim($_POST['nom_mal'] ?? ''));
    $postnom = strtoupper(trim($_POST['postnom_mal'] ?? ''));
    $sexe = $_POST['sexe_mal'] ?? 'M';
    $date_naiss = !empty($_POST['date_naiss']) ? $_POST['date_naiss'] : null;
    $adresse = trim($_POST['adresse_mal'] ?? '');
    $code_cat = $_POST['code_cat'] ?? '';
    $code_empl = !empty($_POST['code_empl']) ? $_POST['code_empl'] : 'INDIV';
    $id_createur = $_SESSION['id_user'] ?? null;

    if (!empty($nom) && !empty($postnom)) {
        try {
            $sql = "INSERT INTO malades (nom_mal, postnom_mal, sexe_mal, date_naiss, adresse_mal, code_cat, id_createur, code_empl) 
                    VALUES (:nom, :postnom, :sexe, :naiss, :adr, :cat, :user, :empl)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nom' => $nom,
                ':postnom' => $postnom,
                ':sexe' => $sexe,
                ':naiss' => $date_naiss,
                ':adr' => $adresse,
                ':cat' => $code_cat,
                ':user' => $id_createur,
                ':empl' => $code_empl
            ]);
            $message = "<div class='bg-emerald-50 text-emerald-600 p-4 rounded-2xl mb-6 font-bold text-sm border border-emerald-100 italic text-center'>Fiche patient enregistrée avec succès !</div>";
        } catch (PDOException $e) {
            $message = "<div class='bg-red-50 text-red-600 p-4 rounded-2xl mb-6 font-bold text-sm border border-red-100'>Erreur : " . $e->getMessage() . "</div>";
        }
    }
}

// 3. Récupérer les données pour les menus déroulants[cite: 2]
$employeurs = $pdo->query("SELECT code_empl, nom_empl FROM employeurs ORDER BY nom_empl ASC")->fetchAll(PDO::FETCH_ASSOC);
$categories = $pdo->query("SELECT code_cat, nom_cat FROM categories ORDER BY nom_cat ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau Malade</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        select {
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2394a3b8%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1.25rem center;
            background-size: 18px;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] min-h-screen flex">

    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="flex-1 p-8 md:p-12 overflow-y-auto">
        <!-- Titre et Navigation -->
        <header class="mb-10 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight italic">Inscrire un malade</h1>
            </div>
        </header>

        <!-- Formulaire Centralisé (Basé sur Capture d'écran 2026-05-06 085645.png) -->
        <div class="max-w-5xl bg-white rounded-[40px] border border-slate-100 shadow-sm p-12 mx-auto">
            <?= $message ?>

            <form action="" method="POST" class="space-y-12">

                <!-- IDENTITÉ DU MALADE -->
                <div class="space-y-6">
                    <h2 class="text-[11px] font-black text-blue-600 uppercase tracking-[0.25em] border-b border-slate-50 pb-3">Identité du Malade</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 tracking-tight">Nom *</label>
                            <input type="text" name="nom_mal" required class="w-full bg-slate-50/50 border border-slate-200 px-5 py-4 rounded-2xl outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all font-semibold text-slate-700 uppercase">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 tracking-tight">Postnom *</label>
                            <input type="text" name="postnom_mal" required class="w-full bg-slate-50/50 border border-slate-200 px-5 py-4 rounded-2xl outline-none focus:ring-4 focus:ring-blue-50 focus:border-blue-500 transition-all font-semibold text-slate-700 uppercase">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 tracking-tight">Sexe *</label>
                            <select name="sexe_mal" required class="w-full bg-slate-50/50 border border-slate-200 px-5 py-4 rounded-2xl outline-none focus:ring-4 focus:ring-blue-50 font-semibold text-slate-700 appearance-none">
                                <option value="M">Masculin</option>
                                <option value="F">Féminin</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 tracking-tight">Date de Naissance</label>
                            <input type="date" name="date_naiss" class="w-full bg-slate-50/50 border border-slate-200 px-5 py-4 rounded-2xl outline-none focus:ring-4 focus:ring-blue-50 font-semibold text-slate-700">
                        </div>
                    </div>
                </div>

                <!-- PRISE EN CHARGE & CATÉGORIE -->
                <div class="space-y-6 pt-4">
                    <h2 class="text-[11px] font-black text-emerald-600 uppercase tracking-[0.25em] border-b border-slate-50 pb-3">Prise en Charge & Catégorie</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-8">
                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 tracking-tight">Employeur / Société</label>
                            <select name="code_empl" class="w-full bg-slate-50/50 border border-slate-200 px-5 py-4 rounded-2xl outline-none focus:ring-4 focus:ring-emerald-50 font-semibold text-slate-700 appearance-none">
                                <option value="INDIV">Individuel / Privé</option>
                                <?php foreach ($employeurs as $e): ?>
                                    <option value="<?= $e['code_empl'] ?>"><?= htmlspecialchars($e['nom_empl']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 tracking-tight">Catégorie Malade</label>
                            <select name="code_cat" required class="w-full bg-slate-50/50 border border-slate-200 px-5 py-4 rounded-2xl outline-none focus:ring-4 focus:ring-emerald-50 font-semibold text-slate-700 appearance-none">
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?= $c['code_cat'] ?>"><?= htmlspecialchars($c['nom_cat']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="pt-4">
                        <label class="block text-[10px] font-bold uppercase text-slate-400 mb-2 tracking-tight">Adresse de Résidence</label>
                        <textarea name="adresse_mal" rows="3" class="w-full bg-slate-50/50 border border-slate-200 px-5 py-4 rounded-3xl outline-none focus:ring-4 focus:ring-blue-50 font-semibold text-slate-700 resize-none"></textarea>
                    </div>
                </div>

                <!-- BOUTON D'ACTION -->
                <div class="pt-8">
                    <button type="submit" class="w-full bg-[#0F172A] text-white py-6 rounded-2xl font-bold text-xs uppercase tracking-[0.25em] shadow-2xl shadow-slate-200 hover:bg-slate-800 transition-all transform active:scale-[0.99] flex items-center justify-center gap-3">
                        Créer la fiche du patient
                    </button>
                </div>
            </form>
        </div>

        <footer class="mt-16 text-center text-slate-300 text-[10px] font-bold uppercase tracking-[0.4em]">
            SGH-K Kolwezi — Lualaba Province[cite: 2]
        </footer>
    </main>
</body>

</html>