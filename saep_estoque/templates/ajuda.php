<?php
$title = 'Ajuda e Documentação - SAEP Estoque';
$base_url = '/saep_estoque';
$usuario_logado = isset($_SESSION['usuario_id']);

if (!$usuario_logado) {
    header('Location: app.php?action=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="static/css/style.css">
    <style>
        body {
            background: url('static/images/mais.gif') no-repeat center center fixed;
            background-size: cover;
        }

        /* Estilos específicos para a página de Ajuda */
        .doc-section { margin-bottom: 3rem; }
        .doc-title { border-bottom: 2px solid #eee; padding-bottom: 0.5rem; margin-bottom: 1.5rem; color: #333; }
        
        /* Estilo dos Passos do Tutorial */
        .step-list { list-style: none; padding: 0; }
        .step-list li { position: relative; padding-left: 2rem; margin-bottom: 0.8rem; color: #555; }
        .step-list li::before { 
            content: "✓"; position: absolute; left: 0; color: #28a745; font-weight: bold; 
        }

        /* Estilo do FAQ (Acordeão) */
        details {
            background: #fff; border: 1px solid #ddd; border-radius: 6px; margin-bottom: 0.8rem; overflow: hidden;
        }
        summary {
            padding: 1rem; cursor: pointer; font-weight: bold; background: #f8f9fa; list-style: none; outline: none;
            display: flex; justify-content: space-between; align-items: center;
        }
        summary::-webkit-details-marker { display: none; } /* Esconde a seta padrão do Chrome */
        summary::after { content: '+'; font-size: 1.2rem; color: #777; }
        details[open] summary::after { content: '-'; }
        
        .faq-answer { padding: 1rem; border-top: 1px solid #eee; color: #555; line-height: 1.6; background: #fff; }
        
        /* Ícones grandes */
        .icon-lg { font-size: 2rem; margin-bottom: 0.5rem; display: block; }
    </style>
</head>
<body>
    <header>
        <div class="container">
             <div class="flex-between">
                <h1><img src="../saep_estoque/static/images/logo.png" alt="Logo SAEP"  style="height: 85px; width:85px; margin-right: 1px; background-color: white; border-radius: 8px;"> 📦Controle de Estoque</h1>
                <div class="header-user">
                    <span>Bem-vindo, <strong><?php echo htmlspecialchars($_SESSION['nome']); ?></strong></span>
                </div>
            </div>

            <nav>
                <a href="../saep_estoque/app.php?action=dashboard">Dashboard</a>
                <a href="../saep_estoque/app.php?action=produtos">Produtos</a>
                <a href="../saep_estoque/app.php?action=movimentacoes">Movimentações</a>
                <a href="../saep_estoque/app.php?action=historico">Histórico</a>
                <a href="../saep_estoque/app.php?action=perfil">Perfil</a>
                <a href="../saep_estoque/app.php?action=ajuda" class="active">Ajuda</a>            
            </nav>
        </div>
    </header>

    <main class="container">
        
        <div style="text-align: center; margin-bottom: 2rem; color: white;">
            <h2>📘 Central de Ajuda e Documentação</h2>
            <p style="color: white;">Aprenda como utilizar todas as ferramentas do sistema SAEP Estoque.</p>
        </div>

        <section class="doc-section" >
            <h3 class="doc-title" style="color:white ;">📖 Manual do Usuário</h3>
            
            <div class="grid grid-2">
                
                <div class="card">
                    <div class="card-header">📦 Gerenciando Produtos</div>
                    <div style="padding: 1rem;">
                        <p><strong>Para cadastrar ou editar itens:</strong></p>
                        <ul class="step-list">
                            <li>Acesse a aba <strong>Produtos</strong> no menu.</li>
                            <li>Preencha o SKU (Código), Nome e Preço.</li>
                            <li>Clique em <strong>Salvar</strong>.</li>
                            <li>Use o botão "Editar" para alterar valores.</li>
                            <li><em>Nota:</em> Alterações ficam salvas no histórico.</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">🔄 Entradas e Saídas</div>
                    <div style="padding: 1rem;">
                        <p><strong>Para registrar movimentação:</strong></p>
                        <ul class="step-list">
                            <li>Vá até a aba <strong>Movimentações</strong>.</li>
                            <li>Selecione o Produto na lista.</li>
                            <li>Escolha: <span class="badge badge-success">Entrada</span> ou <span class="badge badge-warning">Saída</span>.</li>
                            <li>Informe a quantidade e descrição.</li>
                            <li>O estoque atualiza automaticamente.</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">📜 Histórico e Auditoria</div>
                    <div style="padding: 1rem;">
                        <p><strong>Para consultar registros:</strong></p>
                        <ul class="step-list">
                            <li>Acesse a aba <strong>Histórico</strong>.</li>
                            <li>Use os filtros no topo para buscar por:
                                <br> - Nome do Produto
                                <br> - Data Específica
                            </li>
                            <li>Os dados são permanentes para segurança.</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">📊 Dashboard</div>
                    <div style="padding: 1rem;">
                        <p><strong>Visão Geral do Sistema:</strong></p>
                        <ul class="step-list">
                            <li>Acompanhe o <strong>Valor Total</strong> investido.</li>
                            <li>Monitore itens com <strong>Estoque Baixo</strong>.</li>
                            <li>Veja as movimentações recentes.</li>
                            <li>Use essas métricas para planejar compras.</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">👤 Perfil e Segurança</div>
                    <div style="padding: 1rem;">
                        <p><strong>Gerenciando sua conta:</strong></p>
                        <ul class="step-list">
                            <li>Acesse a aba <strong>Perfil</strong> para ver seus dados.</li>
                            <li>Altere sua senha periodicamente.</li>
                            <li>Ao terminar o trabalho, sempre clique em <strong>Sair</strong>.</li>
                            <li>Nunca compartilhe sua senha com outros funcionários.</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">⚡ Dicas de Produtividade</div>
                    <div style="padding: 1rem;">
                        <p><strong>Melhores práticas:</strong></p>
                        <ul class="step-list">
                            <li>Antes de criar um produto, use a <strong>Busca</strong> para evitar duplicatas.</li>
                            <li>Padronize os nomes (ex: "Caneta Azul" vs "Caneta esferográfica azul").</li>
                            <li>Use o campo "Descrição" na movimentação para anotar número de nota fiscal.</li>
                        </ul>
                    </div>
                </div>

            </div>
        </section>

        <section class="doc-section">
            <h3 class="doc-title">❓ Perguntas Frequentes (FAQ)</h3>
            
            <details>
                <summary>Como altero minha senha de acesso?</summary>
                <div class="faq-answer">
                    Vá até o menu <strong>Perfil</strong>. Lá você encontrará a opção para atualizar seus dados cadastrais e redefinir sua senha. É necessário confirmar a senha atual por segurança.
                </div>
            </details>

            <details>
                <summary>Excluí um produto sem querer, posso recuperar?</summary>
                <div class="faq-answer">
                    <strong>Não.</strong> A exclusão de produtos é permanente. Se você excluiu um produto que ainda tinha histórico, os registros do histórico permanecerão, mas o produto não aparecerá mais na lista ativa. Recomenda-se cadastrá-lo novamente com o mesmo SKU.
                </div>
            </details>

            <details>
                <summary>O que acontece se eu tentar dar saída maior que o estoque?</summary>
                <div class="faq-answer">
                    O sistema irá bloquear a operação. Você receberá uma mensagem de erro informando que o saldo é insuficiente. Verifique a quantidade disponível na aba "Produtos" antes de tentar novamente.
                </div>
            </details>

            <details>
                <summary>Onde vejo quem alterou o preço de um produto?</summary>
                <div class="faq-answer">
                    Ao clicar em <strong>Editar</strong> em um produto, role a página para baixo. Haverá uma tabela "Histórico de Alterações de Preço" mostrando a data, o valor antigo, o novo valor e, se configurado, o usuário que realizou a mudança.
                </div>
            </details>
        </section>

    </main>

    <footer>
        <p>&copy; 2025 SAEP - Controle de Estoque. Todos os direitos reservados.</p>
    </footer>

    <script src="/saep_estoque/static/js/script.js"></script>
</body>
</html>