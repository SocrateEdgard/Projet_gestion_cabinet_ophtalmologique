<?php
session_start();
include("../config/db.php");

// On vérifie si num_fiche est passé dans l'URL (ex: envoyer_medecin.php?id=10)
if (isset($_GET['id'])) {
    $num_fiche = intval($_GET['id']); // Utilise num_fiche au lieu de id[cite: 13, 14]

    try {
        // 1. Vérification avec le bon nom de colonne : num_fiche[cite: 12, 14]
        $check = $pdo->prepare("SELECT num_fiche FROM malades WHERE num_fiche = ?");
        $check->execute([$num_fiche]);
        $patient = $check->fetch();

        if ($patient) {
            // 2. Mise à jour du statut (Assurez-vous que la colonne 'statut' existe dans votre table)
            // Utilisation de num_fiche dans la clause WHERE[cite: 10, 13]
            $sql = "UPDATE malades SET statut = 'En attente médecin' WHERE num_fiche = ?";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$num_fiche])) {
                // Redirection vers la liste des malades
                header("Location: liste_malades.php?msg=Patient N°$num_fiche envoyé en consultation");
                exit();
            }
        } else {
            die("Erreur : Le numéro de fiche $num_fiche n'existe pas dans le système SGH-K.");
        }
    } catch (PDOException $e) {
        die("Erreur SQL : " . $e->getMessage());
    }
} else {
    header("Location: liste_malades.php");
    exit();
}
