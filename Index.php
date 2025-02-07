<?php
include 'config.php';

// Função para buscar campeonatos
function getLeagues() {
    $url = FOOTBALL_API_URL . "/competitions";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Auth-Token: ' . FOOTBALL_API_KEY,
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true)['competitions'];
}

// Buscar lista de campeonatos
$leagues = getLeagues();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Busca de Jogos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4 shadow-lg">
            <h1 class="text-center mb-4">🔍 Buscar Campeonatos</h1>
            <form action="PesquisaCamp.php" method="GET">
                <div class="mb-3">
                    <label for="competition" class="form-label">Escolha um Campeonato:</label>
                    <select name="competition" id="competition" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($leagues as $league) { ?>
                            <option value="<?= $league['code'] ?>"><?= $league['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-100">Buscar Jogos</button>
            </form>
        </div>
    </div>
<!----------------------------------------------------Linha de Corte---------------------------------------------------->

    <div class="container mt-5">
        <div class="card p-4 shadow-lg">
            <h1 class="text-center mb-4">🔍 Buscar Times</h1>
            <form action="Pesquisa.php" method="GET">
                <div class="mb-3">
                    <label for="competition" class="form-label">Escolha um Campeonato:</label>
                    <select name="competition" id="competition" class="form-select" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($leagues as $league) { ?>
                            <option value="<?= $league['code'] ?>"><?= $league['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="team" class="form-label">Digite o Nome do Time:</label>
                    <input type="text" name="team" id="team" class="form-control" placeholder="Ex: Flamengo" required>
                </div>

                 

                <button type="submit" class="btn btn-primary w-100">Buscar Jogos</button>
            </form>
        </div>
    </div>
</body>
</html>
