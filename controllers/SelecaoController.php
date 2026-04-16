<?php
require_once '../config/database.php';
require_once '../models/Selecao.php';

class SelecaoController {
    private $db;
    private $selecao;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->selecao = new Selecao($this->db);
    }

    // Listar todas as seleções
    public function listar() {
        $stmt = $this->selecao->read();
        include '../views/index.php';
    }

    // Mostrar formulário de criação
    public function criar() {
        include '../views/create.php';
    }

    // Salvar nova seleção
    public function salvar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->selecao->nome_selecao = $_POST['nome_selecao'];
            $this->selecao->grupo_selecao = $_POST['grupo_selecao'];
            $this->selecao->titulos_mundiais = $_POST['titulos_mundiais'];

            if ($this->selecao->create()) {
                header('Location: index.php?action=listar&msg=sucesso');
                exit;
            } else {
                header('Location: index.php?action=criar&msg=erro');
                exit;
            }
        }
    }

    // Mostrar formulário de edição
    public function editar($id) {
        if ($this->selecao->readOne($id)) {
            $selecao = [
                'id_selecao' => $this->selecao->id_selecao,
                'nome_selecao' => $this->selecao->nome_selecao,
                'grupo_selecao' => $this->selecao->grupo_selecao,
                'titulos_mundiais' => $this->selecao->titulos_mundiais
            ];
            include '../views/edit.php';
        } else {
            header('Location: index.php?action=listar&msg=nao_encontrado');
            exit;
        }
    }

    // Atualizar seleção
    public function atualizar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id_selecao'];
            $this->selecao->nome_selecao = $_POST['nome_selecao'];
            $this->selecao->grupo_selecao = $_POST['grupo_selecao'];
            $this->selecao->titulos_mundiais = $_POST['titulos_mundiais'];

            if ($this->selecao->update($id)) {
                header('Location: index.php?action=listar&msg=atualizado');
                exit;
            } else {
                header('Location: index.php?action=editar&id=' . $id . '&msg=erro');
                exit;
            }
        }
    }

    // Excluir seleção
    public function excluir($id) {
        if ($this->selecao->delete($id)) {
            header('Location: index.php?action=listar&msg=excluido');
            exit;
        } else {
            header('Location: index.php?action=listar&msg=erro_excluir');
            exit;
        }
    }
}
?>