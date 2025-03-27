<?php
include("conexao.php"); // Conexão com o banco de dados

// Consulta os registros no banco de dados
$sql = "SELECT ID, nome, data, senha FROM seguranca1";
$result = mysqli_query($conexao, $sql);

// Exibe a tabela com os dados
echo "<table class='table'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Data</th>
                <th>Senha</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>" . $row['ID'] . "</td>
            <td>" . $row['nome'] . "</td>
            <td>" . $row['data'] . "</td>
            <td><span class='campo-senha'>" . $row['senha'] . "</span></td>
            <td>
                <button class='btn btn-info btn-descriptografar' data-id='" . $row['ID'] . "'>Descriptografar</button>
                <button class='btn btn-warning btn-criptografar' data-id='" . $row['ID'] . "'>Criptografar</button>
                <button class='btn btn-danger btn-excluir' data-id='" . $row['ID'] . "'>Excluir</button>
            </td>
        </tr>";
}

echo "</tbody></table>";

mysqli_free_result($result);
mysqli_close($conexao);
?>

<!-- CSS para alinhar os botões lado a lado -->
<style>
    .acao-buttons {
        display: flex;
        gap: 10px; /* Espaço entre os botões */
    }

    .acao-buttons .btn {
        flex: 0 1 auto;
    }
</style>

