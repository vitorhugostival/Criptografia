document.addEventListener("DOMContentLoaded", function () {
    // Função para exibir mensagens de status
    function mostrarMensagem(mensagem, tipo) {
        const statusMessage = document.getElementById("statusMessage");
        statusMessage.textContent = mensagem;
        statusMessage.className = `alert alert-${tipo}`;
        setTimeout(() => statusMessage.textContent = "", 3000);
    }

    // Função para alternar a exibição da senha
    function adicionarEventosToggleSenha() {
        document.querySelectorAll(".btn-toggle-senha").forEach(botao => {
            botao.addEventListener("click", function () {
                const campoSenha = this.closest("td").querySelector(".campo-senha");
                const senhaCriptografada = campoSenha.getAttribute("data-criptografada");

                if (this.textContent === "Mostrar") {
                    campoSenha.textContent = senhaCriptografada; // Mostra a senha criptografada
                    this.textContent = "Ocultar";
                } else {
                    campoSenha.textContent = "********"; // Oculta a senha
                    this.textContent = "Mostrar";
                }
            });
        });
    }

    // Função para carregar os usuários automaticamente ao abrir a página
    function carregarUsuarios() {
        fetch("pesquisar1.php")
            .then(response => response.text())
            .then(html => {
                const resultadosDiv = document.getElementById("resultados");
                resultadosDiv.innerHTML = ""; // Limpa o conteúdo existente
                resultadosDiv.innerHTML = html; // Adiciona o novo conteúdo
                adicionarEventosBotoes();
                adicionarEventosToggleSenha(); // Adiciona os eventos de alternância de senha
            })
            .catch(() => mostrarMensagem("Erro ao carregar os usuários!", "danger"));
    }

    // Função para adicionar eventos aos botões de criptografia e exclusão
    function adicionarEventosBotoes() {
        document.querySelectorAll(".btn-descriptografar").forEach(botao => {
            botao.addEventListener("click", async function () {
                const ID = this.getAttribute('data-id');
                const campoSenha = this.closest("tr").querySelector(".campo-senha");

                if (!ID) {
                    mostrarMensagem("Erro: ID inválido para descriptografia.", "danger");
                    return;
                }

                try {
                    const response = await fetch("descriptografar_senha.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `ID=${ID}`
                    });
                    const data = await response.json();

                    if (data.success) {
                        campoSenha.textContent = data.senha; // Atualiza a senha descriptografada na tabela
                    } else {
                        mostrarMensagem(data.message, "danger");
                    }
                } catch (error) {
                    mostrarMensagem("Erro ao descriptografar a senha.", "danger");
                }
            });
        });

        document.querySelectorAll(".btn-criptografar").forEach(botao => {
            botao.addEventListener("click", async function () {
                const ID = this.getAttribute('data-id');
                const campoSenha = this.closest("tr").querySelector(".campo-senha");

                if (!ID) {
                    mostrarMensagem("Erro: ID inválido para criptografia.", "danger");
                    return;
                }

                try {
                    const response = await fetch("criptografar_senha.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: `ID=${ID}`
                    });
                    const data = await response.json();

                    if (data.success) {
                        campoSenha.textContent = "********";
                        campoSenha.setAttribute("data-criptografada", "********"); // Atualiza o atributo com a senha oculta
                    } else {
                        mostrarMensagem(data.message, "danger");
                    }
                } catch (error) {
                    mostrarMensagem("Erro ao criptografar a senha.", "danger");
                }
            });
        });

        document.querySelectorAll(".btn-excluir").forEach(botao => {
            botao.addEventListener("click", async function () {
                const ID = this.getAttribute('data-id'); // Obtém o ID do registro
                const linha = this.closest("tr"); // Seleciona a linha correspondente na tabela

                if (!ID) {
                    mostrarMensagem("Erro: ID inválido para exclusão.", "danger");
                    return;
                }

                if (confirm("Tem certeza que deseja excluir este usuário?")) {
                    try {
                        const response = await fetch("excluir.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/x-www-form-urlencoded" },
                            body: `ID=${ID}` // Envia o ID para exclusão
                        });
                        const data = await response.json();

                        if (data.success) {
                            mostrarMensagem(data.message, "success");
                            linha.remove(); // Remove a linha da tabela
                        } else {
                            mostrarMensagem(data.message, "danger");
                        }
                    } catch (error) {
                        mostrarMensagem("Erro ao excluir o usuário.", "danger");
                        console.error("Erro:", error);
                    }
                }
            });
        });
    }

    // Função para enviar os dados do formulário via AJAX
    async function handleForm(event) {
        event.preventDefault();

        const nome = document.getElementById("nome").value.trim();
        const data = document.getElementById("data").value.trim();
        const senha = document.getElementById("senha").value.trim();
        const csrfToken = document.getElementById("csrf_token").value;

        if (!nome || !data || !senha) {
            mostrarMensagem("Por favor, preencha todos os campos.", "danger");
            return;
        }

        const formData = new FormData();
        formData.append("nome", nome);
        formData.append("data", data);
        formData.append("senha", senha);
        formData.append("csrf_token", csrfToken);

        try {
            const response = await fetch("cadastro.php", {
                method: "POST",
                body: formData
            });
            const data = await response.json();

            mostrarMensagem(data.message, data.success ? "success" : "danger");

            if (data.success) {
                document.getElementById("cadastroForm").reset();
                carregarUsuarios(); // Atualiza a lista de usuários
            }
        } catch (error) {
            mostrarMensagem("Erro ao processar o cadastro.", "danger");
            console.error("Erro:", error);
        }
    }

    // Captura o formulário e adiciona o evento de envio
    const form = document.getElementById("cadastroForm");
    if (form) {
        form.addEventListener("submit", handleForm);
    }

    // Chama a função para carregar usuários ao abrir a página
    carregarUsuarios();
});
