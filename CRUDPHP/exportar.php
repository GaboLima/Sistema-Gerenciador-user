<?php
require 'conexao.php';

// Define o nome do arquivo e o tipo
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=usuarios.csv');

// Abre a saída para o navegador
$output = fopen('php://output', 'w');

// Cabeçalho do arquivo CSV
fputcsv($output, ['ID', 'Nome', 'Email', 'Data de Nascimento', 'Status']);

// Busca os dados
$sql = "SELECT id, nome, email, data_nascimento, status FROM usuarios";
$resultado = mysqli_query($conexao, $sql);

// Escreve cada linha no CSV
while ($linha = mysqli_fetch_assoc($resultado)) {
    fputcsv($output, $linha);
}

fclose($output);
exit;
