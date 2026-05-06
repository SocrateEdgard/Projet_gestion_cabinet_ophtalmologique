<?php

/**
 * Gestion des Employeurs et des Agents - SGH-K
 * Chemin : projet_celestine/reception/employeurs.php
 */
session_start();
require_once __DIR__ . '/../config/db.php';

// Protection de la page (Rôle 2 = Réception)[cite: 2]
if (!isset($_SESSION['id_role']) || (int)$_SESSION['id_role'] !== 2) {
    header('Location: ../auth/login.php');
    exit();
}

$message = "";

// 1. Traitement de l'ajout d'un employeur (Entreprise)[cite: 2]
if (isset($_POST['ajouter_employeur'])) {
    $code = strtoupper(trim($_POST['code_empl'] ?? ''));
    $nom = strtoupper(trim($_POST['nom_empl'] ?? ''));
    $adresse = trim($_POST['adresse_empl'] ?? '');

    if (!empty($nom) && !empty($code)) {
        try {
            $ins = $pdo->prepare("INSERT INTO employeurs (code_empl, nom_empl, adresse_empl) VALUES (?, ?, ?)");
            $ins->execute([$code, $nom, $adresse]);
            $message = "success_empl";
        } catch (PDOException $e) {
            $message = "error";
        }
    }
}

// 2. Traitement de l'ajout d'un agent (Correction des Warning & Nullable)[cite: 1, 2]
if (isset($_POST['ajouter_agent'])) {
    $matr    = strtoupper(trim($_POST['NumMatrAg'] ?? ''));
    $nom     = strtoupper(trim($_POST['NomAg'] ?? ''));
    $postnom = strtoupper(trim($_POST['PostNomAg'] ?? ''));
    $codeE   = $_POST['CodeEmpl'] ?? '';

    // Utilisation de null si le champ est vide pour respecter la structure hospital_kolwezi_db[cite: 2]
    $cat     = !empty($_POST['NumCatAg']) ? trim($_POST['NumCatAg']) : null;
    $siege   = !empty($_POST['NomSiègAg']) ? trim($_POST['NomSiègAg']) : null;
    $adresse = !empty($_POST['AdresseAg']) ? trim($_POST['AdresseAg']) : null;

    $nomConj = strtoupper(trim($_POST['NomConj'] ?? ''));
    $enfants = (int)($_POST['NbreEnf'] ?? 0);

    if (!empty($matr) && !empty($nom) && !empty($codeE)) {
        try {
            $sql = "INSERT INTO agents (NumMatrAg, NomAg, PostNomAg, CodeEmpl, NumCatAg, NomSiègAg, AdresseAg, NomConj, NbreEnf) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insAg = $pdo->prepare($sql);
            $insAg->execute([$matr, $nom, $postnom, $codeE, $cat, $siege, $adresse, $nomConj, $enfants]);
            $message = "success_agent";
        } catch (PDOException $e) {
            $message = "error_agent";
        }
    }
}

