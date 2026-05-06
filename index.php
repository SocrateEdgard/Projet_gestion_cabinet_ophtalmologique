<?php

/**
 * APPLICATION DE GESTION HOSPITALIERE - KOLWEZI
 * Fichier : index.php (Racine)
 */
session_start();

// 1. VERIFICATION DE LA CONNEXION
// Si l'utilisateur n'est pas connecté, on le redirige vers la page de login
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

// 2. REDIRECTION SELON LE ROLE
// On oriente l'utilisateur directement vers son interface spécifique
// pour gagner du temps lors de la prise en charge des abonnés.
$role = $_SESSION['role'];

switch ($role) {
    case 'Réceptionniste':
        header('Location: reception/dashboard.php');
        exit();
        break;

    case 'Médecin':
        header('Location: medecin/dashboard.php');
        exit();
        break;

    case 'Admin':
        // L'admin peut rester sur cette page pour voir les statistiques globales
        break;

    default:
        // Par sécurité, on détruit la session si le rôle est inconnu
        session_destroy();
        header('Location: auth/login.php?error=role_inconnu');
        exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de ophtalmologique</title>
    <!-- Utilisation de Tailwind CSS pour un design moderne type Dribbble -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 font-sans text-gray-900">

    <div class="flex min-h-screen">
        <!-- Barre latérale (Sidebar) -->
        <aside class="w-64 bg-white border-r border-gray-200">
            <div class="p-6">
                <h2 class="text-xl font-bold text-blue-600">Hôpital Kolwezi</h2>
                <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest">Gestion Abonnés</p>
            </div>

            <nav class="mt-6 px-4">
                <a href="#" class="flex items-center p-3 bg-blue-50 text-blue-700 rounded-lg mb-2">
                    <span class="mr-3">📊</span> Dashboard Admin
                </a>
                <a href="admin/gestion_employeurs.php" class="flex items-center p-3 text-gray-600 hover:bg-gray-50 rounded-lg mb-2">
                    <span class="mr-3">🏢</span> Employeurs
                </a>
                <a href="admin/utilisateurs.php" class="flex items-center p-3 text-gray-600 hover:bg-gray-50 rounded-lg mb-2">
                    <span class="mr-3">👥</span> Utilisateurs
                </a>
                <a href="auth/logout.php" class="flex items-center p-3 text-red-600 hover:bg-red-50 rounded-lg mt-10">
                    <span class="mr-3">🚪</span> Déconnexion
                </a>
            </nav>
        </aside>

        <!-- Contenu Principal -->
        <main class="flex-1 p-8">
            <header class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold">Tableau de Bord Global</h1>
                    <p class="text-gray-500">Bienvenue, <?= htmlspecialchars($_SESSION['nom_complet']) ?> 👋</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded"><?= $role ?></span>
                </div>
            </header>

            <!-- Statistiques Rapides -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Total Malades -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-gray-400 text-sm font-medium uppercase">Total Abonnés</h3>
                    <p class="text-3xl font-bold mt-2">1,248</p>
                    <span class="text-green-500 text-sm font-medium mt-2 block">↑ 12% ce mois</span>
                </div>

                <!-- Employeurs Actifs -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-gray-400 text-sm font-medium uppercase">Employeurs</h3>
                    <p class="text-3xl font-bold mt-2">14</p>
                    <span class="text-gray-500 text-sm mt-2 block">GCM, SNCC, DGDA...[cite: 1]</span>
                </div>

                <!-- Consultations du jour -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-gray-400 text-sm font-medium uppercase">Consultations Jour</h3>
                    <p class="text-3xl font-bold mt-2">42</p>
                    <span class="text-blue-500 text-sm mt-2 block">En cours de traitement</span>
                </div>
            </div>

            <!-- Liste des derniers abonnés enregistrés -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50">
                    <h2 class="text-lg font-bold">Dernières Inscriptions</h2>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Fiche n°</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Malade</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Catégorie</th>
                            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase">Employeur</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Exemple de ligne -->
                        <tr>
                            <td class="px-6 py-4 font-medium">00452</td>
                            <td class="px-6 py-4">KABILA Jean</td>
                            <td class="px-6 py-4"><span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs">Agent</span></td>
                            <td class="px-6 py-4 text-gray-500">Gécamines[cite: 1]</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>

</html>