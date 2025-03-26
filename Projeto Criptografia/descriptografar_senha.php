<?php
include("conexao.php"); // Conexão com o banco de dados

// Função para descriptografar a senha
function descriptografarSenha($senhaCriptografadaBase64) {
    $chave = "minhaChaveSecreta123"; // A mesma chave usada na criptografia
    $dados = base64_decode($senhaCriptografadaBase64); // Decodifica a senha criptografada

    $iv_length = openssl_cipher_iv_length('AES-256-CBC'); // Obtém o tamanho do IV para o método AES-256-CBC

    // Extrai o IV (primeiros bytes) e a senha criptografada (restante)
    $iv = substr($dados, 0, $iv_length);
    $senhaCriptografada = substr($dados, $iv_length);

    // Descriptografa a senha
    return openssl_decrypt($senhaCriptografada, 'AES-256-CBC', $chave, 0, $iv);
}

if (isset($_POST['ID'])) {
    $id = $_POST['ID'];

    // Consulta a senha criptografada no banco de dados
    $sql = "SELECT senha FROM seguranca1 WHERE ID = $id";
    $result = mysqli_query($conexao, $sql);

    if ($row = mysqli_fetch_assoc($result)) {
        $senhaOriginal = descriptografarSenha($row['senha']); // Descriptografa a senha

        echo json_encode(['success' => true, 'senha' => $senhaOriginal]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuário não encontrado.']);
    }

    mysqli_free_result($result);
} else {
    echo json_encode(['success' => false, 'message' => 'ID não fornecido.']);
}

mysqli_close($conexao);
?>
