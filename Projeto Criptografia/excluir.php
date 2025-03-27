<?php
include('conexao.php');

// Configura o cabeçalho da resposta como JSON
header('Content-Type: application/json');

// Verifica se a conexão com o banco de dados está ativa
if (!$conexao) {
    error_log("Erro na conexão com o banco de dados: " . mysqli_connect_error());
    echo json_encode(['success' => false, 'message' => 'Erro na conexão com o banco de dados.']);
    exit;
}

// Verifica se o método da requisição é POST e se o parâmetro 'ID' foi enviado e é válido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ID']) && intval($_POST['ID']) > 0) {
    // Converte o valor recebido para inteiro para evitar SQL Injection
    $ID = intval($_POST['ID']);

    // Prepara o comando SQL para excluir o usuário com base no ID
    $sql = "DELETE FROM seguranca1 WHERE ID = ?";
    $stmt = mysqli_prepare($conexao, $sql); // Prepara a consulta para execução

    if ($stmt) {
        // Associa o valor do ID do usuário ao parâmetro na consulta
        // "i" indica que o parâmetro é um número inteiro
        mysqli_stmt_bind_param($stmt, "i", $ID);

        // Executa a consulta preparada
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Usuário excluído com sucesso.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao excluir o usuário.']);
        }

        // Libera os recursos usados pela consulta preparada
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao preparar a consulta.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
}

// Fecha a conexão com o banco de dados
mysqli_close($conexao);
?>