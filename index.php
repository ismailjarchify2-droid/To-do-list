<?php
// Inclure les fonctions back-end
require_once '../backend/config.php';
require_once '../backend/database.php';
require_once '../backend/todo_actions.php';

// Récupérer les données
$taches = getAllTasks();
$stats = getStats();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TodoList Moderne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
        
root {
    --primary: #4361ee;
    --secondary: #4cc9f0;
    --accent: #f72585;
    --light: #f8f9fa;
}

body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 20px 0;
}

.todo-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    backdrop-filter: blur(10px);
    margin-top: 20px;
    overflow: hidden;
}

.todo-header {
    background: var(--primary);
    color: white;
    padding: 25px;
    text-align: center;
}

.todo-header h1 {
    margin: 0;
    font-weight: 600;
}

.add-task-form {
    padding: 25px;
    border-bottom: 1px solid #eee;
}

.task-input {
    border-radius: 50px;
    padding: 15px 25px;
    border: 2px solid #e9ecef;
    transition: all 0.3s;
}

.task-input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
}

.btn-add {
    background: var(--primary);
    border: none;
    border-radius: 50px;
    padding: 15px 30px;
    color: white;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-add:hover {
    background: #3a56d4;
    transform: translateY(-2px);
}

.task-list {
    padding: 0;
}

.task-item {
    display: flex;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #f1f1f1;
    transition: all 0.3s;
}

.task-item:hover {
    background: #f8f9ff;
}

.task-done {
    background-color: #f0fff4;
    border-left: 4px solid #48bb78;
}

.task-undone {
    background-color: #fffaf0;
    border-left: 4px solid #ed8936;
}

.task-content {
    flex-grow: 1;
    margin: 0 15px;
}

.task-title {
    font-size: 1.1em;
    margin: 0;
    font-weight: 500;
}

.task-done .task-title {
    text-decoration: line-through;
    color: #718096;
}

.task-actions {
    display: flex;
    gap: 10px;
}

.btn-toggle {
    background: var(--secondary);
    border: none;
    border-radius: 8px;
    padding: 8px 15px;
    color: white;
    font-size: 0.9em;
    transition: all 0.3s;
}

.btn-delete {
    background: var(--accent);
    border: none;
    border-radius: 8px;
    padding: 8px 15px;
    color: white;
    font-size: 0.9em;
    transition: all 0.3s;
}

.btn-toggle:hover, .btn-delete:hover {
    transform: scale(1.05);
}

.empty-state {
    text-align: center;
    padding: 50px 20px;
    color: #718096;
}

.empty-state i {
    font-size: 3em;
    margin-bottom: 15px;
    color: #cbd5e0;
}

.stats {
    background: #f7fafc;
    padding: 15px 25px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    color: #4a5568;
}

.alert-message {
    margin: 20px;
    border-radius: 10px;
}

/* Responsive */
@media (max-width: 768px) {
    .task-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .task-actions {
        margin-top: 15px;
        width: 100%;
        justify-content: flex-end;
    }
    
    .stats {
        flex-direction: column;
        gap: 10px;
        text-align: center;
    }
}
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="todo-container">
                    <!-- En-tête -->
                    <div class="todo-header">
                        <h1><i class="bi bi-check2-circle me-2"></i>Ma TodoList</h1>
                        <p class="mb-0">Organisez vos tâches efficacement</p>
                    </div>
                    
                    <!-- Messages d'alerte -->
                    <?php if (isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?= $_SESSION['message_type'] === 'error' ? 'danger' : 'success' ?> alert-message" role="alert">
                            <i class="bi bi-<?= $_SESSION['message_type'] === 'error' ? 'exclamation-triangle-fill' : 'check-circle-fill' ?> me-2"></i>
                            <?= $_SESSION['message'] ?>
                        </div>
                        <?php 
                        unset($_SESSION['message']); 
                        unset($_SESSION['message_type']);
                        ?>
                    <?php endif; ?>
                    
                    <!-- Formulaire d'ajout -->
                    <form method="post" action="../backend/todo_actions.php" class="add-task-form">
                        <div class="input-group">
                            <input type="text" class="form-control task-input" name="title" placeholder="Quelle est votre prochaine tâche ?" required>
                            <button type="submit" class="btn btn-add" name="action" value="new">
                                <i class="bi bi-plus-circle me-2"></i>Ajouter
                            </button>
                        </div>
                    </form>
                    
                    <!-- Liste des tâches -->
                    <div class="task-list">
                        <?php if (empty($taches)): ?>
                            <!-- État vide -->
                            <div class="empty-state">
                                <i class="bi bi-clipboard-check"></i>
                                <h4>Aucune tâche pour le moment</h4>
                                <p>Commencez par ajouter votre première tâche !</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($taches as $tache): ?>
                                <div class="task-item <?= $tache['done'] ? 'task-done' : 'task-undone' ?>">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" <?= $tache['done'] ? 'checked' : '' ?> disabled>
                                    </div>
                                    <div class="task-content">
                                        <p class="task-title"><?= htmlspecialchars($tache['title']) ?></p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            Créée le <?= date('d/m/Y à H:i', strtotime($tache['created_at'])) ?>
                                        </small>
                                    </div>
                                    <div class="task-actions">
                                        <form method="post" action="../backend/todo_actions.php" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $tache['id'] ?>">
                                            <button type="submit" class="btn btn-toggle" name="action" value="toggle">
                                                <?php if ($tache['done']): ?>
                                                    <i class="bi bi-arrow-counterclockwise" title="Marquer comme non terminée"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-check-lg" title="Marquer comme terminée"></i>
                                                <?php endif; ?>
                                            </button>
                                        </form>
                                        <form method="post" action="../backend/todo_actions.php" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?')">
                                            <input type="hidden" name="id" value="<?= $tache['id'] ?>">
                                            <button type="submit" class="btn btn-delete" name="action" value="delete">
                                                <i class="bi bi-trash" title="Supprimer"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Statistiques -->
                    <div class="stats">
                        <span>Total: <strong><?= $stats['total'] ?> tâche<?= $stats['total'] > 1 ? 's' : '' ?></strong></span>
                        <span>Terminées: <strong><?= $stats['completed'] ?> tâche<?= $stats['completed'] > 1 ? 's' : '' ?></strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
