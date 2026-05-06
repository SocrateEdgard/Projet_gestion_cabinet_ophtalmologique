<?php
session_start();

$error = "";

// Identifiants stockés directement dans le code
$utilisateurs_autorises = [
    'medecin' => [
        'password' => '1234',
        'nom' => 'medecin',
        'role_id' => 1,
        'redirect' => '../medecin/dashboard.php'
    ],
    'reception' => [
        'password' => '1234',
        'nom' => 'reception',
        'role_id' => 2,
        'redirect' => '../reception/dashboard.php'
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Vérification si l'utilisateur existe dans notre tableau ci-dessus
    if (isset($utilisateurs_autorises[$username])) {
        $user = $utilisateurs_autorises[$username];

        // Vérification du mot de passe
        if ($password === $user['password']) {
            $_SESSION['user_id'] = ($username === 'medecin') ? 1 : 2;
            $_SESSION['username'] = $username;
            $_SESSION['nom_complet'] = $user['nom'];
            $_SESSION['id_role'] = $user['role_id'];

            header('Location: ' . $user['redirect']);
            exit();
        } else {
            $error = "Mot de passe incorrect.";
        }
    } else {
        $error = "Identifiant inconnu.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-[400px]">
        <div class="bg-white rounded-[32px] shadow-sm border border-slate-100 p-10">
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-2xl mb-4 shadow-lg shadow-blue-200">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-slate-800">Accès Direct</h2>
                <p class="text-slate-500 text-sm mt-2">Utilisez vos codes de session</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-3 rounded-xl text-sm mb-6 border border-red-100 text-center">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Identifiant</label>
                    <input type="text" name="username" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">Mot de passe</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-2xl shadow-lg transition-all transform active:scale-[0.98]">
                    Se connecter
                </button>
            </form>
        </div>
    </div>
</body>

</html>