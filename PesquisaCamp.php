<?php
include 'config.php';

// Verifica se a competição foi escolhida
if (!isset($_GET['competition']) || empty($_GET['competition'])) {
    die("⚠️ Nenhum campeonato selecionado.");
}

$competition = $_GET['competition']; // ID do campeonato

// Função para buscar os próximos jogos
function ProximosResultados($competition) {
    $url = FOOTBALL_API_URL . "/competitions/$competition/matches?status=SCHEDULED";

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
    return $data['matches'] ?? [];
}

// Função para buscar os últimos resultados
function UltimoResultado($competition) {
    $url = FOOTBALL_API_URL . "/competitions/$competition/matches?status=FINISHED";

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
    return $data['matches'] ?? [];
}

// Buscar jogos futuros e últimos resultados
$matches = ProximosResultados($competition);
$results = UltimoResultado($competition);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogos e Resultados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4 shadow-lg">
            <h1 class="text-center mb-4">📅 Jogos e Resultados</h1>

            <div class="row">
                <!-- Próximos Jogos -->
                <div class="col-md-6">
                    <h3 class="text-center">🔜 Próximos Jogos</h3>
                    <?php if (empty($matches)) { ?>
                        <p class="text-center">⚠️ Nenhum jogo encontrado para este campeonato.</p>
                    <?php } else { ?>
                        <table class="table table-bordered table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>Casa</th>
                                    <th>Fora</th>
                                    <th>Data</th>
                                    <th>Estádio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($matches as $match) { ?>
                                    <tr>
                                        <td><?= $match['homeTeam']['name'] ?></td>
                                        <td><?= $match['awayTeam']['name'] ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($match['utcDate'])) ?></td>
                                        <td><?= $match['venue'] ?? 'Não informado' ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>

                <!-- Últimos Resultados -->
                <div class="col-md-6">
                    <h3 class="text-center">✅ Últimos Resultados</h3>
                    <?php if (empty($results)) { ?>
                        <p class="text-center">⚠️ Nenhum resultado disponível.</p>
                    <?php } else { ?>
                        <table class="table table-bordered table-hover">
                            <thead class="table-success">
                                <tr>
                                    <th>Casa</th>
                                    <th>Placar</th>
                                    <th>Fora</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($results as $match) { ?>
                                    <tr>
                                        <td><?= $match['homeTeam']['name'] ?></td>
                                        <td><?= $match['score']['fullTime']['home'] ?? '?' ?>x<?= $match['score']['fullTime']['away'] ?? '?' ?></td>
                                        <td><?= $match['awayTeam']['name'] ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } ?>
                </div>
            </div>

            <a href="index.php" class="btn btn-secondary mt-3">🔙 Voltar</a>
        </div>
    </div>
</body>
</html>
