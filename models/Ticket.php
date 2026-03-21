<?php

class Ticket
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllTickets()
    {
        $sql = "SELECT t*, s.seat_id AS seat_id, h.name AS hall_name, c.name AS cinema_name, s.seat_type_id AS seat_type_id, st.type_name
                FROM tickets t, shows sh, halls h, cinema c, seats s, seat_tyoes st
                JOIN show s ON sh.show_id = t.show_id
                JOIN halls h ON h.hall_id = sh.show_id
                JOIN cinema c ON c.cinema_id = h.cinema_id
                JOIN seats s ON s.hall_id = h.hall_id
                JOIN seat_types ON st.seat_type_id = s.seat_type_id
                
        ";
    }
}
