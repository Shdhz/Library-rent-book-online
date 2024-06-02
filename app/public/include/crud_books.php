<?php
    include_once 'database.php';

    class Books{
        private $connect;
        private $table_name = "books";

        public $book_id;
        public $title;
        public $author;
        public $category_id;
        public $isbn;
        public $publish_date;
        public $available_copies;
        public $cover;
        public $publisher;
        public $sinopsis;

        public function __construct($db)
        {
            $this->connect = $db;
        }

        public function readAll($limit, $offset, $search = '%') {
            $query = "SELECT * FROM " . $this->table_name . " WHERE title LIKE :search LIMIT :limit OFFSET :offset";
            $stmt = $this->connect->prepare($query);
            $stmt->bindParam(':search', $search);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        }

        public function readCover() {
            // Pilih hanya kolom yang relevan
            $query = "SELECT book_id, title, cover, author, available_copies FROM " . $this->table_name;
            $stmt = $this->connect->prepare($query);
            $stmt->execute();
            return $stmt;
        }
        

        public function countAll($search = '%') {
            $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE title LIKE :search";
            $stmt = $this->connect->prepare($query);
            $stmt->bindParam(':search', $search);
            $stmt->execute();
            return $stmt->fetchColumn();
        }

        public function readOne(){
            $query = "SELECT * FROM " . $this->table_name . " WHERE book_id = ?";
            $stmt = $this->connect->prepare($query);
            $stmt->bindParam(1, $this->book_id);
            $stmt->execute();
            return $stmt;
        }

        public function getBookDetails($book_id) {
            $query = "SELECT b.*, c.category_name 
                    FROM books b 
                    JOIN categories c ON b.category_id = c.category_id
                    WHERE b.book_id = ?";
            $stmt = $this->connect->prepare($query);
            $stmt->execute([$book_id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }        
        

        public function create(){
            $query = "INSERT INTO " . $this->table_name . " SET title=:title, author=:author, category_id=:category_id, isbn=:isbn, publish_date=:publish_date, available_copies=:available_copies, cover=:cover, publisher=:publisher, sinopsis=:sinopsis";
            $stmt = $this->connect->prepare($query);

            $stmt->bindParam(":title", $this->title);
            $stmt->bindParam(":author", $this->author);
            $stmt->bindParam(":category_id", $this->category_id);
            $stmt->bindParam(":isbn", $this->isbn);
            $stmt->bindParam(":publish_date", $this->publish_date);
            $stmt->bindParam(":available_copies", $this->available_copies);
            $stmt->bindParam(":cover", $this->cover);
            $stmt->bindParam(":publisher", $this->publisher);
            $stmt->bindParam(":sinopsis", $this->sinopsis);

            if ($stmt->execute()){
                return true;
            }
            return false;
        }

        public function update() {
            $query = "UPDATE " . $this->table_name . " 
                    SET isbn = :isbn, title = :title, author = :author, category_id = :category_id, 
                    publisher = :publisher, publish_date = :publish_date, sinopsis = :sinopsis, 
                    available_copies = :available_copies, cover = :cover 
                    WHERE book_id = :book_id";
    
            $stmt = $this->connect->prepare($query);
    
            $stmt->bindParam(':isbn', $this->isbn);
            $stmt->bindParam(':title', $this->title);
            $stmt->bindParam(':author', $this->author);
            $stmt->bindParam(':category_id', $this->category_id);
            $stmt->bindParam(':publisher', $this->publisher);
            $stmt->bindParam(':publish_date', $this->publish_date);
            $stmt->bindParam(':sinopsis', $this->sinopsis);
            $stmt->bindParam(':available_copies', $this->available_copies);
            $stmt->bindParam(':cover', $this->cover);
            $stmt->bindParam(':book_id', $this->book_id);
    
            if ($stmt->execute()) {
                return true;
            }
    
            return false;
        }

        public function delete() {
            $query = "DELETE FROM books WHERE book_id = :book_id";
            $stmt = $this->connect->prepare($query);
            $stmt->bindParam(':book_id', $this->book_id);

            if ($stmt->execute()) {
                return true;
            } else {
                return false;
            }
        }
        public function searchBooks($search, $publisher, $author, $genre) {
            $query = "SELECT books.*, categories.category_name 
                      FROM books 
                      JOIN categories ON books.category_id = categories.category_id 
                      WHERE 1=1";
            
            // Array to hold query parameters
            $params = [];
        
            // Add search condition if search parameter is not empty
            if (!empty($search)) {
                $query .= " AND (books.title LIKE :search OR books.author LIKE :search OR books.isbn LIKE :search)";
                $params[':search'] = "%$search%";
            }
        
            // Add publisher condition if publisher parameter is not empty
            if (!empty($publisher)) {
                $query .= " AND books.publisher = :publisher";
                $params[':publisher'] = $publisher;
            }
        
            // Add author condition if author parameter is not empty
            if (!empty($author)) {
                $query .= " AND books.author = :author";
                $params[':author'] = $author;
            }
        
            // Add genre condition if genre parameter is not empty
            if (!empty($genre)) {
                $query .= " AND books.category_id = :genre";
                $params[':genre'] = $genre;
            }
        
            $query .= " LIMIT :limit";
            $params[':limit'] = 100; // Adjust limit as needed
        
            // Prepare the query
            $stmt = $this->connect->prepare($query);
        
            // Bind the parameters
            foreach ($params as $key => &$val) {
                if ($key == ':limit') {
                    $stmt->bindParam($key, $val, PDO::PARAM_INT);
                } else {
                    $stmt->bindParam($key, $val, PDO::PARAM_STR);
                }
            }
        
            // Execute the query
            $stmt->execute();
            
            return $stmt;
        }
    
        public function getDistinctValues($column) {
            $query = "SELECT DISTINCT $column FROM books";
            $stmt = $this->connect->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
    
        public function getDistinctCategories() {
            $query = "SELECT category_id, category_name FROM categories";
            $stmt = $this->connect->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
?>