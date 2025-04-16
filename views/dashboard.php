<?php

    require '../config.php';
    require '../controllers/dashboard.php';

    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }

    $user = $_SESSION['user'];

    $total = totalCurrentMonth($conn);

    $typeSelected = $_POST['type'] ?? 'revenu';

    function getCategories($type, $connection) {
        $query = "SELECT * FROM categories WHERE type = :type";
        $stmt = $connection->prepare($query);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
    
    $categories = getCategories($typeSelected, $conn);

    $result;
    
    if (isset($_POST['afficher'])) {
        if ($typeSelected === 'revenu') {
            $result = totalIncomesByCategory($_POST['categorie'], $conn);

        } else {
            $result = totalExpensesByCategory($_POST['categorie'], $conn);

        }
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau de bord</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
    <?php  require '../header.php'; ?>
<div class="flex flex-wrap gap-4 p-6 bg-gray-50">
  <!-- Total Balance -->
  <div class="bg-white rounded-xl shadow-md p-6 w-72">
    <h2 class="text-sm font-semibold text-gray-500">Total Balance</h2>
    <p class="text-3xl font-bold text-blue-900 mt-2"><?= number_format(soldUser($conn), 2) ?> Dh </p>
    <div class="flex items-center text-sm text-green-500 mt-4 border-t-2 pt-2">
      <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
        <path d="M5 10l5-5 5 5H5z" />
      </svg>
      2.47%
      <span class="ml-2 text-gray-400">Last month <span class="font-medium">$24,478</span></span>
    </div>
  </div>

  <!-- Total Period Income -->
  <div class="bg-white rounded-xl shadow-md p-6 w-72">
    <h2 class="text-sm font-semibold text-gray-500">Total Period Income</h2>
    <p class="text-3xl font-bold text-blue-900 mt-2"><?= number_format($total['revenu'], 2) ?> Dh</p>
    <div class="flex items-center text-sm text-green-500 mt-4 border-t-2 pt-2">
    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        aria-hidden="true">
                        <path fill-rule="evenodd"
                            d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z"
                            clip-rule="evenodd"></path>
                    </svg>
      2.47%
      <span class="ml-2 text-gray-400">Last month <span class="font-medium">$24,478</span></span>
    </div>
  </div>

  <!-- Total Period Expenses -->
  <div class="bg-white rounded-xl shadow-md p-6 w-72">
    <h2 class="text-sm font-semibold text-gray-500">Total Period Expenses</h2>
    <p class="text-3xl font-bold text-blue-900 mt-2"><?= number_format($total['depense'], 2) ?> Dh</p>
    <div class="flex items-center text-sm text-red-500 mt-4 border-t-2 pt-2">
        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd"
                d="M12 13a1 1 0 100 2h5a1 1 0 001-1V9a1 1 0 10-2 0v2.586l-4.293-4.293a1 1 0 00-1.414 0L8 9.586 3.707 5.293a1 1 0 00-1.414 1.414l5 5a1 1 0 001.414 0L11 9.414 14.586 13H12z"
                clip-rule="evenodd"></path>
        </svg>
      2.47%
      <span class="ml-2 text-gray-400">Last month <span class="font-medium">$24,478</span></span>
    </div>
  </div>
</div>


    <div class="max-w-6xl mx-auto p-6">
        <h2 class="text-2xl font-semibold mb-4">Bienvenue, <?= htmlspecialchars($user['nom']) ?></h2>

      

        <form method="POST" class="mb-4">
            <label for="type">Type :</label>
            <select name="type" id="type" class="border p-2 rounded" >
                <option value="revenu" <?= $typeSelected === 'revenu' ? 'selected' : '' ?>>Revenu</option>
                <option value="depense" <?= $typeSelected === 'depense' ? 'selected' : '' ?>>Dépense</option>
            </select>

            <label for="categorie">Catégorie :</label>
            <select name="categorie" id="categorie" class="border p-2 rounded" >
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?=$_POST['categorie']===$cat['id']? 'selected':''?>><?= $cat['nom'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" name="afficher" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded">Afficher</button>
        </form>

        <div class="mb-4">
            <h3 class="text-xl">Total par catégorie</h3>
            <p><?= number_format($result ?? 0, 2) ?> MAD</p>
        </div>


        <div class="mb-4">
            <h3 class="text-xl">Transactions</h3>
            <a href="transactions.php" class="text-blue-500">Voir toutes les transactions</a>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
