<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Seleção</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 20px; color: #2c3e50; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; color: white; }
        .btn-update { background: #f39c12; }
        .btn-cancel { background: #95a5a6; margin-left: 10px; }
        .error { color: #e74c3c; background: #fadbd8; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        small { color: #666; font-size: 13px; margin-top: 4px; display: block; }
    </style>
</head>
<body>
    <div class="container">
        <h1>✏️ Editar Seleção</h1>
        
        <?php if(($_GET['msg'] ?? '') == 'erro'): ?>
            <div class="error">❌ Erro ao atualizar. Tente novamente.</div>
        <?php endif; ?>

        <form action="../public/index.php?action=atualizar" method="POST">
            <input type="hidden" name="id_selecao" value="<?= $selecao['id_selecao'] ?>">
            
            <div class="form-group">
                <label>Nome da Seleção:</label>
                <input type="text" name="nome_selecao" value="<?= htmlspecialchars($selecao['nome_selecao']) ?>" required>
            </div>

            <div class="form-group">
                <label>Grupo:</label>
                <select name="grupo_selecao" required>
                    <?php foreach(range('A', 'L') as $g): ?>
                        <option value="<?= $g ?>" <?= $selecao['grupo_selecao'] == $g ? 'selected' : '' ?>>
                            Grupo <?= $g ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small>🎯 Atual: Grupo <?= $selecao['grupo_selecao'] ?></small>
            </div>

            <div class="form-group">
                <label>Títulos Mundiais:</label>
                <input type="number" name="titulos_mundiais" value="<?= $selecao['titulos_mundiais'] ?>" min="0" max="10" required>
                <small>🏆 Atual: <?= $selecao['titulos_mundiais'] ?> título(s)</small>
            </div>
            
            <div style="margin-top: 25px;">
                <button type="submit" class="btn btn-update">💾 Atualizar</button>
                <a href="../public/index.php?action=listar" class="btn btn-cancel">❌ Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>