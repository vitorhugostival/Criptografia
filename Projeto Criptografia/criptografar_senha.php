<?php
include("conexao.php");

// Função para criptografar a senha
function criptografarSenha($senha) {
    $chave = "minhaChaveSecreta123"; // A mesma chave usada na descriptografia
    $iv_length = openssl_cipher_iv_length('AES-256-CBC'); // Obtém o tamanho do IV para o método AES-256-CBC
    $iv = openssl_random_pseudo_bytes($iv_length); // Gera um IV aleatório

    // Criptografa a senha
    $senhaCriptografada = openssl_encrypt($senha, 'AES-256-CBC', $chave, 0, $iv);

    // Codifica a senha criptografada e o IV em base64 para armazenamento
    return base64_encode($iv . $senhaCriptografada);
}

if (isset($_POST['ID'])) {
    $ID = $_POST['ID'];

    // Consulta para obter a senha original
    $sql = "SELECT senha FROM seguranca1 WHERE ID = '$ID'";
    $result = mysqli_query($conexao, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        // Criptografar a senha
        $senhaCriptografada = criptografarSenha($row['senha']);
        // Atualizar a senha no banco
        $update_sql = "UPDATE seguranca1 SET senha = '$senhaCriptografada' WHERE ID = '$ID'";
        mysqli_query($conexao, $update_sql);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido']);
}

mysqli_close($conexao);
?>
