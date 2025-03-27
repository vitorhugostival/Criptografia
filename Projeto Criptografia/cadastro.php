<?php
include("conexao.php"); // Conexão com o banco de dados
session_start();

// Exibe todos os erros de PHP
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configura a resposta inicial
header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Erro desconhecido'];

// Função para criptografar a senha
function criptografarSenha($senha) {
    $chave = "minhaChaveSecreta123"; // A chave de criptografia (deve ser a mesma usada na descriptografia)
    $metodo = "AES-256-CBC"; // Método de criptografia
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($metodo)); // Gera um IV aleatório
    $senhaCriptografada = openssl_encrypt($senha, $metodo, $chave, 0, $iv); // Criptografa a senha
    
    if ($senhaCriptografada === false) {
        return false; // Se falhar a criptografia
    }
    
    return base64_encode($iv . $senhaCriptografada); // Retorna o IV + a senha criptografada (em base64)
}

// Função para descriptografar a senha
function descriptografarSenha($senhaCriptografadaBase64) {
    $chave = "minhaChaveSecreta123"; // A mesma chave usada na criptografia
    $dados = base64_decode($senhaCriptografadaBase64); // Decodifica a senha criptografada

    if ($dados === false) {
        return false; // Falha na decodificação
    }

    $iv_length = openssl_cipher_iv_length('AES-256-CBC'); // Obtém o tamanho do IV para o método AES-256-CBC

    // Extrai o IV (primeiros bytes) e a senha criptografada (restante)
    $iv = substr($dados, 0, $iv_length);
    $senhaCriptografada = substr($dados, $iv_length);

    // Descriptografa a senha
    return openssl_decrypt($senhaCriptografada, 'AES-256-CBC', $chave, 0, $iv);
}

// Verifica os dados recebidos
if (
    !isset($_POST['nome']) ||
    !isset($_POST['data']) ||
    !isset($_POST['senha'])
) {
    $response['message'] = 'Erro: Todos os campos são obrigatórios!';
    echo json_encode($response);
    exit;
}

// Sanitização dos dados
$nome = trim($_POST['nome']);
$data = trim($_POST['data']);
$senha = trim($_POST['senha']);

// Verifica se o nome já existe
$check_nome_sql = "SELECT nome FROM seguranca1 WHERE nome = ?";
$stmt = mysqli_prepare($conexao, $check_nome_sql);

if (!$stmt) {
    $response['message'] = "Erro ao preparar consulta de verificação de nome: " . mysqli_error($conexao);
    echo json_encode($response);
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $nome);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if (!$resultado) {
    $response['message'] = "Erro ao executar consulta de verificação de nome: " . mysqli_error($conexao);
    echo json_encode($response);
    exit;
}

if (mysqli_num_rows($resultado) > 0) {
    $response['message'] = 'Erro: Este nome já está cadastrado.';
    echo json_encode($response);
    exit;
}

// Criptografa a senha
$senha_criptografada = criptografarSenha($senha);

if ($senha_criptografada === false) {
    $response['message'] = 'Erro ao criptografar a senha.';
    echo json_encode($response);
    exit;
}

// Cadastro do usuário
$sql = "INSERT INTO seguranca1 (nome, data, senha) VALUES (?, ?, ?)";
$stmt = mysqli_prepare($conexao, $sql);

if (!$stmt) {
    $response['message'] = "Erro ao preparar consulta de inserção: " . mysqli_error($conexao);
    echo json_encode($response);
    exit;
}

mysqli_stmt_bind_param($stmt, "sss", $nome, $data, $senha_criptografada);
$resultado = mysqli_stmt_execute($stmt);

if (!$resultado) {
    $response['message'] = "Erro ao executar inserção: " . mysqli_error($conexao);
    echo json_encode($response);
    exit;
}

$response['success'] = true;
$response['message'] = 'Usuário cadastrado com sucesso.';

// Fecha a conexão e envia a resposta
mysqli_close($conexao);
echo json_encode($response);
?>
