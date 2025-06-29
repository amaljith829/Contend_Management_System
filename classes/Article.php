<?php

class Article {

    public $id;
    public $title;
    public $content;
    public $published_at;
    public $image_file;
    public $errors = [];

    public static function getById($conn, $id, $columns = '*') {

        $sql = "SELECT $columns FROM article WHERE id = :id";
    
        $stmt = $conn->prepare($sql);
    
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Article');
    
        if($stmt->execute()) {
            return $stmt->fetch();
        }
    }

    public static function getWithCategories($conn, $id, $only_published = false) {
        $sql = "SELECT article.*, category.name AS category_name FROM article 
        LEFT JOIN article_category ON article.id = article_category.article_id 
        LEFT JOIN category ON article_category.category_id = category.id 
        WHERE article.id = :id";
        if($only_published) {
            $sql .= " AND article.published_at IS NOT NULL";
        }
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getCategories($conn, $id) {
        $sql = "SELECT category.* FROM category 
        JOIN article_category ON category.id = article_category.category_id
        WHERE article_category.article_id = :id";

        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    


    public function update($conn) {
        if($this->validate()) {
        $sql = "UPDATE article SET title = :title, content = :content, published_at = :published_at WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindValue(":title", $this->title, PDO::PARAM_STR);
        $stmt->bindValue(":content", $this->content, PDO::PARAM_STR);
        if($this->published_at == '') {
            $stmt->bindValue(":published_at", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(":published_at", $this->published_at, PDO::PARAM_STR);
        }
        
        return $stmt->execute();
    }
    else {
        return false;
    }

        
    }

    public function setCategories($conn, $ids) {
        // Remove categories not in $ids
        if ($ids && count($ids) > 0) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "DELETE FROM article_category WHERE article_id = ? AND category_id NOT IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(1, $this->id, PDO::PARAM_INT);
            foreach ($ids as $index => $id) {
                $stmt->bindValue($index + 2, $id, PDO::PARAM_INT);
            }
            $stmt->execute();
        } else {
            // If no categories selected, remove all
            $sql = "DELETE FROM article_category WHERE article_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(1, $this->id, PDO::PARAM_INT);
            $stmt->execute();
        }

        // Insert new categories (ignore duplicates)
        if ($ids && count($ids) > 0) {
            $sql = "INSERT IGNORE INTO article_category (article_id, category_id) VALUES ";
            $values = [];
            foreach ($ids as $id) {
                $values[] = "(?, ?)";
            }
            $sql .= implode(", ", $values);
            $stmt = $conn->prepare($sql);
            $i = 1;
            foreach ($ids as $id) {
                $stmt->bindValue($i++, $this->id, PDO::PARAM_INT);
                $stmt->bindValue($i++, $id, PDO::PARAM_INT);
            }
            $stmt->execute();
        }
        return true;
    }

    protected function validate() {
        $this->errors = []; // Clear previous errors

        if($this->title == '') {
            $this->errors[] = "Title is required";
        }
        if($this->content == '') {
            $this->errors[] = "Content is required";
        }
        // published_at is optional, no need to check for future date here
        if($this->published_at == '') {
            $this->published_at = null;
        }
        return empty($this->errors);
    }

    public function delete($conn) {
        $sql = "DELETE FROM article WHERE id = :id";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
        
        
        return $stmt->execute();
    }

    public function create($conn) {
        if($this->validate()) {
        $sql = "INSERT INTO article (title, content, published_at) VALUES (:title, :content, :published_at)";
        
        $stmt = $conn->prepare($sql);
        
        $stmt->bindValue(":title", $this->title, PDO::PARAM_STR);
        $stmt->bindValue(":content", $this->content, PDO::PARAM_STR);
        if($this->published_at == '') {
            $stmt->bindValue(":published_at", null, PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(":published_at", $this->published_at, PDO::PARAM_STR);
        }
        
        if ($stmt->execute()) {
            $this->id = $conn->lastInsertId();
            return true;
        } else {
            return false;
        }
    }
    else {
        return false;
    }

        
    }


    public static function getAll($conn) {
        $sql = "SELECT * FROM article ORDER BY published_at;";
        $result = $conn->query($sql);
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPage($conn, $limit, $offset, $only_published = false) {
        // First, get the paginated article IDs
        $sql = "SELECT id FROM article";
        if ($only_published) {
            $sql .= " WHERE published_at IS NOT NULL AND published_at <= NOW()";
        }
        $sql .= " ORDER BY published_at LIMIT :limit OFFSET :offset";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $article_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($article_ids)) {
            return [];
        }
        
        // Then, get the articles with their categories
        $placeholders = implode(',', array_fill(0, count($article_ids), '?'));
        $sql = "SELECT article.*, category.name AS category_name FROM article
        LEFT JOIN article_category ON article.id = article_category.article_id 
        LEFT JOIN category ON article_category.category_id = category.id
        WHERE article.id IN ($placeholders)
        ORDER BY article.published_at";
        
        $stmt = $conn->prepare($sql);
        foreach ($article_ids as $index => $id) {
            $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Process the results to group categories
        $articles = [];
        $previous_id = null;
        foreach($result as $row) {
            $article_id = $row['id'];
            if($article_id !== $previous_id) {
                $row['category_names'] = [];
                $articles[$article_id] = $row;
            } 
            if (!empty($row['category_name'])) {
                $articles[$article_id]['category_names'][] = $row['category_name'];
            }
            $previous_id = $article_id;
        }
        return array_values($articles);
    }

    public static function getTotal($conn, $only_published = false) {
        $condition = $only_published ? "WHERE published_at IS NOT NULL AND published_at <= NOW()" : "";
        return $conn->query("SELECT COUNT(*) FROM article $condition")->fetchColumn();
    }

    public function setImageFile($conn, $filename) {
        $sql = "UPDATE article SET image_file = :image_file WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindValue(":image_file", $filename, $filename == null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function publish($conn) {
        $sql = "UPDATE article SET published_at = :published_at WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":id", $this->id, PDO::PARAM_INT);
        $published_at = $this->published_at ?? date("Y-m-d H:i:s");
        $stmt->bindValue(":published_at", $published_at, PDO::PARAM_STR);
        if($stmt->execute()) {
            return $published_at;
        }
    }
}   