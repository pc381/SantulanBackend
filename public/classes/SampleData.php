<?php
class SampleData {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllData() {
        $stmt = $this->pdo->query("SELECT * FROM sampledata ORDER BY lastmodified DESC");
        return $stmt->fetchAll();
    }

    public function getDataById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM sampledata WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getStatusText($status) {
        return $status ? 'Active' : 'Inactive';
    }
} 