// 3. Récupération des données (Correction du ORDER BY)[cite: 2]
try {
    $employeurs = $pdo->query("SELECT e.*, COUNT(a.NumMatrAg) as nb_agents 
                               FROM employeurs e 
                               LEFT JOIN agents a ON e.code_empl = a.CodeEmpl 
                               GROUP BY e.code_empl 
                               ORDER BY e.nom_empl ASC")->fetchAll(PDO::FETCH_ASSOC);

    $agents = $pdo->query("SELECT a.*, e.nom_empl 
                           FROM agents a 
                           JOIN employeurs e ON a.CodeEmpl = e.code_empl 
                           ORDER BY a.NomAg ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Partenaires & Agents |</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] min-h-screen flex text-slate-900">

    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="flex-1 p-8 md:p-12">
        <!-- Header -->
        <header class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight italic">Gestion Partenaires</h1>
                <p class="text-slate-500 font-medium">Sociétés et agents de Kolwezi</p>
            </div>

            <div class="flex gap-4">
                <button onclick="document.getElementById('modal-agent').classList.remove('hidden')" class="bg-emerald-600 text-white px-6 py-3 rounded-2xl font-bold text-xs shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all">
                    + Nouvel Agent
                </button>
                <button onclick="document.getElementById('modal-empl').classList.remove('hidden')" class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-bold text-xs shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all">
                    + Nouvelle Société
                </button>
            </div>
        </header>

        <!-- Alertes -->
        <?php if (str_contains($message, 'success')): ?>
            <div class="bg-emerald-50 text-emerald-600 p-4 rounded-2xl border border-emerald-100 mb-6 font-bold text-sm animate-pulse">
                Opération effectuée avec succès !
            </div>
        <?php endif; ?>

        <!-- Grille des Employeurs -->
        <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
            <span class="w-2 h-8 bg-blue-600 rounded-full"></span> Entreprises Conventionnées
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <?php foreach ($employeurs as $emp): ?>
                <div class="bg-white p-6 rounded-[32px] border border-slate-200 shadow-sm hover:border-blue-300 transition-all">
                    <div class="text-blue-600 font-black text-xs mb-2"><?= htmlspecialchars($emp['code_empl']) ?></div>
                    <h3 class="text-lg font-bold truncate"><?= htmlspecialchars($emp['nom_empl']) ?></h3>
                    <div class="mt-4 flex items-center justify-between border-t pt-4 border-slate-50">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Effectif</span>
                        <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full font-bold text-sm"><?= $emp['nb_agents'] ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Liste des Agents -->
        <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
            <span class="w-2 h-8 bg-emerald-500 rounded-full"></span> Registre des Agents
        </h2>
        <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase">Matricule</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase">Nom & Postnom</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase">Employeur</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase">Siège / Service</th>
                            <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase">Situation</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($agents as $ag): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-blue-600"><?= htmlspecialchars($ag['NumMatrAg']) ?></td>
                                <td class="px-6 py-4">
                                    <div class="font-bold"><?= htmlspecialchars($ag['NomAg']) ?></div>
                                    <div class="text-xs text-slate-500"><?= htmlspecialchars($ag['PostNomAg']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-tight">
                                        <?= htmlspecialchars($ag['nom_empl']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 italic">
                                    <?= htmlspecialchars($ag['NomSiègAg'] ?? 'Non défini') ?>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 font-medium">
                                    Conj: <?= htmlspecialchars($ag['NomConj'] ?: 'Celib.') ?> (<?= $ag['NbreEnf'] ?> enfants)
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($agents)): ?>
                            <tr>
                                <td colspan="5" class="p-10 text-center text-slate-400 italic">Aucun agent enregistré pour le moment.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL : NOUVELLE SOCIÉTÉ -->
        <div id="modal-empl" class="fixed inset-0 bg-black/40 hidden flex items-center justify-center z-50 backdrop-blur-sm p-4">
            <div class="bg-white p-8 rounded-[40px] shadow-2xl max-w-md w-full border border-slate-100">
                <h2 class="text-2xl font-bold mb-6 italic">Ajouter une Société</h2>
                <form method="post">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Code Employeur (ex: GCM)</label>
                            <input type="text" name="code_empl" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-400 uppercase mb-2">Nom Complet</label>
                            <input type="text" name="nom_empl" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-4 mt-8">
                        <button type="button" onclick="document.getElementById('modal-empl').classList.add('hidden')" class="px-6 py-3 text-slate-400 font-bold text-xs uppercase hover:text-slate-600 transition-colors">Fermer</button>
                        <button type="submit" name="ajouter_employeur" class="bg-blue-600 text-white px-8 py-3 rounded-2xl font-bold text-xs uppercase shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL : NOUVEL AGENT -->
        <div id="modal-agent" class="fixed inset-0 bg-black/40 hidden flex items-center justify-center z-50 backdrop-blur-sm p-4">
            <div class="bg-white p-8 rounded-[40px] shadow-2xl max-w-2xl w-full border border-slate-100 overflow-y-auto max-h-[95vh]">
                <h2 class="text-2xl font-bold mb-6 italic">Enregistrer un Nouvel Agent</h2>
                <form method="post" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2 text-[10px] font-black text-blue-600 uppercase tracking-[0.2em] border-b pb-2 mb-2">Informations de Base</div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Numéro Matricule *[cite: 2]</label>
                        <input type="text" name="NumMatrAg" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Employeur *</label>
                        <select name="CodeEmpl" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Choisir la société...</option>
                            <?php foreach ($employeurs as $e): ?>
                                <option value="<?= $e['code_empl'] ?>"><?= htmlspecialchars($e['nom_empl'] . " (" . $e['code_empl'] . ")") ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nom *</label>
                        <input type="text" name="NomAg" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Postnom</label>
                        <input type="text" name="PostNomAg" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                    </div>

                    <div class="md:col-span-2 text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] border-b pb-2 mt-4 mb-2">Détails Service & Localisation</div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Siège / Site</label>
                        <input type="text" name="NomSiègAg" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500" placeholder="ex: KTC">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Catégorie</label>
                        <input type="text" name="NumCatAg" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500" placeholder="ex: CADRE">
                    </div>

                    <div class="md:col-span-2 text-[10px] font-black text-slate-600 uppercase tracking-[0.2em] border-b pb-2 mt-4 mb-2">Famille</div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nom du Conjoint</label>
                        <input type="text" name="NomConj" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase mb-1">Nombre d'enfants</label>
                        <input type="number" name="NbreEnf" min="0" value="0" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="md:col-span-2 flex justify-end space-x-4 mt-10">
                        <button type="button" onclick="document.getElementById('modal-agent').classList.add('hidden')" class="px-6 py-3 text-slate-400 font-bold text-xs uppercase hover:text-slate-600">Fermer</button>
                        <button type="submit" name="ajouter_agent" class="bg-emerald-600 text-white px-8 py-3 rounded-2xl font-bold text-xs uppercase shadow-lg shadow-emerald-100 hover:bg-emerald-700 transition-all">Valider l'Agent</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>