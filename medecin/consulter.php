<?php

/**
 * Page d'enregistrement d'une consultation
 * Chemin : projet_celestine/medecin/consultations.php
 */
session_start();
require_once __DIR__ . '/../config/db.php';

// Protection : Accès réservé au médecin
if (!isset($_SESSION['id_role']) || (int)$_SESSION['id_role'] !== 1) {
    header('Location: ../auth/login.php');
    exit();
}

$id_medecin = $_SESSION['user_id'];
$message = "";
$status = "";

// Traitement de l'enregistrement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enregistrer_consult'])) {
    $num_fiche = $_POST['num_fiche'];
    $plainte = trim($_POST['plainte']);
    $diagnostic = trim($_POST['diagnostic']);
    $traitement = trim($_POST['traitement']);
    $montant = !empty($_POST['montant']) ? $_POST['montant'] : 0;

    if (!empty($num_fiche) && !empty($diagnostic)) {
        try {
            // Utilisation de 'Diagnostic' avec D majuscule comme dans votre table
            $sql = "INSERT INTO consultations (num_fiche, medecin_id, plainte, Diagnostic, traitement, montant, date_diag) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$num_fiche, $id_medecin, $plainte, $diagnostic, $traitement, $montant])) {
                $message = "Consultation enregistrée avec succès !";
                $status = "success";
            }
        } catch (PDOException $e) {
            $message = "Erreur lors de l'enregistrement : " . $e->getMessage();
            $status = "error";
        }
    }
}

// Récupération des malades
$stmt_malades = $pdo->query("SELECT num_fiche, nom_mal, postnom_mal FROM malades ORDER BY nom_mal ASC");
$liste_malades = $stmt_malades->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Consultation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] min-h-screen flex flex-row">

    <?php
    $sidebarPath = __DIR__ . '/../includes/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }
    ?>

    <main class="flex-1 p-6 md:p-10 overflow-y-auto">

        <div class="max-w-4xl mx-auto">
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Nouvelle Consultation</h1>
                <p class="text-slate-500 font-medium">Enregistrement d'un nouveau diagnostic médical pour Kolwezi.</p>
            </div>

            <?php if ($message): ?>
                <div class="mb-6 p-4 border rounded-2xl text-center font-bold shadow-sm <?= $status === 'success' ? 'bg-emerald-50 border-emerald-100 text-emerald-600' : 'bg-red-50 border-red-100 text-red-600' ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-[32px] shadow-sm border border-slate-200 overflow-hidden">
                <form action="" method="POST" class="p-8 md:p-10 space-y-8">

                    <div class="space-y-3">
                        <label class="block text-sm font-black text-slate-700 uppercase tracking-widest text-[10px]">Patient à consulter</label>
                        <select name="num_fiche" required class="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all appearance-none font-semibold text-slate-700">
                            <option value="">-- Rechercher un patient --</option>
                            <?php foreach ($liste_malades as $m): ?>
                                <option value="<?= $m['num_fiche'] ?>">
                                    FICHE N°<?= str_pad($m['num_fiche'], 4, '0', STR_PAD_LEFT) ?> — <?= htmlspecialchars($m['nom_mal'] . ' ' . $m['postnom_mal']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-widest text-[10px]">Symptômes & Plaintes</label>
                            <textarea name="plainte" rows="4" class="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all resize-none" placeholder="Description des symptômes..."></textarea>
                        </div>

                        <div class="space-y-3">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-widest text-[10px]">Diagnostic Médical</label>
                            <textarea name="diagnostic" required rows="4" class="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all resize-none" placeholder="Votre conclusion médicale..."></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-widest text-[10px]">Ordonnance</label>
                            <textarea name="traitement" rows="3" class="w-full bg-slate-50 border border-slate-200 p-4 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all resize-none" placeholder="Médicaments prescrits..."></textarea>
                        </div>
                        <div class="space-y-3">
                            <label class="block text-sm font-black text-slate-700 uppercase tracking-widest text-[10px]">Frais (FC)</label>
                            <div class="relative">
                                <input type="number" step="0.01" name="montant" class="w-full bg-slate-50 border border-slate-200 p-4 pl-14 rounded-2xl focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all font-bold text-lg" placeholder="0">
                                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-xs uppercase">FC</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" name="enregistrer_consult" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-5 rounded-[20px] shadow-xl shadow-blue-200 transition-all transform active:scale-95 uppercase tracking-widest text-sm">
                            Confirmer l'enregistrement
                        </button>
                    </div>
                </form>
            </div>

            <p class="text-center text-slate-400 text-[10px] mt-12 font-bold uppercase tracking-[0.3em]">
                Service Hospitalier de Kolwezi — SGH-K
            </p>
        </div>
    </main>

    <?php
    $footerPath = __DIR__ . '/../includes/header_bottom.php';
    if (file_exists($footerPath)) {
        include $footerPath;
    }
    ?>

</body>

</html>