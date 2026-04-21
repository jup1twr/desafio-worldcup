<?php
// Conexão com o banco de dados
$conn = new PDO("mysql:host=localhost;dbname=copa_db", "root", "alunolab");
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// CRIAÇÃO AUTOMÁTICA DAS COLUNAS DE ESTATÍSTICAS (Caso não existam)
try {
    $conn->exec("ALTER TABLE selecoes 
        ADD COLUMN pts INT DEFAULT 0, ADD COLUMN pj INT DEFAULT 0, 
        ADD COLUMN vit INT DEFAULT 0, ADD COLUMN emp INT DEFAULT 0, 
        ADD COLUMN der INT DEFAULT 0, ADD COLUMN gm INT DEFAULT 0, 
        ADD COLUMN gc INT DEFAULT 0, ADD COLUMN sg INT DEFAULT 0");
} catch(PDOException $e) { /* Ignora se já existirem */ }

$pagina = $_GET['pagina'] ?? 'home';
$acao = $_GET['acao'] ?? '';

// COOKIES - ACEITAÇÃO
$mostrarCookies = !isset($_COOKIE['cookies_aceitos']);
if (isset($_GET['aceitar_cookies'])) {
    setcookie('cookies_aceitos', '1', time() + (86400 * 30), '/');
    header('Location: index.php'); exit;
}

// AÇÕES CRUD E SIMULAÇÃO
if ($acao == 'excluir' && isset($_GET['id'])) {
    $stmt = $conn->prepare("DELETE FROM selecoes WHERE id_selecao = ?");
    $stmt->execute([$_GET['id']]);
    header('Location: index.php?pagina=selecoes&msg=excluido'); exit;
}

if (isset($_POST['salvar'])) {
    $stmt = $conn->prepare("INSERT INTO selecoes (nome_selecao, grupo_selecao, titulos_mundiais, biografia) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['nome'], $_POST['grupo'], $_POST['titulos'], $_POST['biografia']]);
    header('Location: index.php?pagina=selecoes&msg=salvo'); exit;
}

if (isset($_POST['atualizar'])) {
    $stmt = $conn->prepare("UPDATE selecoes SET nome_selecao=?, grupo_selecao=?, titulos_mundiais=?, biografia=?, pts=?, pj=?, vit=?, emp=?, der=?, gm=?, gc=?, sg=? WHERE id_selecao=?");
    $stmt->execute([
        $_POST['nome'], $_POST['grupo'], $_POST['titulos'], $_POST['biografia'],
        $_POST['pts'], $_POST['pj'], $_POST['vit'], $_POST['emp'], 
        $_POST['der'], $_POST['gm'], $_POST['gc'], $_POST['sg'], 
        $_POST['id']
    ]);
    header('Location: index.php?pagina=selecoes&msg=atualizado'); exit;
}

if ($acao == 'simular' && isset($_POST['time1'], $_POST['time2'])) {
    $t1 = $_POST['time1']; $t2 = $_POST['time2'];
    $g1 = rand(0, 4); $g2 = rand(0, 4);

    $atualizar = function($nome, $gPro, $gContra) use ($conn) {
        $pts = 0; $vit = 0; $emp = 0; $der = 0;
        if ($gPro > $gContra) { $pts = 3; $vit = 1; }
        elseif ($gPro == $gContra) { $pts = 1; $emp = 1; }
        else { $der = 1; }

        $stmt = $conn->prepare("UPDATE selecoes SET pj=pj+1, pts=pts+?, vit=vit+?, emp=emp+?, der=der+?, gm=gm+?, gc=gc+?, sg=sg+(?-?) WHERE nome_selecao=?");
        $stmt->execute([$pts, $vit, $emp, $der, $gPro, $gContra, $gPro, $gContra, $nome]);
    };
    $atualizar($t1, $g1, $g2); $atualizar($t2, $g2, $g1);
    header("Location: index.php?pagina=jogos&msg=simulado&res={$t1} {$g1}x{$g2} {$t2}"); exit;
}

// DADOS DE BANDEIRAS E BIOGRAFIAS ATUALIZADOS PARA 2026
$bandeiras = [
    'Brasil'=>'br','Alemanha'=>'de','Croácia'=>'hr','França'=>'fr','Inglaterra'=>'gb','Argentina'=>'ar',
    'Países Baixos'=>'nl','Espanha'=>'es','Portugal'=>'pt','Uruguai'=>'uy','México'=>'mx','Japão'=>'jp',
    'Estados Unidos'=>'us','Canadá'=>'ca','Bélgica'=>'be','Suíça'=>'ch','Suécia'=>'se','Senegal'=>'sn',
    'Tunísia'=>'tn','Gana'=>'gh','Equador'=>'ec','Colômbia'=>'co','Paraguai'=>'py','Costa Rica'=>'cr',
    'Coreia do Sul'=>'kr','Arábia Saudita'=>'sa','Austrália'=>'au','Egito'=>'eg','Noruega'=>'no','África do Sul'=>'za',
    'Turquia'=>'tr','Escócia'=>'gb-sct','Tchéquia'=>'cz', 'Catar'=>'qa', 'Bósnia e Herzegovina'=>'ba', 
    'Marrocos'=>'ma', 'Haiti'=>'ht', 'Curaçao'=>'cw', 'Costa do Marfim'=>'ci', 'Irã'=>'ir', 'Nova Zelândia'=>'nz',
    'Cabo Verde'=>'cv', 'Iraque'=>'iq', 'Argélia'=>'dz', 'Áustria'=>'at', 'Jordânia'=>'jo', 'Uzbequistão'=>'uz', 
    'RD Congo'=>'cd', 'Panamá'=>'pa'
];

$autoBios = [
    "México" => ["hist" => "Hugo Sánchez, Rafa Márquez, Cuauhtémoc Blanco", "tit" => "0", "part" => "18", "cur" => "1. Primeira seleção a sediar a Copa do Mundo três vezes (1970, 1986, 2026). 2. Conhecida por sempre parar nas Oitavas de Final, a 'Maldição do Quinto Jogo' dura desde 1994."],
    "África do Sul" => ["hist" => "Benni McCarthy, Lucas Radebe", "tit" => "0", "part" => "4", "cur" => "1. País-sede da Copa de 2010, famosa pelo barulho ensurdecedor das 'Vuvuzelas'. 2. Tornou-se a primeira nação anfitriã da história a ser eliminada na Fase de Grupos."],
    "Coreia do Sul" => ["hist" => "Son Heung-min, Park Ji-sung, Cha Bum-kun", "tit" => "0", "part" => "12", "cur" => "1. Alcançou o 4º lugar em 2002 na campanha mais polêmica e heroica do futebol asiático. 2. Ostenta a maior goleada sofrida por uma seleção anfitriã em estreias: 9x0 para a Hungria em 1954."],
    "Tchéquia" => ["hist" => "Pavel Nedvěd, Patrik Schick, Petr Čech", "tit" => "0 (2 como Tchecoslováquia)", "part" => "10", "cur" => "1. Herdeira da tradição da Tchecoslováquia, que foi vice-campeã em 1934 e 1962. 2. Patrik Schick marcou o gol mais bonito da Euro 2020 e é a esperança de gols para a primeira Copa com o novo nome."],
    "Canadá" => ["hist" => "Alphonso Davies, Jonathan David", "tit" => "0", "part" => "3", "cur" => "1. País-sede que ainda busca seu primeiro gol e sua primeira vitória em Copas do Mundo (perdeu todas em 1986 e 2022). 2. Alphonso Davies é o primeiro jogador canadense a marcar um gol em Copa."],
    "Catar" => ["hist" => "Akram Afif, Hassan Al-Haydos", "tit" => "0", "part" => "2", "cur" => "1. Sede da Copa de 2022, entrou para a história como o primeiro país do Oriente Médio a sediar o torneio. 2. Apesar do investimento bilionário, perdeu todos os 3 jogos na edição passada."],
    "Suíça" => ["hist" => "Granit Xhaka, Xherdan Shaqiri, Stéphane Chapuisat", "tit" => "0", "part" => "13", "cur" => "1. Especialista em zebras: Eliminou a campeã França nas oitavas da Euro 2020 e segurou o Brasil na Copa de 2018. 2. A seleção suíça detém o recorde de minutos sem sofrer gols em Copas: 559 minutos entre 2006 e 2010."],
    "Bósnia e Herzegovina" => ["hist" => "Edin Džeko, Miralem Pjanić", "tit" => "0", "part" => "2", "cur" => "1. Estreou em Copas em 2014, onde Edin Džeko marcou o primeiro gol da história do país no torneio. 2. É uma das nações mais jovens do futebol mundial, tendo se tornado independente apenas em 1992."],
    "Brasil" => ["hist" => "Pelé, Ronaldo, Romário, Zico, Garrincha", "tit" => "5 (1958, 1962, 1970, 1994, 2002)", "part" => "23", "cur" => "1. Única seleção a participar de TODAS as 23 edições da história das Copas. 2. Maior campeão mundial e detentor do recorde de vitórias e gols marcados na história da competição."],
    "Marrocos" => ["hist" => "Hakim Ziyech, Achraf Hakimi, Mustapha Hadji", "tit" => "0", "part" => "7", "cur" => "1. Em 2022 tornou-se a primeira seleção africana e árabe a chegar às Semifinais de uma Copa do Mundo. 2. Em 1986 foi o primeiro time africano a vencer um grupo, ficando à frente de Portugal e Inglaterra."],
    "Haiti" => ["hist" => "Emmanuel Sanon, Duckens Nazon", "tit" => "0", "part" => "2", "cur" => "1. Emmanuel Sanon marcou o gol que quebrou a invencibilidade do goleiro Dino Zoff (Itália) em 1974, que durava 1143 minutos. 2. Retorna à Copa após 52 anos de espera."],
    "Escócia" => ["hist" => "Kenny Dalglish, Denis Law, Andrew Robertson", "tit" => "0", "part" => "9", "cur" => "1. Famoso 'Exército de Tartan', os torcedores escoceses são considerados os mais animados e amigáveis do mundo. 2. Nunca passou da Fase de Grupos em 8 participações, sendo a seleção que mais participou sem nunca ir ao mata-mata."],
    "Estados Unidos" => ["hist" => "Landon Donovan, Clint Dempsey, Christian Pulisic", "tit" => "0", "part" => "12", "cur" => "1. Melhor campanha foi o 3º lugar na primeira Copa de 1930. 2. Venceu a Inglaterra por 1x0 na Copa de 1950, resultado considerado um dos maiores 'Milagres' da história das Copas."],
    "Paraguai" => ["hist" => "Roque Santa Cruz, José Luis Chilavert, Carlos Gamarra", "tit" => "0", "part" => "9", "cur" => "1. Conhecida pela 'Raça Guarani', chegou às Quartas de Final em 2010 quase eliminando a campeã Espanha. 2. José Luis Chilavert é o goleiro com mais gols na história do futebol e cobrava faltas na seleção."],
    "Austrália" => ["hist" => "Tim Cahill, Mark Viduka, Harry Kewell", "tit" => "0", "part" => "7", "cur" => "1. Mudou da Confederação da Oceania para a Ásia para enfrentar adversários mais fortes e se classificar com mais frequência. 2. O maior ídolo, Tim Cahill, marcou gols em 3 Copas diferentes (2006, 2010, 2014)."],
    "Turquia" => ["hist" => "Hakan Şükür, Arda Turan, Hakan Çalhanoğlu", "tit" => "0", "part" => "3", "cur" => "1. Alcançou o inédito 3º lugar em 2002, na Copa sediada por Japão e Coreia do Sul. 2. Hakan Şükür marcou o gol mais rápido da história das Copas: 11 segundos contra a Coreia do Sul em 2002."],
    "Alemanha" => ["hist" => "Franz Beckenbauer, Gerd Müller, Lothar Matthäus, Miroslav Klose", "tit" => "4 (1954, 1974, 1990, 2014)", "part" => "21", "cur" => "1. Miroslav Klose é o maior artilheiro da história das Copas com 16 gols. 2. Foi eliminada na Fase de Grupos em 2018 e 2022, algo impensável para a tetracampeã."],
    "Curaçao" => ["hist" => "Juninho Bacuna, Leandro Bacuna", "tit" => "0", "part" => "1", "cur" => "1. Estreante em Copas, é uma ilha caribenha de apenas 150 mil habitantes (população menor que muitos bairros de São Paulo). 2. Conta com jogadores que atuam principalmente na Holanda devido aos laços históricos com o Reino dos Países Baixos."],
    "Costa do Marfim" => ["hist" => "Didier Drogba, Yaya Touré, Sébastien Haller", "tit" => "0", "part" => "4", "cur" => "1. Conquistou a Copa Africana de Nações de 2024 em casa, liderada por Haller (que superou um câncer). 2. Na Copa de 2014, Didier Drogba parou uma guerra civil no país ao fazer um apelo pela paz em rede nacional."],
    "Equador" => ["hist" => "Agustín Delgado, Antonio Valencia, Moisés Caicedo", "tit" => "0", "part" => "5", "cur" => "1. Joga na altitude de Quito (2.850m), um dos estádios mais temidos das Eliminatórias Sul-Americanas. 2. Alcançou as Oitavas de Final pela primeira vez em 2006 com uma geração histórica."],
    "Países Baixos" => ["hist" => "Johan Cruyff, Marco van Basten, Ruud Gullit, Dennis Bergkamp", "tit" => "0", "part" => "12", "cur" => "1. A 'Laranja Mecânica' é a eterna vice-campeã, tendo perdido 3 finais (1974, 1978, 2010) sem nunca ter vencido. 2. Inventora do 'Futebol Total' nos anos 70, revolucionou a tática do esporte."],
    "Japão" => ["hist" => "Hidetoshi Nakata, Shinji Kagawa, Keisuke Honda, Shunsuke Nakamura", "tit" => "0", "part" => "8", "cur" => "1. Os 'Samurais Azuis' venceram duas ex-campeãs mundiais na fase de grupos de 2022: Alemanha e Espanha. 2. A torcida japonesa é famosa mundialmente por limpar o estádio após os jogos."],
    "Tunísia" => ["hist" => "Wahbi Khazri, Radhi Jaïdi", "tit" => "0", "part" => "7", "cur" => "1. As 'Águias de Cartago' conquistaram a primeira vitória da África em Copas do Mundo, vencendo o México por 3x1 em 1978. 2. Wahbi Khazri é o maior artilheiro tunisiano em Copas com 3 gols."],
    "Suécia" => ["hist" => "Zlatan Ibrahimović, Gunnar Nordahl, Tomas Brolin", "tit" => "0", "part" => "13", "cur" => "1. Foi vice-campeã em 1958 jogando em casa, perdendo a final para um garoto de 17 anos chamado Pelé. 2. Eliminou a Itália na repescagem de 2018, deixando a Azzurra fora da Copa pela primeira vez em 60 anos."],
    "Bélgica" => ["hist" => "Eden Hazard, Kevin De Bruyne, Paul Van Himst", "tit" => "0", "part" => "15", "cur" => "1. A 'Geração Belga' alcançou seu auge com o 3º lugar na Copa de 2018. 2. Na Copa de 1982, venceram a Argentina de Maradona na estreia."],
    "Egito" => ["hist" => "Mohamed Salah, Aboutrika", "tit" => "0", "part" => "4", "cur" => "1. Os 'Faraós' nunca venceram uma partida de Copa do Mundo em 4 participações (2 empates e 6 derrotas). 2. O Egito foi a primeira seleção africana a disputar uma Copa, em 1934."],
    "Irã" => ["hist" => "Ali Daei, Mehdi Mahdavikia, Ali Karimi", "tit" => "0", "part" => "7", "cur" => "1. Ali Daei é o maior artilheiro da história do futebol de seleções (109 gols). 2. A primeira vitória em Copas só veio em 1998, justamente contra o rival político Estados Unidos, em jogo histórico chamado de 'A Mãe de todas as Partidas'."],
    "Nova Zelândia" => ["hist" => "Wynton Rufer, Chris Wood, Ryan Nelsen", "tit" => "0", "part" => "3", "cur" => "1. Única seleção a sair INVICTA da Copa de 2010, empatando todos os 3 jogos e ficando à frente da campeã Itália no grupo. 2. Dominam a Oceania, mas só se classificam quando a vaga direta está disponível."],
    "Espanha" => ["hist" => "Xavi, Iniesta, Raúl, Iker Casillas, David Villa", "tit" => "1 (2010)", "part" => "17", "cur" => "1. A 'Fúria' quebrou o estigma de 'amarelona' ao vencer Copa de 2010, Euro 2008 e Euro 2012 consecutivamente. 2. Na Copa de 2022 aplicou a maior goleada de sua história: 7x0 na Costa Rica."],
    "Cabo Verde" => ["hist" => "Ryan Mendes, Bebé", "tit" => "0", "part" => "1", "cur" => "1. Os 'Tubarões Azuis' são uma das grandes surpresas africanas, eliminando gigantes do continente para chegar à primeira Copa. 2. O país é um arquipélago de 10 ilhas com menos de 600 mil habitantes."],
    "Arábia Saudita" => ["hist" => "Majed Abdullah, Sami Al-Jaber, Salem Al-Dawsari", "tit" => "0", "part" => "7", "cur" => "1. Protagonizou uma das maiores zebras da história ao vencer a Argentina de Messi na estreia da Copa de 2022. 2. O goleiro Al-Owairan fez um gol antológico contra a Bélgica em 1994, driblando 5 jogadores."],
    "Uruguai" => ["hist" => "Luis Suárez, Diego Forlán, Enzo Francescoli, Obdulio Varela", "tit" => "2 (1930, 1950)", "part" => "15", "cur" => "1. Campeão da Primeira Copa do Mundo, sediada em Montevidéu. 2. Protagonizou o 'Maracanazo' em 1950, calando 200 mil pessoas no Rio de Janeiro para vencer o Brasil em casa."],
    "França" => ["hist" => "Zinedine Zidane, Michel Platini, Thierry Henry, Kylian Mbappé", "tit" => "2 (1998, 2018)", "part" => "17", "cur" => "1. Apenas a terceira seleção a ser bicampeã consecutivamente no século XXI (1998 e 2018... e vice em 2022). 2. Just Fontaine detém o recorde de gols em uma única edição: 13 gols em 1958."],
    "Senegal" => ["hist" => "El Hadji Diouf, Sadio Mané, Kalidou Koulibaly", "tit" => "0", "part" => "4", "cur" => "1. Em sua estreia em Copas (2002), venceu a campeã França na abertura e chegou às Quartas de Final. 2. Foi eliminada na Fase de Grupos em 2018 por ter mais cartões amarelos que o Japão (critério de Fair Play)."],
    "Noruega" => ["hist" => "Erling Haaland, Ole Gunnar Solskjær, John Carew", "tit" => "0", "part" => "4", "cur" => "1. Na Copa de 1998, venceu o Brasil por 2x1 na Fase de Grupos com um gol de bicicleta de Flo. 2. Retorna à Copa com Haaland, o 'Androide', quebrando um jejum de mais de 20 anos."],
    "Iraque" => ["hist" => "Ahmed Radhi, Younis Mahmoud", "tit" => "0", "part" => "2", "cur" => "1. Conquistou a Copa da Ásia de 2007 em meio à guerra civil, unindo o país em uma das histórias mais emocionantes do futebol. 2. Marcou seu único gol em Copas contra a Bélgica em 1986."],
    "Argentina" => ["hist" => "Diego Maradona, Lionel Messi, Mario Kempes, Alfredo Di Stéfano", "tit" => "3 (1978, 1986, 2022)", "part" => "19", "cur" => "1. Campeã da 'Melhor Final de Todos os Tempos' contra a França em 2022. 2. Dono do 'Gol do Século' e da 'Mão de Deus', ambos feitos por Maradona contra a Inglaterra em 1986."],
    "Argélia" => ["hist" => "Riyad Mahrez, Rabah Madjer, Lakhdar Belloumi", "tit" => "0", "part" => "5", "cur" => "1. As 'Raposas do Deserto' foram garfadas no 'Jogo da Vergonha' em 1982, quando Alemanha e Áustria fizeram um pacto de não agressão para eliminar os argelinos. 2. Levou a campeã Alemanha à prorrogação nas Oitavas de 2014."],
    "Áustria" => ["hist" => "Hans Krankl, David Alaba, Toni Polster", "tit" => "0", "part" => "8", "cur" => "1. O 'Wunderteam' (Time Maravilha) dos anos 30 era considerado o melhor do mundo antes da Segunda Guerra. 2. Melhor campanha foi o 3º lugar em 1954."],
    "Jordânia" => ["hist" => "Mousa Al-Tamari, Amer Shafi", "tit" => "0", "part" => "1", "cur" => "1. Estreante absoluta em Copas, conseguiu a vaga na repescagem intercontinental. 2. Mousa Al-Tamari é o primeiro jogador jordaniano a atuar em uma grande liga europeia (Ligue 1 da França)."],
    "Portugal" => ["hist" => "Cristiano Ronaldo, Eusébio, Luís Figo, Rui Costa", "tit" => "0", "part" => "9", "cur" => "1. 3º lugar em 1966 com Eusébio sendo artilheiro (9 gols) e considerado um dos maiores jogadores da história. 2. Cristiano Ronaldo é o único jogador a marcar gols em 5 Copas diferentes."],
    "Uzbequistão" => ["hist" => "Maksim Shatskikh, Server Djeparov, Eldor Shomurodov", "tit" => "0", "part" => "1", "cur" => "1. Estreante em Copas, é a maior potência da Ásia Central. 2. Shatskikh é o maior artilheiro da história da seleção e fez fama no Dínamo de Kiev."],
    "Colômbia" => ["hist" => "Carlos Valderrama, James Rodríguez, Radamel Falcao, René Higuita", "tit" => "0", "part" => "7", "cur" => "1. James Rodríguez foi artilheiro da Copa de 2014 com um golaço de voleio contra o Uruguai. 2. Andrés Escobar foi assassinado na Colômbia dias após fazer um gol contra que eliminou a seleção em 1994."],
    "RD Congo" => ["hist" => "Chancel Mbemba, Dieumerci Mbokani", "tit" => "0", "part" => "2", "cur" => "1. Estreou em 1974 como 'Zaire', sofrendo 14 gols e protagonizando a cena bizarra de um jogador chutando a bola para longe em uma barreira. 2. Conhecidos como 'Leopardos', são a maior potência física da África Central."],
    "Inglaterra" => ["hist" => "Bobby Charlton, Bobby Moore, David Beckham, Wayne Rooney, Harry Kane", "tit" => "1 (1966)", "part" => "17", "cur" => "1. Campeã em casa com o 'Gol do Século' (ou não), a bola na trave de Hurst contra a Alemanha. 2. Harry Kane foi artilheiro da Copa de 2018 com 6 gols."],
    "Croácia" => ["hist" => "Luka Modrić, Davor Šuker, Zvonimir Boban", "tit" => "0", "part" => "7", "cur" => "1. País com apenas 4 milhões de habitantes, foi vice em 2018 e 3º em 1998 e 2022. 2. O uniforme quadriculado vermelho e branco é um dos mais icônicos do mundo."],
    "Gana" => ["hist" => "Abedi Pelé, Asamoah Gyan, Michael Essien, Mohammed Kudus", "tit" => "0", "part" => "5", "cur" => "1. Em 2010, Suárez (Uruguai) salvou uma bola com a mão no último minuto. Gyan perdeu o pênalti e Gana perdeu a chance de ser a 1ª semi-finalista africana. 2. Os 'Black Stars' carregam uma estrela negra na bandeira em homenagem ao pan-africanismo."],
    "Panamá" => ["hist" => "Luis Tejada, Blas Pérez, Adalberto Carrasquilla", "tit" => "0", "part" => "2", "cur" => "1. Estreou em 2018 e, apesar da goleada sofrida para a Inglaterra, a torcida e o país comemoraram o primeiro gol em Copas como um título mundial. 2. O futebol cresceu após os EUA devolverem o controle do Canal do Panamá."]
];

$grupos = ['A','B','C','D','E','F','G','H','I','J','K','L'];
$dados = [];
foreach ($grupos as $g) {
    $dados[$g] = $conn->query("SELECT * FROM selecoes WHERE grupo_selecao='$g' ORDER BY pts DESC, sg DESC, gm DESC, nome_selecao")->fetchAll();
}

$total = $conn->query("SELECT COUNT(*) FROM selecoes")->fetchColumn();
$grupos_ativos = count(array_filter($dados, fn($g) => count($g) > 0));

$jogos = [];
foreach ($grupos as $g) {
    $times = $dados[$g];
    $qtd = count($times);
    if ($qtd >= 2) $jogos[] = [$times[0]['nome_selecao'], $times[1]['nome_selecao'], $g];
    if ($qtd >= 4) $jogos[] = [$times[2]['nome_selecao'], $times[3]['nome_selecao'], $g];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Copa 2026</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        :root {
            --verde-bandeira: #009B3A; --amarelo-ouro: #FEDD00; --azul-royal: #002776;
            --white: #fff; --shadow: 0 8px 32px rgba(0,0,0,0.3); --radius: 16px;
            --border-light: rgba(254, 221, 0, 0.2); --glass-bg: rgba(0, 39, 118, 0.65);
            --glass-dark: rgba(0, 0, 0, 0.4);
        }
        body { font-family: 'Inter', sans-serif; min-height: 100vh; color: var(--white); display: flex; flex-direction: column; background: linear-gradient(135deg, var(--azul-royal) 0%, #004d40 40%, var(--verde-bandeira) 80%, var(--amarelo-ouro) 100%); overflow-x: hidden; }
        body::after { content: ''; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); width: min(1200px, 120vw); height: min(1200px, 120vh); background: url('../img/logo_copa.png') center/contain no-repeat; opacity: 0.12; z-index: 1010; pointer-events: none; filter: drop-shadow(0 0 50px rgba(254, 221, 0, 0.5)); }
        .content { flex: 1; position: relative; z-index: 1000; max-width: 1400px; margin: 0 auto; width: 100%; padding: 24px; padding-bottom: 80px; }
        
        .top-bar { position: relative; display: flex; align-items: center; margin-bottom: 30px; min-height: 50px; }
        .btn-home { background: var(--amarelo-ouro); color: var(--azul-royal); padding: 12px 24px; border-radius: 40px; text-decoration: none; font-weight: 800; box-shadow: 0 4px 20px rgba(0,0,0,0.3); transition: .2s; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: none; cursor: pointer; font-size: 15px; position: relative; z-index: 10; }
        .btn-home:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(254, 221, 0, 0.4); background: #fff; color: var(--azul-royal); }
        .title-brand { position: absolute; left: 50%; transform: translateX(-50%); font-size: 32px; font-weight: 800; color: var(--amarelo-ouro); text-shadow: 0 2px 10px rgba(0,0,0,0.5); display: flex; align-items: center; gap: 12px; width: 100%; justify-content: center; z-index: 1; pointer-events: none;}
        
        .stats { display: flex; justify-content: center; gap: 24px; flex-wrap: wrap; margin-bottom: 40px; }
        .stat { background: var(--glass-bg); backdrop-filter: blur(12px); padding: 24px 40px; border-radius: var(--radius); text-align: center; min-width: 180px; border: 1px solid var(--border-light); box-shadow: var(--shadow); position: relative; z-index: 2; }
        .stat span { font-size: 44px; font-weight: 800; color: var(--amarelo-ouro); display: block; }
        .stat small { font-size: 14px; opacity: .9; text-transform: uppercase; letter-spacing: 1px; }
        
        .home-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 32px; margin-top: 20px; position: relative; z-index: 1000; }
        .card { background: var(--glass-bg); backdrop-filter: blur(12px); padding: 48px 32px; border-radius: 24px; text-align: center; text-decoration: none; color: #fff; transition: .3s; border: 1px solid var(--border-light); box-shadow: var(--shadow); display: flex; flex-direction: column; justify-content: center;}
        .card:hover { transform: translateY(-8px); border-color: var(--amarelo-ouro); background: rgba(0, 39, 118, 0.85); box-shadow: 0 12px 40px rgba(254, 221, 0, 0.3); }
        .card i { font-size: 56px; margin-bottom: 24px; color: var(--amarelo-ouro); }
        .card h2 { font-size: 26px; margin-bottom: 12px; }
        .card p { opacity: .8; font-size: 16px; }
        
        .grupos-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 24px; position: relative; z-index: 1000;}
        .grupo-card { background: var(--glass-bg); backdrop-filter: blur(12px); border-radius: var(--radius); border: 1px solid var(--border-light); overflow: hidden; box-shadow: var(--shadow); }
        .grupo-card h3 { background: var(--glass-dark); padding: 16px; text-align: center; font-size: 18px; font-weight: 800; color: var(--amarelo-ouro); border-bottom: 1px solid var(--border-light); }
        
        .table-standings { width: 100%; border-collapse: collapse; }
        .table-standings th, .table-standings td { padding: 12px 8px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.05); font-size: 14px; }
        .table-standings th { font-size: 12px; color: var(--amarelo-ouro); font-weight: 800; background: rgba(0,0,0,0.2); }
        .table-standings tr:hover td { background: rgba(254, 221, 0, 0.05); }
        .table-standings tr:last-child td { border-bottom: none; }
        .team-name { display: flex; align-items: center; gap: 8px; font-weight: 600; text-align: left; }
        .team-name img { width: 24px; height: 16px; border-radius: 3px; object-fit: cover; box-shadow: 0 2px 4px rgba(0,0,0,0.4); }
        .pts-col { font-weight: 800; color: var(--amarelo-ouro); }
        
        .actions { display: flex; gap: 4px; justify-content: center; }
        .btn-icon { width: 28px; height: 28px; border-radius: 6px; background: rgba(255,255,255,0.1); color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; border: none; cursor: pointer; transition: .2s; font-size: 12px;}
        .btn-icon:hover { background: var(--amarelo-ouro); color: var(--azul-royal); }
        .btn-icon.bio-btn { color: var(--amarelo-ouro); background: rgba(254, 221, 0, 0.15); }
        .btn-icon.bio-btn:hover { background: var(--amarelo-ouro); color: var(--azul-royal); transform: scale(1.1); }
        .btn-icon.del-btn { color: #ff4757; background: rgba(255,71,87,0.15); }
        .btn-icon.del-btn:hover { background: #ff4757; color: #fff; }

        .vs { color: var(--amarelo-ouro); font-size: 14px; font-weight: 800; background: var(--glass-dark); padding: 6px 12px; border-radius: 8px; }

        .form-box { background: var(--glass-bg); backdrop-filter: blur(15px); padding: 40px; border-radius: 24px; max-width: 600px; margin: 40px auto; border: 1px solid var(--border-light); box-shadow: var(--shadow); position: relative; z-index: 1000;}
        .form-box input, .form-box select, .form-box textarea { width: 100%; padding: 12px; margin-bottom: 16px; border-radius: 8px; border: 1px solid var(--border-light); background: var(--glass-dark); color: #fff; font-family: 'Inter', sans-serif; font-size: 14px; outline: none; transition: .3s; }
        .form-box input:focus, .form-box select:focus, .form-box textarea:focus { border-color: var(--amarelo-ouro); background: rgba(0,0,0,0.6); }
        .form-box button { width: 100%; padding: 16px; background: var(--amarelo-ouro); border: none; border-radius: 12px; color: var(--azul-royal); font-weight: 800; font-size: 16px; cursor: pointer; transition: .2s; text-transform: uppercase; margin-top: 10px; }
        .form-box button:hover { background: #fff; box-shadow: 0 5px 15px rgba(254, 221, 0, 0.4); transform: translateY(-2px); }
        .form-box label { font-size: 12px; font-weight: 600; color: var(--amarelo-ouro); margin-bottom: 4px; display: block;}
        
        .grid-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 10px; }

        .alert { background: rgba(46, 213, 115, 0.2); color: #2ed573; padding: 16px; border-radius: 12px; margin-bottom: 24px; border: 1px solid #2ed573; font-weight: 600; text-align: center; position: relative; z-index: 1000; }
        .alert-sim { background: rgba(254, 221, 0, 0.2); color: var(--amarelo-ouro); border-color: var(--amarelo-ouro); }

        .btn-simular { width: max-content !important; padding: 10px 24px !important; font-size: 14px !important; margin: 0 auto; border-radius: 20px !important; }

        .cookie-banner { position: fixed; bottom: 0; left: 0; width: 100%; background: var(--azul-royal); color: #fff; padding: 20px; display: flex; justify-content: center; align-items: center; gap: 20px; z-index: 9999; box-shadow: 0 -4px 10px rgba(0,0,0,0.3); border-top: 2px solid var(--amarelo-ouro); flex-wrap: wrap; text-align: center; }
        .cookie-banner p { font-size: 14px; margin: 0; }

        .footer { text-align: center; padding: 24px; margin-top: auto; border-top: 1px solid rgba(255,255,255,0.1); font-size: 14px; position: relative; z-index: 1000; background: var(--glass-dark); backdrop-filter: blur(5px); }
        .footer a { color: var(--amarelo-ouro); text-decoration: none; font-weight: bold; }
        .footer a:hover { text-decoration: underline; }

        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 2000; display: flex; align-items: center; justify-content: center; }
        .modal-content { background: linear-gradient(135deg, var(--azul-royal), var(--verde-bandeira)); border: 2px solid var(--amarelo-ouro); border-radius: 24px; width: 90%; max-width: 600px; padding: 32px; position: relative; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
        .modal-close { position: absolute; top: 20px; right: 20px; text-decoration: none; color: #fff; font-size: 24px; opacity: .7; transition: .2s; }
        .modal-close:hover { opacity: 1; color: var(--amarelo-ouro); }
        .modal-header { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 20px; }
        .modal-header img { width: 70px; height: 50px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.4); object-fit: cover; }
        .modal-title h2 { font-size: 26px; color: var(--amarelo-ouro); margin-bottom: 4px; }
        .modal-bio-text { font-size: 15px; line-height: 1.6; color: #f0f0f0; max-height: 400px; overflow-y: auto; padding-right: 10px; }
        .bio-topic { margin-bottom: 12px; background: rgba(0,0,0,0.2); padding: 10px; border-radius: 8px; border-left: 3px solid var(--amarelo-ouro); }
        .bio-topic b { color: var(--amarelo-ouro); display: inline-flex; align-items: center; gap: 6px; }
        .badge { background: var(--amarelo-ouro); color: var(--azul-royal); padding: 3px 8px; border-radius: 20px; font-size: 12px; font-weight: 800; display: inline-flex; align-items: center; gap: 4px; }

        @media(max-width: 768px){ .grupos-grid { grid-template-columns: 1fr; } .table-standings th, .table-standings td { padding: 8px 4px; font-size: 12px;} .team-name { font-size: 13px; } .grid-stats { grid-template-columns: repeat(2, 1fr); } }
        
    </style>
</head>
<body>

<?php if ($mostrarCookies): ?>
<div class="cookie-banner">
    <p><i class="fas fa-cookie-bite"></i> Utilizamos cookies para garantir a melhor experiência no nosso site. Ao continuar navegando, você concorda com a nossa política.</p>
    <a href="?aceitar_cookies=1" class="btn-home" style="padding: 8px 20px; font-size: 13px;">Entendi e Aceito</a>
</div>
<?php endif; ?>

<div class="content">
    <div class="top-bar">
        <?php if($pagina != 'home'): ?>
            <a href="index.php?pagina=home" class="btn-home"><i class="fas fa-arrow-left"></i> Home</a>
        <?php endif; ?>
        <div class="title-brand">
            <i class="fas fa-trophy" style="color: var(--amarelo-ouro)"></i> COPA 2026
        </div>
    </div>

    <?php if($pagina != 'adicionar' && $pagina != 'editar'): ?>
    <div class="stats">
        <div class="stat"><span><?=$total?></span><small>Seleções</small></div>
        <div class="stat"><span><?=$grupos_ativos?>/12</span><small>Grupos</small></div>
    </div>
    <?php endif; ?>

    <?php if(isset($_GET['msg'])): ?>
        <?php if($_GET['msg'] == 'simulado'): ?>
            <div class="alert alert-sim"><i class="fas fa-futbol"></i> Jogo Simulado: <b><?=$_GET['res']?></b> - Tabela Atualizada!</div>
        <?php else: ?>
            <div class="alert"><i class="fas fa-check-circle"></i> <?=['salvo'=>'Seleção cadastrada com sucesso!','atualizado'=>'Dados atualizados com sucesso!','excluido'=>'Seleção removida.'][$_GET['msg']]??''?></div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if($pagina=='home'): ?>
        <div class="home-grid">
            <a href="?pagina=selecoes" class="card"><i class="fas fa-list-ol"></i><h2>Grupos e Tabela</h2><p>Classificação e estatísticas</p></a>
            <a href="?pagina=jogos" class="card"><i class="fas fa-gamepad"></i><h2>Próximos Jogos</h2><p>Simular partidas da fase de grupos</p></a>
            <a href="?pagina=adicionar" class="card"><i class="fas fa-plus-circle"></i><h2>Nova Seleção</h2><p>Cadastre times no sistema</p></a>
        </div>

    <?php elseif($pagina=='selecoes'): $modo=isset($_GET['modo']); ?>
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 24px; position: relative; z-index: 1000;">
            <a href="?pagina=selecoes" class="btn-home" style="<?=!$modo ? 'background: #fff; color: var(--azul-royal);' : 'background: transparent; color: #fff;'?>"><i class="fas fa-eye"></i> Visualizar</a>
            <a href="?pagina=selecoes&modo=gerenciar" class="btn-home" style="<?=$modo ? 'background: #fff; color: var(--azul-royal);' : 'background: transparent; color: #fff;'?>"><i class="fas fa-pen"></i> Editar/Excluir</a>
        </div>
        
        <div class="grupos-grid">
            <?php foreach($grupos as $g): if(count($dados[$g]) > 0 || $modo): ?>
            <div class="grupo-card">
                <h3>Grupo <?=$g?></h3>
                <div style="overflow-x:auto;">
                    <table class="table-standings">
                        <thead>
                            <tr>
                                <th style="text-align:left; padding-left:12px;">Seleção</th>
                                <th>Pts</th><th>PJ</th><th>VIT</th><th>E</th><th>DER</th><th>GM</th><th>GC</th><th>SG</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($dados[$g] as $s): 
                                $bandeira = "https://flagcdn.com/" . ($bandeiras[$s['nome_selecao']] ?? 'un') . ".svg";
                                $linkModo = $modo ? '&modo=gerenciar' : '';
                            ?>
                            <tr>
                                <td class="team-name" style="padding-left:12px;">
                                    <img src="<?=$bandeira?>" onerror="this.src='https://flagcdn.com/un.svg'">
                                    <?=$s['nome_selecao']?>
                                </td>
                                <td class="pts-col"><?=$s['pts']?></td>
                                <td><?=$s['pj']?></td><td><?=$s['vit']?></td><td><?=$s['emp']?></td><td><?=$s['der']?></td><td><?=$s['gm']?></td><td><?=$s['gc']?></td><td><?=$s['sg']?></td>
                                <td class="actions">
                                    <a href="?pagina=selecoes<?=$linkModo?>&ver_bio=<?=$s['id_selecao']?>" class="btn-icon bio-btn" title="Ver Biografia"><i class="fas fa-book-open"></i></a>
                                    <?php if($modo): ?>
                                        <a href="?pagina=editar&id=<?=$s['id_selecao']?>" class="btn-icon" title="Editar"><i class="fas fa-pen"></i></a>
                                        <a href="?pagina=selecoes&modo=gerenciar&confirmar_excluir=<?=$s['id_selecao']?>&nome_excluir=<?=urlencode($s['nome_selecao'])?>" class="btn-icon del-btn" title="Excluir"><i class="fas fa-trash"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php if(count($dados[$g]) == 0): ?>
                        <div style="padding: 20px; text-align: center; opacity: 0.5; font-size: 14px;">Nenhuma seleção cadastrada no grupo.</div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; endforeach; ?>
        </div>

        <?php if(isset($_GET['confirmar_excluir'])): ?>
        <div class="modal-overlay">
            <div class="modal-content" style="text-align: center; max-width: 400px;">
                <a href="?pagina=selecoes&modo=gerenciar" class="modal-close"><i class="fas fa-times"></i></a>
                <h3 style="color: var(--amarelo-ouro); margin-bottom: 20px; font-size: 24px;"><i class="fas fa-exclamation-triangle"></i> Atenção</h3>
                <p>Tem certeza que deseja excluir a seleção <b><?=htmlspecialchars($_GET['nome_excluir'])?></b>?</p>
                <div style="margin-top: 24px; display: flex; gap: 12px; justify-content: center;">
                    <a href="?pagina=selecoes&modo=gerenciar" class="btn-home" style="background: rgba(255,255,255,0.1); color: #fff;">Cancelar</a>
                    <a href="?pagina=selecoes&acao=excluir&id=<?=$_GET['confirmar_excluir']?>" class="btn-home" style="background: #ff4757; color: #fff;">Sim, Excluir</a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if(isset($_GET['ver_bio'])): 
            $stmt = $conn->prepare("SELECT * FROM selecoes WHERE id_selecao = ?");
            $stmt->execute([$_GET['ver_bio']]);
            $sel_bio = $stmt->fetch();
            
            if($sel_bio):
                $nome_sel = $sel_bio['nome_selecao'];
                $titulos_sel = $sel_bio['titulos_mundiais'];
                $bio_db = $sel_bio['biografia'];
                $bandeira_sel = "https://flagcdn.com/" . ($bandeiras[$nome_sel] ?? 'un') . ".svg";
                $linkVoltar = "?pagina=selecoes" . (isset($_GET['modo']) ? "&modo=gerenciar" : "");
        ?>
        <div class="modal-overlay">
            <div class="modal-content">
                <a href="<?=$linkVoltar?>" class="modal-close"><i class="fas fa-times"></i></a>
                <div class="modal-header">
                    <img src="<?=$bandeira_sel?>" onerror="this.src='https://flagcdn.com/un.svg'">
                    <div class="modal-title">
                        <h2><?=$nome_sel?></h2>
                        <?php if($titulos_sel > 0): ?>
                            <span class="badge"><i class="fas fa-trophy"></i> <?=$titulos_sel?> Títulos Mundiais</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="modal-bio-text">
                    <?php 
                        // Prioridade para biografia do banco de dados, caso contrário usa o array $autoBios
                        if (!empty(trim($bio_db))) {
                            echo nl2br(htmlspecialchars($bio_db));
                        } else {
                            if (isset($autoBios[$nome_sel])) {
                                $auto = $autoBios[$nome_sel];
                                echo "<div class='bio-topic'><b><i class='fas fa-star'></i> Melhores da História:</b> {$auto['hist']}</div>";
                                echo "<div class='bio-topic'><b><i class='fas fa-trophy'></i> Títulos na Copa:</b> {$auto['tit']}</div>";
                                echo "<div class='bio-topic'><b><i class='fas fa-futbol'></i> Participações na Copa:</b> {$auto['part']}</div>";
                                echo "<div class='bio-topic'><b><i class='fas fa-lightbulb'></i> Curiosidades:</b><br>{$auto['cur']}</div>";
                                echo "<div class='bio-topic'><b><i class='fas fa-user-tie'></i> Técnico Atual:</b> {$auto['tec']}</div>";
                            } else {
                                echo "<p style='color: #fff'>Nenhuma biografia disponível para esta seleção.</p>";
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
        <?php endif; endif; ?>

    <?php elseif($pagina=='jogos'): ?>
        <h2 style="text-align:center; color:var(--amarelo-ouro); margin-bottom: 24px; position:relative; z-index:1000;"><i class="fas fa-calendar-alt"></i> Fase de Grupos - Simulação</h2>
        <?php if(empty($jogos)): ?>
            <div class="alert" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.3); color:#fff;"><i class="fas fa-info-circle"></i> Cadastre pelo menos 2 seleções no mesmo grupo.</div>
        <?php else: ?>
            <div class="home-grid">
                <?php foreach($jogos as $j): 
                    $b1 = "https://flagcdn.com/" . ($bandeiras[$j[0]] ?? 'un') . ".svg";
                    $b2 = "https://flagcdn.com/" . ($bandeiras[$j[1]] ?? 'un') . ".svg";
                ?>
                <div class="card" style="padding: 30px 20px;">
                    <h3 style="margin-bottom: 15px; font-size: 16px; color: var(--amarelo-ouro); opacity: 0.8;">Grupo <?=$j[2]?></h3>
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 20px;">
                        <div style="width: 40%; text-align:center;">
                            <img src="<?=$b1?>" style="width:60px; height:40px; border-radius:6px; margin-bottom:10px; object-fit:cover;">
                            <div style="font-weight:600; font-size: 15px;"><?=$j[0]?></div>
                        </div>
                        <div class="vs">VS</div>
                        <div style="width: 40%; text-align:center;">
                            <img src="<?=$b2?>" style="width:60px; height:40px; border-radius:6px; margin-bottom:10px; object-fit:cover;">
                            <div style="font-weight:600; font-size: 15px;"><?=$j[1]?></div>
                        </div>
                    </div>
                    <form method="POST" action="?pagina=jogos&acao=simular">
                        <input type="hidden" name="time1" value="<?=$j[0]?>">
                        <input type="hidden" name="time2" value="<?=$j[1]?>">
                        <button type="submit" class="btn-home btn-simular"><i class="fas fa-play"></i> Simular Resultado</button>
                    </form>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php elseif($pagina=='adicionar'): ?>
        <div class="form-box">
            <h2 style="margin-bottom:24px; text-align:center; color:var(--amarelo-ouro)"><i class="fas fa-flag"></i> Cadastrar Seleção</h2>
            <form method="POST">
                <label>Nome da Seleção</label><input name="nome" placeholder="Ex: Brasil" required>
                <label>Grupo na Copa</label>
                <select name="grupo" required><option value="" disabled selected>Selecione...</option><?php foreach($grupos as $g): ?><option value="<?=$g?>">Grupo <?=$g?></option><?php endforeach; ?></select>
                <label>Títulos Mundiais</label><input name="titulos" value="0" min="0" type="number">
                <label>Biografia (Opcional - substitui a automática)</label><textarea name="biografia" rows="4" placeholder="Deixe em branco para usar a biografia oficial do sistema."></textarea>
                <button name="salvar"><i class="fas fa-save"></i> Salvar</button>
            </form>
        </div>

    <?php elseif($pagina=='editar'): $e = $conn->query("SELECT * FROM selecoes WHERE id_selecao=".(int)$_GET['id'])->fetch(); ?>
        <div class="form-box">
            <h2 style="margin-bottom:24px; text-align:center; color:var(--amarelo-ouro)"><i class="fas fa-edit"></i> Editar: <?=$e['nome_selecao']?></h2>
            <form method="POST">
                <input type="hidden" name="id" value="<?=$e['id_selecao']?>">
                <label>Nome</label><input name="nome" value="<?=$e['nome_selecao']?>" required>
                <label>Grupo</label>
                <select name="grupo" required><?php foreach($grupos as $g): ?><option value="<?=$g?>" <?=$e['grupo_selecao']==$g?'selected':''?>>Grupo <?=$g?></option><?php endforeach; ?></select>
                <label>Títulos</label><input name="titulos" value="<?=$e['titulos_mundiais']?>" type="number">
                <hr style="border:0; border-top:1px solid rgba(255,255,255,0.1); margin: 20px 0;">
                <div class="grid-stats">
                    <div><label>Pts</label><input type="number" name="pts" value="<?=$e['pts']?>"></div>
                    <div><label>PJ</label><input type="number" name="pj" value="<?=$e['pj']?>"></div>
                    <div><label>Vit</label><input type="number" name="vit" value="<?=$e['vit']?>"></div>
                    <div><label>Emp</label><input type="number" name="emp" value="<?=$e['emp']?>"></div>
                    <div><label>Der</label><input type="number" name="der" value="<?=$e['der']?>"></div>
                    <div><label>GM</label><input type="number" name="gm" value="<?=$e['gm']?>"></div>
                    <div><label>GC</label><input type="number" name="gc" value="<?=$e['gc']?>"></div>
                    <div><label>SG</label><input type="number" name="sg" value="<?=$e['sg']?>"></div>
                </div>
                <label>Biografia (Opcional)</label><textarea name="biografia" rows="4"><?=htmlspecialchars($e['biografia'] ?? '')?></textarea>
                <button name="atualizar"><i class="fas fa-sync-alt"></i> Salvar</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<footer class="footer">
    <p>© 2026 Copa do Mundo - Todos os direitos reservados | <a href="https://github.com/jup1twr/desafio-worldcup" target="_blank"><i class="fab fa-github"></i> jup1twr/desafio-worldcup</a></p>
</footer>

</body>
</html>