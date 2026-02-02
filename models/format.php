<?php

class Format
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getFormatById($formatId)
    {
        $sql = "SELECT * FROM formats WHERE format_id = ? AND status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $formatId);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getAllFormats()
    {
        $sql = "SELECT * FROM formats WHERE status = 1 ORDER BY name";
        $result = $this->conn->query($sql);

        $formats = [];
        while ($row = $result->fetch_assoc()) {
            $formats[] = $row;
        }
        return $formats;
    }

    public function getFormatByName($formatName)
    {
        $sql = "SELECT * FROM formats WHERE name LIKE ? AND status = 1";
        $stmt = $this->conn->prepare($sql);
        $like = "%" . $formatName . "%";
        $stmt->bind_param("s", $like);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function createFormat($name)
    {
        $sql = "INSERT INTO formats (name, status) VALUES (?, 1)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $name);
        return $stmt->execute();
    }

    public function updateFormat($formatId, $name)
    {
        $sql = "UPDATE formats SET name = ? WHERE format_id = ? AND status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $name, $formatId);
        return $stmt->execute();
    }

    public function deleteFormat($formatId)
    {
        $sql = "UPDATE formats SET status = 0 WHERE format_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $formatId);
        return $stmt->execute();
    }
}
