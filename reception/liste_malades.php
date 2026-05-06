<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. Sécurité
if (!isset($_SESSION['id_role']) || (int)$_SESSION['id_role'] !== 2) {
    header('Location: ../auth/login.php');
    exit();
}

// 2. Paramètres de recherche et pagination
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Nombre de patients par page
$offset = ($page - 1) * $limit;

// 3. Construction de la requête avec filtres
$whereClauses = [];
$params = [];

if (!empty($search)) {
    $whereClauses[] = "(m.nom_mal LIKE :search OR m.postnom_mal LIKE :search OR m.num_fiche LIKE :search)";
    $params[':search'] = "%$search%";
}

$whereSql = !empty($whereClauses) ? " WHERE " . implode(" AND ", $whereClauses) : "";

try {
    // Compter le total pour la pagination
    $totalQuery = "SELECT COUNT(*) FROM malades m $whereSql";
    $stmtTotal = $pdo->prepare($totalQuery);
    $stmtTotal->execute($params);
    $totalRows = $stmtTotal->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    // Récupérer les données
    $query = "SELECT m.*, e.nom_empl 
              FROM malades m 
              LEFT JOIN employeurs e ON m.code_empl = e.code_empl 
              $whereSql 
              ORDER BY m.num_fiche DESC 
              LIMIT $limit OFFSET $offset";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $malades = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage()); // Log discret côté serveur
    die("Une erreur technique est survenue.");
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre Médical</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] flex min-h-screen">

    <!-- Sidebar (Récupérée dynamiquement) -->
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="flex-1 p-6 lg:p-10">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-10 gap-6">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">Registre Médical</h1>
            </div>

            <form method="GET" class="flex w-full lg:w-auto gap-3">
                <div class="relative flex-1 lg:w-80">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                        placeholder="Nom, Postnom ou N° Fiche..."
                        class="w-full bg-white border border-slate-200 pl-4 pr-4 py-3 rounded-2xl outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition-all shadow-sm text-sm">
                </div>
                <button type="submit" class="bg-emerald-600 text-white px-6 py-3 rounded-2xl font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-100">
                    Filtrer
                </button>
            </form>
        </div>

        <!-- Tableau -->
        <div class="bg-white rounded-[32px] border border-slate-200 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 border-b border-slate-100">
                    <tr class="text-slate-400 text-[10px] font-black uppercase tracking-widest">
                        <th class="px-8 py-5">N° Fiche</th>
                        <th class="px-8 py-5">Identité du Patient</th>
                        <th class="px-8 py-5">Genre</th>
                        <th class="px-8 py-5">Prise en charge</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($malades)): ?>
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="text-slate-400 font-medium italic">Aucun patient ne correspond à votre recherche.</div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($malades as $m): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors group">
                                <td class="px-8 py-6">
                                    <span class="bg-slate-100 text-slate-600 px-3 py-1.5 rounded-lg font-extrabold text-xs">
                                        #<?= str_pad($m['num_fiche'], 5, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6 font-bold text-slate-800 uppercase text-sm">
                                    <?= htmlspecialchars($m['nom_mal'] . ' ' . $m['postnom_mal'], ENT_QUOTES) ?>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-[10px] font-black px-2 py-1 rounded <?= $m['sexe_mal'] == 'M' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' ?>">
                                        <?= $m['sexe_mal'] == 'M' ? 'MASCULIN' : 'FÉMININ' ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xs font-semibold text-slate-500">
                                        <?= htmlspecialchars($m['nom_empl'] ?? 'Privé (Individuel)', ENT_QUOTES) ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex gap-2">
                                        <a href="fiche_detail.php?id=<?= $m['num_fiche'] ?>" class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-xl transition-all">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
                <div class="p-6 bg-slate-50/50 border-t border-slate-100 flex justify-center gap-2">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"
                            class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all <?= $i === $page ? 'bg-emerald-600 text-white shadow-lg' : 'bg-white text-slate-500 hover:bg-slate-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </div>

        <footer class="mt-10 text-center text-slate-400 text-[10px] font-bold uppercase tracking-[0.3em]">
            SGH - Direction Provinciale de la Santé - Lualaba
        </footer>
    </main>
</body>

</html>