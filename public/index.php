<?php
require_once '../controllers/SelecaoController.php';

$action = $_GET['action'] ?? 'listar';
$id     = $_GET['id'] ?? null;
$controller = new SelecaoController($db); // ou como você instanciou
$controller->listar(); // É aqui que a mágica começa

// Roteamento Dinâmico: Chama o método se ele existir no controller
if (method_exists($ctrl, $action)) {
    // Para ações que exigem ID (editar/excluir), passamos o parâmetro
    in_array($action, ['editar', 'excluir']) ? $ctrl->$action($id) : $ctrl->$action();
} else {
    $ctrl->listar();
}