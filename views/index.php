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

// MAPA DE BANDEIRAS
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

// Gerar partidas
$partidas = [];
foreach($grupos as $g) {
    if(count($dados[$g]) >= 2) {
        $times = $dados[$g];
        $partidas[] = ['time1' => $times[0], 'time2' => $times[1], 'grupo' => $g];
        if(count($times) >= 4) {
            $partidas[] = ['time1' => $times[2], 'time2' => $times[3], 'grupo' => $g];
        }
    }
}
$partidas = array_slice($partidas, 0, 8);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Copa do Mundo</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica';
            padding: 20px;
            background-image: url('img/background-project.svg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            min-height: 100vh;
        }
        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 25px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        h1 { text-align: center; color: white; margin-bottom: 25px; font-size: 36px; text-shadow: 2px 2px 10px rgba(0,0,0,0.3); }
        .botoes {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 15px;
            border-radius: 50px;
            margin-bottom: 25px;
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 40px;
            text-decoration: none;
            font-weight: bold;
            cursor: pointer;
            color: white;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-verde { background: rgba(39, 174, 96, 0.85); }
        .btn-azul { background: rgba(41, 128, 185, 0.85); }
        .btn-laranja { background: rgba(230, 126, 34, 0.85); }
        .btn-vermelho { background: rgba(192, 57, 43, 0.85); }
        .btn-cinza { background: rgba(127, 140, 141, 0.85); }
        
        /* LAYOUT 2 COLUNAS: GRUPOS | PARTIDAS */
        .layout { display: grid; grid-template-columns: 2.5fr 1fr; gap: 20px; }
        
        /* GRUPOS - 4 COLUNAS */
        .grupos-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
        .box {
            border-radius: 20px;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            margin-bottom: 15px;
        }
        .titulo {
            background: rgba(26, 58, 92, 0.7);
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: bold;
            border-bottom: 2px solid rgba(230, 126, 34, 0.8);
        }
        .selecoes { padding: 10px; }
        .selecao {
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(8px);
            padding: 10px;
            margin: 6px 0;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid rgba(230, 126, 34, 0.9);
        }
        .selecao input[type="checkbox"] { width: 16px; height: 16px; accent-color: #e67e22; }
        .bandeira { font-size: 24px; }
        .info { flex: 1; color: #1a1a2e; }
        
        /* PARTIDAS - BLOCO SEPARADO */
        .partidas-bloco {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            height: fit-content;
        }
        .partida-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .partida-times {
            display: flex;
            align-items: center;
            justify-content: space-around;
        }
        .time { text-align: center; }
        .time .bandeira { font-size: 32px; display: block; }
        .time span { color: white; font-weight: bold; font-size: 14px; }
        .vs {
            background: rgba(230, 126, 34, 0.9);
            color: white;
            padding: 8px 15px;
            border-radius: 30px;
            font-weight: bold;
        }
        .partida-info {
            text-align: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,0.3);
            color: white;
            font-size: 12px;
        }
        
        .form-box {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(15px);
            padding: 25px;
            border-radius: 25px;
            margin: 20px 0;
        }
        input, select {
            padding: 12px;
            margin: 5px;
            border-radius: 15px;
            border: 1px solid rgba(255,255,255,0.4);
            background: rgba(255,255,255,0.3);
        }
        .sem { padding: 20px; color: white; text-align: center; }
        
        @media (max-width: 1200px) {
            .layout { grid-template-columns: 1fr; }
            .grupos-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 600px) {
            .grupos-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<!-- LAYOUT: DUAS CAIXAS DO MESMO TAMANHO, CENTRALIZADAS -->
<div style="display: flex; justify-content: center; gap: 20px; max-width: 1200px; margin: 0 auto;">
    
    <!-- CAIXA ESQUERDA: COPA DO MUNDO (GRUPOS) -->
    <div style="flex: 1; max-width: 700px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border-radius: 25px; padding: 20px; border: 1px solid rgba(255, 255, 255, 0.2);">
        <h2 style="color: white; text-align: center; margin-bottom: 15px; text-shadow: 2px 2px 8px rgba(0,0,0,0.3); font-size: 24px;">🏆 Copa do Mundo 2026</h2>
        
        <form method="POST">
            <div style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(5px); padding: 10px; border-radius: 40px; margin-bottom: 20px; display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                <a href="?novo" style="padding: 8px 16px; background: rgba(39, 174, 96, 0.7); color: white; border-radius: 30px; text-decoration: none; font-weight: bold; font-size: 13px; border: 1px solid rgba(255,255,255,0.2);">+ Nova</a>
                <button type="button" onclick="editar()" style="padding: 8px 16px; background: rgba(230, 126, 34, 0.7); color: white; border: none; border-radius: 30px; font-weight: bold; font-size: 13px; cursor: pointer; border: 1px solid rgba(255,255,255,0.2);">Editar</button>
                <button type="submit" name="excluir" onclick="return confirm('Excluir selecionados?')" style="padding: 8px 16px; background: rgba(192, 57, 43, 0.7); color: white; border: none; border-radius: 30px; font-weight: bold; font-size: 13px; cursor: pointer; border: 1px solid rgba(255,255,255,0.2);">Excluir</button>
            </div>
        </form>

        <?php if(isset($_GET['novo'])): ?>
        <div style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(8px); padding: 15px; border-radius: 15px; margin-bottom: 15px;">
            <h4 style="color: white; margin-bottom: 10px;">+ Nova Seleção</h4>
            <form method="POST">
                <input type="text" name="nome" placeholder="Nome" required style="width: calc(50% - 5px);">
                <select name="grupo" required style="width: calc(25% - 5px);">
                    <option value="">Grupo</option>
                    <?php foreach($grupos as $g): ?><option><?=$g?></option><?php endforeach; ?>
                </select>
                <input type="number" name="titulos" value="0" min="0" style="width: calc(25% - 5px);">
                <button type="submit" name="salvar" style="background: rgba(41, 128, 185, 0.7); color: white; border: none; padding: 8px 15px; border-radius: 20px; font-weight: bold;">Salvar</button>
                <a href="index.php" style="background: rgba(127, 140, 141, 0.7); color: white; padding: 8px 15px; border-radius: 20px; text-decoration: none; font-weight: bold;">Cancelar</a>
            </form>
        </div>
        <?php endif; ?>

        <?php if($editar): ?>
        <div style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(8px); padding: 15px; border-radius: 15px; margin-bottom: 15px;">
            <h4 style="color: white; margin-bottom: 10px;">✏️ Editar: <?=$editar['nome_selecao']?></h4>
            <form method="POST">
                <input type="hidden" name="id" value="<?=$editar['id_selecao']?>">
                <input type="text" name="nome" value="<?=$editar['nome_selecao']?>" required style="width: calc(50% - 5px);">
                <select name="grupo" required style="width: calc(25% - 5px);">
                    <?php foreach($grupos as $g): ?>
                    <option <?=$editar['grupo_selecao']==$g?'selected':''?>><?=$g?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="titulos" value="<?=$editar['titulos_mundiais']?>" min="0" style="width: calc(25% - 5px);">
                <button type="submit" name="atualizar" style="background: rgba(230, 126, 34, 0.7); color: white; border: none; padding: 8px 15px; border-radius: 20px; font-weight: bold;">Atualizar</button>
                <a href="index.php" style="background: rgba(127, 140, 141, 0.7); color: white; padding: 8px 15px; border-radius: 20px; text-decoration: none; font-weight: bold;">Cancelar</a>
            </form>
        </div>
        <?php endif; ?>

        <!-- GRUPOS - 4 COLUNAS -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px;">
            <?php foreach([['A','B','C'],['D','E','F'],['G','H','I'],['J','K','L']] as $col): ?>
            <div>
                <?php foreach($col as $g): ?>
                <div style="background: rgba(255, 255, 255, 0.06); backdrop-filter: blur(6px); border-radius: 12px; overflow: hidden; margin-bottom: 8px; border: 1px solid rgba(255, 255, 255, 0.15);">
                    <div style="background: rgba(26, 58, 92, 0.5); color: white; padding: 8px; text-align: center; font-weight: bold; font-size: 13px; border-bottom: 2px solid rgba(230, 126, 34, 0.6);">🏁 GRUPO <?=$g?></div>
                    <div style="padding: 6px;">
                        <?php if(count($dados[$g])): ?>
                            <?php foreach($dados[$g] as $s): ?>
                            <div style="background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(4px); padding: 6px; margin: 4px 0; border-radius: 8px; display: flex; align-items: center; gap: 6px; border-left: 3px solid rgba(230, 126, 34, 0.7);">
                                <input type="checkbox" name="ids[]" value="<?=$s['id_selecao']?>" style="width: 12px; height: 12px; accent-color: #e67e22;">
                                <span style="font-size: 16px;"><?=$bandeiras[$s['nome_selecao']]??'🏳️'?></span>
                                <div style="flex: 1; font-size: 11px; color: #1a1a2e;">
                                    <strong><?=$s['nome_selecao']?></strong>
                                    <?php if($s['titulos_mundiais']>0): ?>🏆<?=$s['titulos_mundiais']?><?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div style="padding: 10px; color: rgba(255,255,255,0.6); text-align: center; font-size: 11px;">Nenhuma</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- CAIXA DIREITA: PRÓXIMAS PARTIDAS (MESMO TAMANHO) -->
    <div style="flex: 1; max-width: 350px; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border-radius: 25px; padding: 20px; border: 1px solid rgba(255, 255, 255, 0.2);">
        <h3 style="color: white; text-align: center; margin-bottom: 15px; text-shadow: 2px 2px 8px rgba(0,0,0,0.3); font-size: 20px;">⚽ PRÓXIMAS PARTIDAS</h3>
        
        <?php if(count($partidas)): ?>
            <?php foreach($partidas as $p): ?>
            <div style="background: rgba(255, 255, 255, 0.06); backdrop-filter: blur(6px); border-radius: 15px; padding: 12px; margin-bottom: 10px; border: 1px solid rgba(255, 255, 255, 0.15);">
                <div style="display: flex; align-items: center; justify-content: space-around;">
                    <div style="text-align: center;">
                        <span style="font-size: 28px; display: block;"><?=$bandeiras[$p['time1']['nome_selecao']]??'🏳️'?></span>
                        <span style="color: white; font-weight: bold; font-size: 11px;"><?=$p['time1']['nome_selecao']?></span>
                    </div>
                    <div style="background: rgba(230, 126, 34, 0.7); color: white; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 12px;">VS</div>
                    <div style="text-align: center;">
                        <span style="font-size: 28px; display: block;"><?=$bandeiras[$p['time2']['nome_selecao']]??'🏳️'?></span>
                        <span style="color: white; font-weight: bold; font-size: 11px;"><?=$p['time2']['nome_selecao']?></span>
                    </div>
                </div>
                <div style="text-align: center; margin-top: 8px; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.15); color: rgba(255,255,255,0.9); font-size: 10px;">
                    📅 Amanhã • 16:00 • Grupo <?=$p['grupo']?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div style="text-align: center; margin-top: 15px;">
                <a href="#" style="background: rgba(255,255,255,0.08); backdrop-filter: blur(5px); padding: 8px 15px; border-radius: 30px; color: white; text-decoration: none; font-weight: bold; font-size: 12px; border: 1px solid rgba(255,255,255,0.15); display: inline-block;">📺 Ver todos</a>
            </div>
        <?php else: ?>
            <div style="padding: 20px; color: rgba(255,255,255,0.7); text-align: center; font-size: 13px;">Cadastre mais seleções!</div>
        <?php endif; ?>
    </div>
    
</div>
</script>
</body>
</html>