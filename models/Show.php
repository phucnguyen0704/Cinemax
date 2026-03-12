<?php

class Show
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllShows()
    {
        $sql = "SELECT * FROM shows WHERE status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        foreach ($result as $row) {
            $shows[] = $row;
        }

        return $shows;
    }

    public function getShowById($id)
    {
        $sql = "SELECT * FROM shows WHERE id = ? AND status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getShowsByMovieId($movie_id)
    {
        $sql = "SELECT * FROM shows WHERE movie_id = ? AND status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $movie_id);
        $stmt->execute();
        $result = $stmt->get_result();
        foreach ($result as $row) {
            $shows[] = $row;
        }

        return $shows;
    }

    public function createShow($movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price)
    {
        $sql = "INSERT INTO shows (movie_id, hall_id, show_date, start_time, end_time, base_price) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiisss", $movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price);
        return $stmt->execute();
    }

    public function updateShow($id, $movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price)
    {
        $sql = "UPDATE shows SET movie_id = ?, hall_id = ?, show_date = ?, start_time = ?, end_time = ?, base_price = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiisssi", $movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price, $id);
        return $stmt->execute();
    }

    public function deleteShow($id)
    {
        $sql = "UPDATE shows SET status = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
