function openUpdatePermissionModal(element) {
  openModal("updatePermissionModal");
  setTimeout(() => {
    document.getElementById("update_permission_id").value =
      element.dataset.permissionId || "";

    document.getElementById("update_permission_code").value =
      element.dataset.permissionCode || "";

    document.getElementById("update_permission_description").value =
      element.dataset.permissionDescription || "";
  }, 0);
}

function confirmDeletePermission(permissionId) {
  if (confirm("Bạn có chắc chắn muốn xóa quyền này không?")) {
    window.location.href =
      "../admin/index.php?page=permissions&action=delete&id=" + permissionId;
  }
}
