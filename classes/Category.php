<?php

class Category {
    

    public static function getAll($conn) {
        $sql = "SELECT * FROM category ORDER BY name";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}