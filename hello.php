<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['nome'])) {
  $nome = strip_tags(trim($_POST['nome']));
  $data = date("d/m/Y H:i:s");
  $linha = "$nome - Confirmado em $data\n";
  file_put_contents("confirmados.txt", $linha, FILE_APPEND);
  header("Location: sucesso.html");
  exit();
} else {
  echo "Nome inválido.";
}
?>
