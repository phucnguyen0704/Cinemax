<?php

class Ticket
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function getAllTickets()
    {
        $sql = "SELECT t.*, h.name AS hall_name, c.name AS cinema_name, s.row_name AS seat_name, s.seat_number AS seat_number, st.type_name AS type_name
                FROM tickets t
                JOIN shows sh ON sh.show_id = t.show_id
                JOIN halls h ON h.hall_id = sh.hall_id
                JOIN cinemas c ON c.cinema_id = h.cinema_id
                JOIN seats s ON s.seat_id = t.seat_id
                JOIN seat_types st ON s.seat_type_id = st.seat_type_id
        ";
        $result = $this->conn->query($sql);

        $tickets = [];
        while ($row = $result->fetch_assoc()) {
            $tickets[] = $row;
        }

        return $tickets;
    }

    public function getTicketByShowId($show_id)
    {
        $sql = "SELECT t.*, h.name AS hall_name, c.name AS cinema_name, s.row_name AS row_name, s.seat_number AS seat_number, st.*
                FROM tickets t
                JOIN shows sh ON sh.show_id = t.show_id
                JOIN halls h ON h.hall_id = sh.hall_id
                JOIN cinemas c ON c.cinema_id = h.cinema_id
                JOIN seats s ON s.seat_id = t.seat_id
                JOIN seat_types st ON s.seat_type_id = st.seat_type_id

                WHERE sh.show_id = ?
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $show_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $tickets = [];
        foreach ($result as $row) {
            $tickets[] = $row;
        }

        return $tickets;
    }

    public function getallSeatsByShow($show_id)
    {
        $sql = " SELECT 
                s.*,
                st.price_multiplier,
                sh.base_price
            FROM seats s
            JOIN halls h ON s.hall_id = h.hall_id
            JOIN shows sh ON sh.hall_id = h.hall_id
            JOIN seat_types st ON st.seat_type_id = s.seat_type_id
            WHERE sh.show_id = ?
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $show_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $seats = [];
        foreach ($result as $row) {
            $seats[] = $row;
        }

        return $seats;
    }


    public function createTicket($show_id, $seat_id, $bill_id, $price, $status)
    {
        $sql = "INSERT INTO tickets (show_id, seat_id, bill_id, price, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("iiids", $show_id, $seat_id, $bill_id, $price, $status);

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        return true;
    }

    public function updateStatusTicket($ticket_id, $status)
    {
        $sql = "UPDATE tickets SET status = ? WHERE ticket_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $status, $ticket_id);
        return $stmt->execute();
    }

    public function updateBillIdTicket($ticket_id, $bill_id)
    {
        $sql = "UPDATE tickets SET bill_id = ? WHERE ticket_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $bill_id, $ticket_id);
        return $stmt->execute();
    }

    function getTicketBySeatId($seat_id)
    {
        $sql = "SELECT * FROM tickets WHERE seat_id = ? AND status = 'available'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $seat_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }

    public function updateHold($ticketId, $sessionId, $expiredAt)
    {
        $sql = "UPDATE tickets 
        SET status='booked',
            session_id=?,
            hold_expired_at=?
        WHERE ticket_id=? AND status='available'";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("ssi", $sessionId, $expiredAt, $ticketId);

        if (!$stmt->execute()) {
            die("Execute failed: " . $stmt->error);
        }

        return $stmt->affected_rows; // 👈 debug cực quan trọng
    }

    public function releaseBySession($sessionId)
    {
        $sql = "UPDATE tickets 
                SET status='available',
                session_id=NULL,
                hold_expired_at=NULL
                WHERE status='booked'
                AND hold_expired_at < NOW()";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $sessionId);
        $stmt->execute();
    }

    public function confirmBySession($sessionId)
    {
        $sql = "UPDATE tickets 
            SET status='paid'
            WHERE session_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $sessionId);
        $stmt->execute();
    }

    public function clearExpiredSeats()
    {
        $sql = "UPDATE tickets 
            SET status='available',
                session_id=NULL,
                hold_expired_at=NULL
            WHERE status='booking'
            AND hold_expired_at < NOW()";

        $this->conn->query($sql);
    }

    public function getTicketBySeatAndShow($seat_id, $show_id)
    {
        $sql = "SELECT * FROM tickets 
            WHERE seat_id = ? AND show_id = ? AND status = 'available'
            LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $seat_id, $show_id);
        $stmt->execute();
        $result = $stmt->get_result();

        return $result->fetch_assoc();
    }
}
