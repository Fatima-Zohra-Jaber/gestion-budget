<?php
session_start();
require '../config.php';
require '../controllers/transactions.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$transactions = listTransactions($conn);

if(isset($_POST['edit'])){
    $idTransaction = $_POST['transaction_id'];
    $newTransaction['montant'] = $_POST['montant'];
    $newTransaction['description'] = $_POST['description'];
    $newTransaction['date_transaction'] = $_POST['date_transaction'];
    $newTransaction['category_id'] = $_POST['category_id'];
    editTransaction($idTransaction,$newTransaction,$conn);
}
if(isset($_POST['delete'])){
    $idTransaction = $_POST['transaction_id'];
    deleteTransaction($idTransaction,$conn);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Transactions</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.1.2/dist/tailwind.min.css" rel="stylesheet">
<!-- AlpineJS -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100">
<?php  require '../header.php'; ?>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Historique des Transactions</h1>

        <!-- Search and Add User (Static) -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <div class="w-full md:w-1/3 mb-4 md:mb-0">
                <input type="text" placeholder="Search users..." class="w-full px-4 py-2 rounded-md border border-gray-300 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <a href="" >
                 <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-300">
                Add New User
            </button>
            </a>
           
        </div>

        <!-- User Table -->
        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Catégorie</th>
                        <th class="px-4 py-2 text-left">Description</th>
                        <th class="px-4 py-2 text-left">Montant</th>
                        <th class="px-4 py-2 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm">
                <?php foreach ($transactions as $transaction): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left"><?= htmlspecialchars($transaction['date_transaction']) ?></td>
                        <td class="py-3 px-6 text-left"><?= htmlspecialchars($transaction['nom']) ?></td>
                        <td class="py-3 px-6 text-left"><?= htmlspecialchars($transaction['description']) ?></td>
                        <?php if ($transaction['type'] === "revenu"): ?>
                        <td class="py-3 px-6 text-left text-green-500"><?= number_format($transaction['montant'], 2) ?></td>
                        <?php else: ?>
                            <td class="py-3 px-6 text-left text-red-500">-<?= number_format($transaction['montant'], 2) ?></td>
                        <?php endif; ?>
                        <td class="py-3 px-6 text-left">
                            <div class="flex item-center justify-center">
                                <button onclick='openEditModal(<?= json_encode($transaction, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="w-4 transform hover:text-blue-500 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button data-id="<?= htmlspecialchars($transaction['id']) ?>" onclick="openDeleteModal(this)" class="w-4 transform hover:text-red-500 hover:scale-110">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <!-- Modal Modifier -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
  <div class="bg-white rounded-lg w-full max-w-md p-6">
    <h2 class="text-xl font-semibold mb-4">Modifier la transaction</h2>
    <form action="" method="POST" class="space-y-4">
      <input type="hidden" name="transaction_id" id="edit_id">

      <input type="number" name="montant" id="edit_montant" class="w-full border p-2 rounded">
      <input type="date" name="date_transaction" id="edit_date" required class="w-full border p-2 rounded">
      <textarea name="description" id="edit_description" class="w-full border p-2 rounded" rows="2"></textarea>
      
      <div class="inline-flex border border-blue-400 rounded-full overflow-hidden text-sm">
        <label class="px-4 py-2 font-medium cursor-pointer transition-colors">
            <input type="radio" name="type" value="depense" class="hidden"> Dépense
        </label>
        <label class="px-4 py-2 font-medium cursor-pointer transition-colors">
            <input type="radio" name="type" value="revenu" class="hidden"> Revenu
        </label>
      </div>



      <select name="category_id" id="edit_category" class="w-full border p-2 rounded">
        <?php 
        $categories = listCategories($type, $conn);
        foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nom']) ?></option>
        <?php endforeach; ?>
      </select>

      <div class="flex justify-end space-x-2 pt-4 border-t">
        <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 text-gray-500 hover:text-red-500">Annuler</button>
        <button type="submit" name="edit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Modifier</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Supprimer -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 hidden">
  <div class="bg-white rounded-lg w-full max-w-md p-6">
    <h2 class="text-xl font-semibold mb-4 text-red-600">Confirmer la suppression</h2>
    <p>Êtes-vous sûr de vouloir supprimer cette transaction ?</p>
    <form method="POST" class="flex justify-end pt-4 space-x-2">
        <input type="hidden" name="transaction_id" id="delete_id">
        <button onclick="closeModal('deleteModal')" class="px-4 py-2 text-gray-600 hover:text-red-500">Annuler</button>
        <button type="submit" name="delete" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Supprimer</button>
    </form>
  </div>
</div>

<script>
function openEditModal(transaction) {
    document.getElementById('edit_id').value = transaction.id;
    document.getElementById('edit_montant').value = transaction.montant;
    document.getElementById('edit_date').value = transaction.date_transaction;
    document.getElementById('edit_description').value = transaction.description;
    document.getElementById('edit_category').value = transaction.category_id;
    document.getElementById('editModal').classList.remove("hidden");
}

function openDeleteModal(button) {
    const id = button.getAttribute('data-id');
    document.getElementById('delete_id').value = id;
    document.getElementById('deleteModal').classList.remove("hidden");
}

function closeModal(id) {
    document.getElementById(id).classList.add("hidden");
}
</script>

</body>
</html>
