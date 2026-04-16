<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Nova Seleção</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 20px; color: #2c3e50; }
        .container { max-width: 500px; margin: auto; background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; color: white; }
        .btn-save { background: #27ae60; }
        .btn-cancel { background: #95a5a6; margin-left: 10px; }
        .error { color: #e74c3c; background: #fadbd8; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>➕ Nova Seleção</h1>
        
        <?php if(($_GET['msg'] ?? '') == 'erro'): ?>
            <div class="error">❌ Erro ao salvar a seleção.</div>
        <?php endif; ?>

        <form action="../public/index.php?action=salvar" method="POST">
            <div class="form-group">
                <label>Nome da Seleção:</label>
                <input type="text" name="nome_selecao" placeholder="Ex: Brasil" required>
            </div>

            <div class="form-group">
                <label>Grupo:</label>
                <select name="grupo_selecao" required>
                    <option value="">Selecione...</option>
                    <?php foreach(range('A', 'L') as $letra): ?>
                        <option value="<?= $letra ?>">Grupo <?= $letra ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Títulos Mundiais:</label>
                <input type="number" name="titulos_mundiais" value="0" min="0" max="10" required>
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn btn-save">💾 Salvar</button>
                <a href="../public/index.php?action=listar" class="btn btn-cancel">❌ Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>