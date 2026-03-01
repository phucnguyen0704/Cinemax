<?php

class Role_permissions
{
    private $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM role_permissions";
        $result = $this->conn->query($sql);
        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }

        return $data;
    }

    public function getPermissionsByRoleId($roleId)
    {
        $sql = "SELECT permission_id FROM role_permissions WHERE role_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $roleId);
        $stmt->execute();
        $result = $stmt->get_result();
        $permissionIds = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $permissionIds[] = $row['permission_id'];
            }
        }

        return $permissionIds;
    }

    public function truncate()
    {
        $this->conn->query("DELETE FROM role_permissions");
    }

    public function insert($roleId, $permissionId)
    {
        $sql = "INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $roleId, $permissionId);
        $stmt->execute();
    }

    public function save($rolePermissions)
    {
        $this->truncate();

        foreach ($rolePermissions as $roleId => $permissionIds) {
            foreach ($permissionIds as $permissionId) {
                $this->insert($roleId, $permissionId);
            }
        }
    }
}
