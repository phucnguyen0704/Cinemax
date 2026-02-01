<?php

class PermissionService
{
    private $permissionModel;
    public function __construct($permissionModel)
    {
        $this->permissionModel = $permissionModel;
    }

    //Permission methods
    public function getAllPermissions()
    {
        return $this->permissionModel->getAllPermissions();
    }

    public function getPermissionById($permissionId)
    {
        return $this->permissionModel->getPermissionById($permissionId);
    }

    public function getPermissionByCode($permissionCode)
    {
        return $this->permissionModel->getPermissionByCode($permissionCode);
    }

    public function createPermission($permissionCode, $description)
    {
        if (empty($permissionCode)) {
            throw new InvalidArgumentException("All fields are required to create a permission.");
        }

        if ($this->getPermissionByCode($permissionCode)) {
            throw new InvalidArgumentException("Permission code already exists.");
        }

        return $this->permissionModel->createPermission($permissionCode, $description);
    }

    public function updatePermission($permissionId, $permissionCode, $description)
    {
        if (empty($permissionCode)) {
            throw new InvalidArgumentException("All fields are required to update a permission.");
        }

        return $this->permissionModel->updatePermission($permissionId, $permissionCode, $description);
    }

    public function deletePermission($permissionId)
    {
        return $this->permissionModel->deletePermission($permissionId);
    }
}
