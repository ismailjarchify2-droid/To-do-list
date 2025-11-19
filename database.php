<?php
// Connexion à la base de données
function connectDB() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }
}

// Récupérer toutes les tâches triées par date
function getAllTasks() {
    $pdo = connectDB();
    $stmt = $pdo->query("SELECT * FROM todo ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ajouter une nouvelle tâche
function addTask($title) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("INSERT INTO todo (title, done) VALUES (?, 0)");
    return $stmt->execute([trim($title)]);
}

// Basculer l'état d'une tâche
function toggleTask($id) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("UPDATE todo SET done = 1 - done WHERE id = ?");
    return $stmt->execute([$id]);
}

// Supprimer une tâche
function deleteTask($id) {
    $pdo = connectDB();
    $stmt = $pdo->prepare("DELETE FROM todo WHERE id = ?");
    return $stmt->execute([$id]);
}

// Obtenir les statistiques
function getStats() {
    $pdo = connectDB();
    $total = $pdo->query("SELECT COUNT(*) FROM todo")->fetchColumn();
    $completed = $pdo->query("SELECT COUNT(*) FROM todo WHERE done = 1")->fetchColumn();
    return ['total' => $total, 'completed' => $completed];
}
?>