<?php
    include_once 'database.php';

    class User{
        private $connect;
        private $table_name = "users";

        public $user_id;
        public $nama_lengkap;
        public $email;
        public $username;
        public $password;
        public $role;
        public $photo;
        public $no_hp;

        public function __construct($db)
        {
            $this->connect = $db;
        }

        public function readAll($limit, $offset){
            $query = "SELECT * FROM " . $this->table_name . " LIMIT :limit OFFSET :offset";
            $stmt = $this->connect->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        }
        public function countAll(){
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt = $this->connect->prepare($query);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        }

        public function readOne(){
            $query = "SELECT * FROM " .  $this->table_name . " WHERE user_id = ?";
            $stmt = $this->connect->prepare($query);
            $stmt->bindParam(1, $this->user_id);
            $stmt->execute();
            return $stmt;
        }

        public function create(){
            $query = "INSERT INTO " . $this->table_name . " SET nama_lengkap=:nama_lengkap, email=:email, username=:username, password=:password, role=:role, photo=:photo, no_hp=:no_hp";
            $stmt = $this->connect->prepare($query);
        
            $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);
        
            $stmt->bindParam(":nama_lengkap", $this->nama_lengkap);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":password", $hashedPassword);
            $stmt->bindParam(":role", $this->role);
            $stmt->bindParam(":photo", $this->photo);
            $stmt->bindParam(":no_hp", $this->no_hp);
        
            if ($stmt->execute()) {
                return true;
            }
            return false;
        }
        
        public function update() {
            $query = "UPDATE " . $this->table_name . " SET nama_lengkap = :nama_lengkap, email = :email, username = :username, password = :password, role = :role, photo = :photo, no_hp = :no_hp WHERE user_id = :user_id";
            $stmt = $this->connect->prepare($query);

            $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);

            $stmt->bindParam(":nama_lengkap", $this->nama_lengkap);
            $stmt->bindParam(":email", $this->email);
            $stmt->bindParam(":username", $this->username);
            $stmt->bindParam(":password", $hashedPassword);
            $stmt->bindParam(":role", $this->role);
            $stmt->bindParam(":photo", $this->photo);
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":no_hp", $this->no_hp);

            if($stmt->execute()) {
                return true;
            }
            return false;
        }

        public function delete() {
            $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
            $stmt = $this->connect->prepare($query);
            $stmt->bindParam(1, $this->user_id);

            if($stmt->execute()) {
                return true;
            }
            return false;
        }
    }
?>
