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
            $sql = "SELECT * FROM users WHERE Status = 1";
            $result = $this->conn->query($sql);

            $users = [];
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }

            return $users;
        }

        public function getUserByEmail($email)
        {
            $sql = "SELECT * FROM users WHERE Email LIKE ? AND Status = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();

            $result = $stmt->get_result();
            return $result->fetch_assoc();
        }

        public function getUserByName($fullName)
        {
            $sql = "SELECT * FROM users WHERE FullName LIKE ? AND Status = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $fullName);
            $stmt->execute();

            $result = $stmt->get_result();
            return $result->fetch_assoc(MYSQLI_ASSOC);
        }

        public function getUserById($UserID)
        {
            $sql = "SELECT * FROM users WHERE UserID = ? AND Status = 1";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $UserID);
            $stmt->execute();

            $result = $stmt->get_result();
            return $result->fetch_assoc(MYSQLI_ASSOC);
        }

        public function createUser($fullName, $email, $password, $phone)
        {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            $sql = "INSERT INTO users (FullName, Email, PasswordHash, Phone)VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssss", $fullName, $email, $hashedPassword, $phone);

            return $stmt->execute();
        }

        public function updateUser($UserID, $fullName, $email, $phone)
        {
            $sql = "UPDATE users SET FullName = ?, Email = ?, Phone = ? WHERE UserID = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssi", $fullName, $email, $phone, $UserID);
            return $stmt->execute();
        }

        public function deleteUser($UserID)
        {
            $sql = "UPDATE users SET Status = 0 WHERE UserID = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $UserID);
            return $stmt->execute();
        }
    }
