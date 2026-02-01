<?php

class Cinema
{
    private mysqli $conn;
    public function __construct($conn) // ham khoi tao
    {
        $this->conn = $conn;
    }
    public function getCinemas(){
        $sql = "SELECT * FROM cinemas";
        $result = $this->conn->query($sql);

        $cinemas = [];
        while ($row = $result->fetch_assoc()) {
            $cinemas[] = $row;
        }
        return $cinemas;
    }

    public function getCinemaById($id){
        $sql = "SELECT * FROM cinemas WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function createCinema($name, $address, $location_id, $status){
        $sql = "INSERT INTO cinemas (name, address, status, location_id) VALUES (?,?,?,?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssii", $name, $address, $status, $location_id);

        return $stmt->execute();
    }

    public function updateCinema($cinema_id, $name, $address, $status, $location_id){
        $sql = "UPDATE cinemas SET name = ?, address = ?, status = ?, location_id = ? WHERE id =?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssiii", $name, $address, $status, $location_id, $cinema_id);

        return $stmt->execute();
    }

    public function deleteCinema($id){
        $sql = "DELETE FROM cinemas WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i",$id);

        return $stmt->execute();
    }
}