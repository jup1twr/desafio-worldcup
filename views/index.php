<?php
// Conexão com o banco
$conn = new PDO("mysql:host=localhost;dbname=copa_db", "root", "alunolab");

// EXCLUIR
if (isset($_POST['excluir']) && isset($_POST['ids'])) {
    foreach ($_POST['ids'] as $id) {
        $conn->query("DELETE FROM selecoes WHERE id_selecao = " . $id);
    }
    header('Location: index.php');
    exit;
}

// SALVAR
if (isset($_POST['salvar'])) {
    $conn->query("INSERT INTO selecoes (nome_selecao, grupo_selecao, titulos_mundiais) 
                  VALUES ('{$_POST['nome']}', '{$_POST['grupo']}', {$_POST['titulos']})");
    header('Location: index.php');
    exit;
}

// ATUALIZAR
if (isset($_POST['atualizar'])) {
    $conn->query("UPDATE selecoes SET 
                  nome_selecao='{$_POST['nome']}', grupo_selecao='{$_POST['grupo']}', titulos_mundiais={$_POST['titulos']} 
                  WHERE id_selecao={$_POST['id']}");
    header('Location: index.php');
    exit;
}

// Carregar para editar
$editar = isset($_GET['editar']) ? $conn->query("SELECT * FROM selecoes WHERE id_selecao=".$_GET['editar'])->fetch() : null;

// MAPA DE BANDEIRAS (EMOJI - Compatível com todos navegadores)
$bandeiras = [
    'Brasil' => '🇧🇷', 'Alemanha' => '🇩🇪', 'Croácia' => '🇭🇷', 'Camarões' => '🇨🇲',
    'França' => '🇫🇷', 'Inglaterra' => '🇬🇧', 'Estados Unidos' => '🇺🇸', 'Gana' => '🇬🇭',
    'Argentina' => '🇦🇷', 'Holanda' => '🇳🇱', 'México' => '🇲🇽', 'Japão' => '🇯🇵',
    'Espanha' => '🇪🇸', 'Portugal' => '🇵🇹', 'Suíça' => '🇨🇭', 'Costa Rica' => '🇨🇷',
    'Itália' => '🇮🇹', 'Bélgica' => '🇧🇪', 'Uruguai' => '🇺🇾', 'Coreia do Sul' => '🇰🇷',
    'Marrocos' => '🇲🇦', 'Senegal' => '🇸🇳', 'Dinamarca' => '🇩🇰', 'Canadá' => '🇨🇦',
    'Sérvia' => '🇷🇸', 'Colômbia' => '🇨🇴', 'Equador' => '🇪🇨', 'Arábia Saudita' => '🇸🇦',
    'Polônia' => '🇵🇱', 'Nigéria' => '🇳🇬', 'Austrália' => '🇦🇺', 'Tunísia' => '🇹🇳',
    'Chile' => '🇨🇱', 'Egito' => '🇪🇬', 'Noruega' => '🇳🇴', 'Peru' => '🇵🇪',
    'Suécia' => '🇸🇪', 'Paraguai' => '🇵🇾', 'Venezuela' => '🇻🇪', 'África do Sul' => '🇿🇦',
    'Turquia' => '🇹🇷', 'Grécia' => '🇬🇷', 'Rússia' => '🇷🇺', 'República Tcheca' => '🇨🇿',
    'Hungria' => '🇭🇺', 'Áustria' => '🇦🇹', 'Escócia' => '🏴󠁧󠁢󠁳󠁣󠁴󠁿', 'Bulgária' => '🇧🇬'
];

// Grupos A-L
$grupos = ['A','B','C','D','E','F','G','H','I','J','K','L'];
$dados = [];
foreach ($grupos as $g) {
    $dados[$g] = $conn->query("SELECT * FROM selecoes WHERE grupo_selecao='$g' ORDER BY nome_selecao")->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Copa do Mundo</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body {font-family: Arial, sans-serif; padding: 20px; background-image: url('img/background-project.png'); background-size: cover; background-attachment: fixed; background-position: center; background-repeat: no-repeat;}
        .container{max-width:1600px;margin:auto;background:rgba(255,255,255,0.95);padding:20px;border-radius:15px;}
        h1{text-align:center;color:#1a3a5c;margin-bottom:20px;}
        .botoes{background:#1a3a5c;padding:15px;border-radius:10px;margin-bottom:20px;display:flex;gap:10px;justify-content:center;flex-wrap:wrap;}
        .btn{padding:10px 20px;border:none;border-radius:5px;text-decoration:none;font-weight:bold;cursor:pointer;color:white;display:inline-block;}
        .btn-verde{background:#27ae60;}.btn-azul{background:#2980b9;}.btn-laranja{background:#e67e22;}.btn-vermelho{background:#c0392b;}.btn-cinza{background:#7f8c8d;}
        .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:15px;}
        .box{background:white;border-radius:10px;overflow:hidden;box-shadow:0 3px 10px rgba(0,0,0,0.1);}
        .titulo{background:#1a3a5c;color:white;padding:12px;text-align:center;border-bottom:3px solid #e67e22;}
        .titulo span{background:#e67e22;padding:3px 10px;border-radius:20px;font-size:13px;margin-left:8px;}
        .selecoes{padding:10px;}
        .selecao{background:#f8f9fa;padding:10px;margin:8px 0;border-radius:8px;display:flex;align-items:center;gap:10px;border-left:4px solid #e67e22;}
        .selecao input[type="checkbox"]{width:18px;height:18px;cursor:pointer;flex-shrink:0;}
        .bandeira{font-size:28px;line-height:1;flex-shrink:0;}
        .info{flex:1;}
        .form-box{background:#f0f4f8;padding:20px;border-radius:10px;margin:20px 0;}
        input,select{padding:10px;margin:5px;border:1px solid #ccc;border-radius:5px;}
        .sem{padding:15px;color:#999;text-align:center;}
        @media(max-width:1200px){.grid{grid-template-columns:repeat(2,1fr);}}
        @media(max-width:600px){.grid{grid-template-columns:1fr;}}
    </style>
</head>
<body>
<div class="container">
    <h1>🏆 Copa do Mundo 2026</h1>
    
    <form method="POST">
        <div class="botoes">
            <a href="?novo" class="btn btn-verde">+ Nova Seleção</a>
            <button type="button" class="btn btn-laranja" onclick="editar()">Editar</button>
            <button type="submit" name="excluir" class="btn btn-vermelho" onclick="return confirm('Excluir selecionados?')">Excluir</button>
        </div>
    </form>

    <?php if(isset($_GET['novo'])): ?>
    <div class="form-box">
        <h3>+ Nova Seleção</h3>
        <form method="POST">
            <input type="text" name="nome" placeholder="Nome da Seleção" required>
            <select name="grupo" required>
                <option value="">Grupo</option>
                <?php foreach($grupos as $g): ?><option><?=$g?></option><?php endforeach; ?>
            </select>
            <input type="number" name="titulos" value="0" min="0" style="width:80px;">
            <button type="submit" name="salvar" class="btn btn-azul">Salvar</button>
            <a href="index.php" class="btn btn-cinza">Cancelar</a>
        </form>
    </div>
    <?php endif; ?>

    <?php if($editar): ?>
    <div class="form-box">
        <h3>✏️ Editar: <?=$editar['nome_selecao']?></h3>
        <form method="POST">
            <input type="hidden" name="id" value="<?=$editar['id_selecao']?>">
            <input type="text" name="nome" value="<?=$editar['nome_selecao']?>" required>
            <select name="grupo" required>
                <?php foreach($grupos as $g): ?>
                <option <?=$editar['grupo_selecao']==$g?'selected':''?>><?=$g?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="titulos" value="<?=$editar['titulos_mundiais']?>" min="0" style="width:80px;">
            <button type="submit" name="atualizar" class="btn btn-laranja">Atualizar</button>
            <a href="index.php" class="btn btn-cinza">Cancelar</a>
        </form>
    </div>
    <?php endif; ?>

    <div class="grid">
        <?php foreach([['A','B','C'],['D','E','F'],['G','H','I'],['J','K','L']] as $col): ?>
        <div>
            <?php foreach($col as $g): ?>
            <div class="box" style="margin-bottom:15px;">
                <div class="titulo">🏁 GRUPO <?=$g?></div>
                <div class="selecoes">
                    <?php if(count($dados[$g])): ?>
                        <?php foreach($dados[$g] as $s): ?>
                        <div class="selecao">
                            <input type="checkbox" name="ids[]" value="<?=$s['id_selecao']?>">
                            <span class="bandeira"><?=$bandeiras[$s['nome_selecao']]??'🏳️'?></span>
                            <div class="info">
                                <strong><?=$s['nome_selecao']?></strong>
                                <?php if($s['titulos_mundiais']>0): ?>
                                    🏆 <?=$s['titulos_mundiais']?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="sem">Nenhuma seleção</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function editar() {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]:checked');
    if (checkboxes.length === 0) {
        alert('Selecione uma seleção para editar!');
    } else if (checkboxes.length > 1) {
        alert('Selecione apenas UMA seleção!');
    } else {
        window.location.href = '?editar=' + checkboxes[0].value;
    }
}
</script>
</body>
</html>