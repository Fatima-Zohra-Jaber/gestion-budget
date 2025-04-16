

function openModal(mode, id) {
    if (mode === "edit") {
        document.getElementById("edit_id").value = id;
        document.getElementById("editModal").classList.remove("hidden");
    } else if (mode === "delete") {
        document.getElementById("confirmDeleteBtn").href = `delete_transaction.php?id=${id}`;
        document.getElementById("deleteModal").classList.remove("hidden");
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add("hidden");
}