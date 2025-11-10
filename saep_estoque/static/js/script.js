// SAEP - Sistema de Controle de Estoque
// Script com funcionalidades gerais do sistema

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 SAEP Estoque iniciado');

    // Fechar alertas automaticamente
    const alertas = document.querySelectorAll('.alert');
    alertas.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.3s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Adicionar listener aos formulários de deleção
    const deleteForms = document.querySelectorAll('form[method="POST"]');
    deleteForms.forEach(form => {
        const input = form.querySelector('input[name="subaction"]');
        if (input && input.value === 'deletar') {
            form.addEventListener('submit', function(e) {
                if (!confirm('Tem certeza que deseja deletar este item? Esta ação não pode ser desfeita.')) {
                    e.preventDefault();
                }
            });
        }
    });

    // Máscara de moeda
    const moedaInputs = document.querySelectorAll('input[id*="preco"], input[name*="preco"]');
    moedaInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    });

    // Formatar números com separador de milhar
    formatarNumeros();

    // Hover effect nas linhas de tabelas
    adicionarHoverTabelas();
});

// Formatar números nas tabelas
function formatarNumeros() {
    const tabelas = document.querySelectorAll('table');
    tabelas.forEach(tabela => {
        const linhas = tabela.querySelectorAll('tbody tr');
        linhas.forEach(linha => {
            const celulas = linha.querySelectorAll('td');
            celulas.forEach(celula => {
                if (celula.textContent.includes('R$')) {
                    const valor = parseFloat(celula.textContent.replace('R$', '').replace(',', '.'));
                    if (!isNaN(valor)) {
                        celula.textContent = 'R$ ' + valor.toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
            });
        });
    });
}

// Adicionar efeito hover nas linhas de tabelas
function adicionarHoverTabelas() {
    const tabelas = document.querySelectorAll('table tbody tr');
    tabelas.forEach(linha => {
        linha.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f0f0f0';
        });
        linha.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
}

// Função para confirmar ações
function confirmar(mensagem = 'Tem certeza?') {
    return confirm(mensagem);
}

// Função para mostrar notificação
function mostrarNotificacao(mensagem, tipo = 'info') {
    const alerta = document.createElement('div');
    alerta.className = `alert alert-${tipo}`;
    alerta.textContent = mensagem;
    
    const main = document.querySelector('main');
    if (main) {
        main.insertBefore(alerta, main.firstChild);
        
        setTimeout(() => {
            alerta.style.transition = 'opacity 0.3s ease';
            alerta.style.opacity = '0';
            setTimeout(() => {
                alerta.remove();
            }, 300);
        }, 5000);
    }
}

// Função para exportar tabela para CSV
function exportarCSV(nomeArquivo = 'exportacao.csv') {
    const tabelas = document.querySelectorAll('table');
    if (tabelas.length === 0) {
        alert('Nenhuma tabela encontrada para exportar');
        return;
    }

    const tabela = tabelas[0];
    let csv = '';

    // Headers
    const headers = Array.from(tabela.querySelectorAll('thead th')).map(th => 
        '"' + th.textContent.trim().replace(/"/g, '""') + '"'
    );
    csv += headers.join(',') + '\n';

    // Dados
    const linhas = tabela.querySelectorAll('tbody tr');
    linhas.forEach(linha => {
        const celulas = Array.from(linha.querySelectorAll('td')).map(td => {
            let texto = td.textContent.trim().replace(/"/g, '""');
            return '"' + texto + '"';
        });
        csv += celulas.join(',') + '\n';
    });

    // Download
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', nomeArquivo);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Função para imprimir tabela
function imprimirTabela() {
    const tabela = document.querySelector('table');
    if (!tabela) {
        alert('Nenhuma tabela encontrada para imprimir');
        return;
    }

    const janela = window.open('', '', 'height=600,width=800');
    janela.document.write('<html><head><title>Impressão</title>');
    janela.document.write('<style>');
    janela.document.write('table { border-collapse: collapse; width: 100%; }');
    janela.document.write('th, td { border: 1px solid black; padding: 8px; text-align: left; }');
    janela.document.write('th { background-color: #f2f2f2; }');
    janela.document.write('</style></head><body>');
    janela.document.write(tabela.outerHTML);
    janela.document.write('</body></html>');
    janela.document.close();
    janela.print();
}
