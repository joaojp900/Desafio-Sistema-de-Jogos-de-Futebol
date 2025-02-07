<?php
include 'config.php';

// Função para buscar ID e escudo do time dentro do campeonato escolhido
function  PegaEscudo($competition, $teamName) {
    $url = FOOTBALL_API_URL . "/competitions/$competition/teams";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Auth-Token: ' . FOOTBALL_API_KEY,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $teams = json_decode($response, true)['teams'];

    foreach ($teams as $team) {
        if (stripos($team['name'], $teamName) !== false) {
            return ['id' => $team['id'], 'crest' => $team['crest']];
        }
    }
    return null;
}

// Função para buscar jogos do time no campeonato
function Pegajgs($competition, $teamId, $filterYear = null) {
    $url = FOOTBALL_API_URL . "/competitions/$competition/matches";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Auth-Token: ' . FOOTBALL_API_KEY,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    // Filtrar jogos do time por ano
    $filteredMatches = array_filter($data['matches'], function ($match) use ($teamId, $filterYear) {
        $matchYear = date("Y", strtotime($match['utcDate']));
        $isTeamMatch = ($match['homeTeam']['id'] == $teamId || $match['awayTeam']['id'] == $teamId);
        
        if ($filterYear) {
            return $isTeamMatch && ($matchYear == $filterYear);
        }
        return $isTeamMatch;
    });

    return array_values($filteredMatches);
}

// Pega os valores do formulário
$competition = $_GET['competition'];
$teamName = $_GET['team'];
$filterYear = isset($_GET['year']) ? $_GET['year'] : null;

// Obtém o ID e a bandeira do time dentro do campeonato selecionado
$teamData =  PegaEscudo($competition, $teamName);

if (!$teamData) {
    die("⚠️ Time não encontrado no campeonato selecionado.");
}

$teamId = $teamData['id'];
$teamCrest = $teamData['crest'];

// Busca os jogos do time no campeonato filtrando pelo ano
$matches = Pegajgs($competition, $teamId, $filterYear);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4 shadow-lg">
            <h1 class="text-center mb-4">⚽ Jogos de <?= htmlspecialchars($teamName) ?></h1>
            <div class="text-center">
                <img src="<?= $teamCrest ?>" alt="Escudo do time" width="100">
            </div>

            <?php if (count($matches) > 0) { ?>
                <ul class="list-group mt-4">
                    <?php foreach ($matches as $match) { ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= $match['homeTeam']['name'] ?> 🆚 <?= $match['awayTeam']['name'] ?></strong>
                                <br>📅 <?= date("d/m/Y H:i", strtotime($match['utcDate'])) ?>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <p class="text-center mt-4">⚠️ Nenhum jogo encontrado para este ano.</p>
            <?php } ?>

            <br>
            <div class="text-center">
                <a href="index.php" class="btn btn-secondary">🔙 Nova Busca</a>
            </div>
        </div>
    </div>
</body>
</html>
