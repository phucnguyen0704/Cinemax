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
        $sql = "SELECT s.*, m.title AS movie_title, h.name AS hall_name, c.name AS cinema_name, c.cinema_id AS cinema_id 
                FROM shows s 
                JOIN movies m ON s.movie_id = m.movie_id 
                JOIN halls h ON s.hall_id = h.hall_id
                JOIN cinemas c ON h.cinema_id = c.cinema_id
                ";
        $result = $this->conn->query($sql);

        $shows = [];
        while ($row = $result->fetch_assoc()) {
            $shows[] = $row;
        }

        return $shows;
    }

    public function getShowById($id)
    {
        $sql = "SELECT * FROM shows WHERE id = ?";
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
                WHERE s.movie_id = ?";
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
        $sql = "INSERT INTO shows 
            (movie_id, hall_id, show_date, start_time, end_time, base_price, status) 
            VALUES (?, ?, ?, ?, ?, ?, 0)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "iisssd",
            $movie_id,
            $hall_id,
            $show_date,
            $start_time,
            $end_time,
            $base_price
        );

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        return true;
    }

    public function updateShow($id, $movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price, $status)
    {
        $sql = "UPDATE shows SET movie_id = ?, hall_id = ?, show_date = ?, start_time = ?, end_time = ?, base_price = ?, status WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iiissdii", $movie_id, $hall_id, $show_date, $start_time, $end_time, $base_price, $status, $id);
        return $stmt->execute();
    }

    public function deleteShow($id)
    {
        $sql = "DELETE shows WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
