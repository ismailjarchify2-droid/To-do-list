<?php
session_start();
require_once 'config.php';
require_once 'database.php';

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Ajouter une nouvelle tâche
    if (isset($_POST['action']) && $_POST['action'] === 'new' && !empty($_POST['title'])) {
        if (addTask($_POST['title'])) {
            $_SESSION['message'] = "Tâche ajoutée avec succès!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erreur lors de l'ajout de la tâche";
            $_SESSION['message_type'] = "error";
        }
    }
    
    // Basculer l'état d'une tâche
    if (isset($_POST['action']) && $_POST['action'] === 'toggle' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        if (toggleTask($id)) {
            $_SESSION['message'] = "État de la tâche modifié!";
            $_SESSION['message_type'] = "success";
        }
    }
    
    // Supprimer une tâche
    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = (int)$_POST['id'];
        if (deleteTask($id)) {
            $_SESSION['message'] = "Tâche supprimée avec succès!";
            $_SESSION['message_type'] = "success";
        }
    }
    
    // Redirection pour éviter le re-post
    header("Location: ../frontend/index.php");
    exit();
}
?>