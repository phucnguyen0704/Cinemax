<?php

class Role_permissionsController
{
    private $rolePermissionsService;

    public function __construct($rolePermissionsService)
    {
        $this->rolePermissionsService = $rolePermissionsService;
    }

    public function getAllRolePermissions()
    {
        $data = $this->rolePermissionsService->getAll();
        $result = [];

        foreach ($data as $item) {
            $result[$item['role_id']][] = $item['permission_id'];
        }

        return $result;
    }

    public function saveRolePermissions()
    {
        $this->rolePermissionsService->save($_POST['role_permissions'] ?? []);

        header('Location: index.php?page=permissions&save=1');
        exit;
    }
}
