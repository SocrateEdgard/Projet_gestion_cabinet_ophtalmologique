<?php

/**
 * Page de détails d'une consultation spécifique - hospital_kolwezi_db
 * Chemin : projet_celestine/medecin/details_consultation.php
 */
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. Sécurité et ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: liste_consultations.php');
    exit();
}

$id_consult = (int)$_GET['id'];

try {
    // 2. Requête SQL adaptée strictement à votre structure hospital_kolwezi_db
    $query = "SELECT c.*, 
                     m.nom_mal, m.postnom_mal, m.sexe_mal, m.date_naiss, 
                     m.adresse_mal, m.num_matr_ag, m.code_empl,
                     e.nom_empl
              FROM consultations c
              JOIN malades m ON c.num_fiche = m.num_fiche
              LEFT JOIN employeurs e ON m.code_empl = e.code_empl
              WHERE c.id_consult = :id";

    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id_consult]);
    $data = $stmt->fetch();

    if (!$data) {
        die("Erreur : Consultation introuvable dans la base de données.");
    }
} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails Consultation #<?= $id_consult ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background: white;
            }

            .print-card {
                border: none !important;
                shadow: none !important;
            }
        }
    </style>
</head>

<body class="bg-[#F8FAFC] min-h-screen flex flex-row">

    <!-- Sidebar -->
    <?php
    $sidebarPath = __DIR__ . '/../includes/sidebar.php';
    if (file_exists($sidebarPath)) {
        include $sidebarPath;
    }
    ?>

    <div class="flex-1 p-6 md:p-10">
        <!-- Actions hautes -->
        <div class="flex justify-between items-center mb-8 no-print">
            <a href="liste_consultations.php" class="text-slate-500 hover:text-blue-600 font-bold flex items-center gap-2 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour à l'historique
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Carte Patient -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-[32px] p-8 border border-slate-200 shadow-sm sticky top-10">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl font-black mb-6">
                        <?= substr($data['nom_mal'], 0, 1) ?>
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 uppercase leading-tight mb-1">
                        <?= htmlspecialchars($data['nom_mal'] . ' ' . $data['postnom_mal']) ?>
                    </h2>
                    <p class="text-blue-600 font-bold text-xs mb-6 tracking-widest">DOSSIER N°<?= $data['num_fiche'] ?></p>

                    <div class="space-y-5 pt-6 border-t border-slate-100">
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Sexe & Naissance</p>
                            <p class="text-sm font-semibold text-slate-700">
                                <?= $data['sexe_mal'] ?> — <?= !empty($data['date_naiss']) ? date('d/m/Y', strtotime($data['date_naiss'])) : 'Non renseigné' ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Employeur / Entreprise</p>
                            <p class="text-sm font-bold text-orange-600">
                                <?= htmlspecialchars($data['nom_empl'] ?? 'Privé') ?>
                                <span class="text-slate-400 font-medium">(<?= htmlspecialchars($data['code_empl'] ?? 'N/A') ?>)</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Matricule Agent</p>
                            <p class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($data['num_matr_ag'] ?? 'Individuel') ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Adresse Domicile</p>
                            <p class="text-sm font-semibold text-slate-700"><?= htmlspecialchars($data['adresse_mal'] ?? 'Kolwezi') ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte Médicale -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-[32px] p-8 border border-slate-200 shadow-sm min-h-full">
                    <div class="flex justify-between items-start mb-10 pb-6 border-b border-slate-50">
                        <div>
                            <span class="bg-blue-100 text-blue-700 text-[10px] font-black px-3 py-1 rounded-lg uppercase tracking-widest">Fiche de Consultation</span>
                            <p class="text-slate-500 text-sm mt-2 font-medium">
                                Diagnostic du <?= date('d/m/Y', strtotime($data['date_diag'])) ?> à <?= date('H:i', strtotime($data['date_diag'])) ?>
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Frais de visite</p>
                            <p class="text-3xl font-black text-slate-900"><?= number_format($data['montant'], 0, ',', ' ') ?> <span class="text-xs text-slate-400">FC</span></p>
                        </div>
                    </div>

                    <div class="space-y-10">
                        <!-- Symptômes / Plainte -->
                        <div>
                            <h3 class="text-slate-900 font-bold mb-4 flex items-center gap-2">
                                <div class="w-1.5 h-4 bg-blue-500 rounded-full"></div>
                                Symptômes & Plaintes
                            </h3>
                            <div class="bg-slate-50 p-6 rounded-[24px] text-slate-600 leading-relaxed italic border border-slate-100">
                                "<?= nl2br(htmlspecialchars($data['plainte'])) ?>"
                            </div>
                        </div>

                        <!-- Diagnostic -->
                        <div>
                            <h3 class="text-slate-900 font-bold mb-4 flex items-center gap-2">
                                <div class="w-1.5 h-4 bg-orange-500 rounded-full"></div>
                                Conclusion Médicale
                            </h3>
                            <div class="inline-block bg-orange-50 text-orange-700 px-6 py-3 rounded-2xl font-extrabold border border-orange-100 text-lg shadow-sm">
                                <?= htmlspecialchars($data['diagnostic']) ?>
                            </div>
                        </div>

                        <!-- Traitement -->
                        <div>
                            <h3 class="text-slate-900 font-bold mb-4 flex items-center gap-2">
                                <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
                                Ordonnance & Traitement
                            </h3>
                            <div class="bg-white border-2 border-slate-50 p-8 rounded-[24px] text-slate-800 leading-loose text-lg shadow-inner">
                                <?= nl2br(htmlspecialchars($data['traitement'])) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer Mobile -->
    <?php
    $footerPath = __DIR__ . '/../includes/header_bottom.php';
    if (file_exists($footerPath)) {
        include $footerPath;
    }
    ?>

</body>

</html>