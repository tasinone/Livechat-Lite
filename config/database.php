<?php
// config/database.php 
class Database {
    private static $instance = null;
    private $db;
    
    private function __construct() {
        $this->db = new SQLite3(__DIR__ . '/../livechat.db');
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance->db;
    }
    
    public static function getSetting($key, $default = '') {
        $db = self::getInstance();
        $stmt = $db->prepare('SELECT setting_value FROM settings WHERE setting_key = ?');
        $stmt->bindValue(1, $key);
        $result = $stmt->execute();
        $row = $result->fetchArray();
        return $row ? $row['setting_value'] : $default;
    }
    
    public static function setSetting($key, $value) {
        $db = self::getInstance();
        $stmt = $db->prepare('INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)');
        $stmt->bindValue(1, $key);
        $stmt->bindValue(2, $value);
        return $stmt->execute();
    }
}
?>