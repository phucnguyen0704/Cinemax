    <?php
    class User
    {
        private $conn;

        public function __construct($conn)
        {
            $this->conn = $conn;
        }

        public function getAllUsers()
        {
            $sql = "SELECT * FROM users WHERE status = 1";
            $result = $this->conn->query($sql);

            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }

            return $users;
        }

        public function getUserByEmail($email)
        {
            $sql = "SELECT * FROM users WHERE email LIKE ? AND status = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();

            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }

        public function getUserByName($fullName)
        {
            $sql = "SELECT * FROM users WHERE full_name LIKE ? AND status = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $fullName);
            $stmt->execute();

            $result = $stmt->get_result();
            return $result->fetch_assoc(MYSQLI_ASSOC);
        }

        public function getUserById($UserID)
        {
            $sql = "SELECT * FROM users WHERE user_id = ? AND status = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $UserID);
            $stmt->execute();

            $result = $stmt->get_result();
            return $result->fetch_assoc(MYSQLI_ASSOC);
        }

        public function createUser($fullName, $email, $password, $phone, $role_id)
        {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO users (full_name, email, password_hash, phone, role_id)VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssssi", $fullName, $email, $hashedPassword, $phone, $role_id);

            return $stmt->execute();
        }

        public function updateUser($UserID, $fullName, $email, $phone, $role_id)
        {
            $sql = "UPDATE users SET full_name = ?, email = ?, phone = ?, role_id = ? WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssii", $fullName, $email, $phone, $role_id, $UserID);
            return $stmt->execute();
        }

        public function deleteUser($UserID)
        {
            $sql = "UPDATE users SET status = 0 WHERE user_id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $UserID);
            return $stmt->execute();
        }
    }
