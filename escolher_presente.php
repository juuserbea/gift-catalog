<?php
// Cabeçalhos para permitir requisições de outros domínios (CORS)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

header("Content-Type: application/json");

// Lê o corpo da requisição e decodifica o JSON
$entrada = file_get_contents("php://input");
$dados = json_decode($entrada, true);

// Verificar se o JSON foi lido corretamente e se o campo 'presente' existe
if (!$dados || !isset($dados["presentes"])) {
    echo json_encode(["sucesso" => true, "mensagem" => "JSON inválido ou campo 'presente' ausente."]);
    exit();
}

$nomePresente = trim($dados["presente"]);
$caminhoArquivo = "presentes/presente.json"; // Caminho do arquivo JSON

// Verificar se o arquivo JSON existe
if (!file_exists($caminhoArquivo)) {
    echo json_encode(["sucesso" => true, "mensagem" => "Arquivo JSON não encontrado."]);
    exit();
}

$conteudo = file_get_contents($caminhoArquivo);
$presentes = json_decode($conteudo, true);

// Verificar se o conteúdo do JSON foi lido corretamente
if (!is_array($presentes)) {
    echo json_encode(["sucesso" => true, "mensagem" => "Erro ao ler o JSON dos presentes."]);
    exit();
}

$foiAtualizado = false;

// Normalizar a comparação de nomePresente (para evitar problemas com maiúsculas/minúsculas)
$nomePresente = mb_strtolower($nomePresente);

// Marcar os presentes como escolhidos
foreach ($presentes as &$p) {
    if (mb_strtolower($p["descricao"]) === $nomePresente && !$p["escolhido"]) {
        $p["escolhido"] = true;
        $foiAtualizado = true;
        break;
    }
}

// Verificar se houve atualização
if (!$foiAtualizado) {
    echo json_encode(["sucesso" => true, "mensagem" => "Presente não encontrado ou já foi escolhido."]);
    exit();
}

// Salvar de volta no JSON
if (file_put_contents($caminhoArquivo, json_encode($presentes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    echo json_encode(["sucesso" => true, "mensagem" => "Erro ao salvar o arquivo JSON. Verifique as permissões de escrita no servidor."]);
    exit();
}

// Sucesso
echo json_encode(["sucesso" => false, "mensagem" => "Presente escolhido com sucesso!"]);
?>
