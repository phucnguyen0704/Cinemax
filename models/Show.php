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
        $sql = "SELECT s.*, m.title AS movie_title 
                FROM shows s 
                JOIN movies m ON s.movie_id = m.movie_id 
                WHERE s.status = 1";
        $result = $this->conn->query($sql);

        $shows = [];
        while ($row = $result->fetch_assoc()) {
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
        $sql = "SELECT s.*, m.title AS movie_title 
                FROM shows s 
                JOIN movies m ON s.movie_id = m.id 
                WHERE s.movie_id = ? AND s.status = 1";
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

    public function getShowsByTimeRangeofHalls($hall_id, $show_date, $start_time, $end_time)
    {
        $sql = "SELECT * FROM shows WHERE hall_id = ? AND show_date = ? AND ((start_time <= ? AND end_time > ?) OR (start_time < ? AND end_time >= ?)) AND status = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("isssss", $hall_id, $show_date, $start_time, $start_time, $end_time, $end_time);
        $stmt->execute();
        $result = $stmt->get_result();
        foreach ($result as $row) {
            $shows[] = $row;
        }

        return $shows;
    }
}
