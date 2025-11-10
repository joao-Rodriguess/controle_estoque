# 📦 SAEP - Sistema de Controle de Estoque

Um sistema web moderno e funcional para gerenciar estoque, produtos e movimentações de forma simples e eficiente.

## ✨ Características

- ✅ **Autenticação Completa** - Login e cadastro de usuários com senha hash segura
- ✅ **Dashboard Intuitivo** - Visão geral do estoque com estatísticas em tempo real
- ✅ **Gerenciamento de Produtos** - Criar, editar e deletar produtos
- ✅ **Controle de Movimentações** - Registrar entradas e saídas de estoque
- ✅ **Histórico Completo** - Rastreamento de todas as movimentações
- ✅ **Design Responsivo** - Funciona perfeitamente em desktop e mobile
- ✅ **Interface Moderna** - CSS limpo e bem estruturado

## 🚀 Primeiros Passos

### Instalação

1. **Coloque os arquivos no seu servidor web**
   ```
   xampp/htdocs/controle_estoque/saep_estoque/
   ```

2. **Acesse a aplicação**
   ```
   http://localhost/saep_estoque/app.php?action=login
   ```

3. **Crie seu primeiro usuário** 
   - Clique em "Criar Conta"
   - Preencha os dados
   - Faça login

### Conta Padrão (Teste)

Se desejar usar dados de teste, uma conta padrão pode ser criada:
- **Usuário:** admin
- **Senha:** 123456

## 📚 Estrutura do Projeto

```
saep_estoque/
├── app.php                 # Arquivo principal (router)
├── db.php                  # Conexão ao banco de dados e funções
├── saep_db.sql            # Schema do banco de dados
├── requirements.txt       # Dependências
├── templates/
│   ├── login.php          # Página de login
│   ├── cadastro.php       # Página de cadastro
│   ├── dashboard.php      # Dashboard principal
│   ├── produtos.php       # Gerenciamento de produtos
│   ├── movimentacoes.php  # Registro de movimentações
│   ├── historico.php      # Histórico completo
│   ├── perfil.php         # Perfil do usuário
│   └── base.php           # Template base (não usado, para referência)
├── static/
│   ├── css/
│   │   └── style.css      # Estilos da aplicação
│   └── js/
│       └── script.js      # Scripts JavaScript
└── saep_db.sqlite         # Banco de dados SQLite (gerado automaticamente)
```

## 📋 Funcionalidades Detalhadas

### 🔐 Autenticação
- Cadastro de novos usuários
- Login seguro com hash de senha
- Sessões PHP
- Logout

### 📊 Dashboard
- Estatísticas de estoque
- Produtos com estoque baixo
- Últimas movimentações
- Links rápidos para ações principais

### 📦 Produtos
- Criar novo produto (SKU, nome, preço)
- Editar informações do produto
- Deletar produtos (com confirmação)
- Lista com quantidade em estoque
- Indicadores de estoque baixo

### 📥 Movimentações
- Registrar entradas de estoque
- Registrar saídas de estoque
- Adicionar descrição às movimentações
- Atualização automática de quantidade
- Histórico visual

### 📜 Histórico
- Lista completa de todas as movimentações
- Filtros por data
- Visualização de dados e horas
- Exportação de dados

### 👤 Perfil
- Visualizar informações do usuário
- Acesso a opções de segurança
- Links de logout

## 🔧 Tecnologias Utilizadas

- **Backend:** PHP 7.4+
- **Banco de Dados:** SQLite (arquivo local)
- **Frontend:** HTML5, CSS3, JavaScript Vanilla
- **Autenticação:** password_hash() (BCRYPT)
- **Sessões:** PHP Sessions

## 📝 Uso da API (Funções PHP)

### Usuários
```php
criar_usuario($pdo, $username, $password, $nome);
verificar_login($pdo, $username, $password);
usuario_existe($pdo, $username);
```

### Produtos
```php
listar_produtos($pdo);
obter_produto($pdo, $id);
criar_produto($pdo, $sku, $nome, $quantidade, $preco);
atualizar_produto($pdo, $id, $sku, $nome, $preco);
deletar_produto($pdo, $id);
```

### Movimentações
```php
listar_movimentacoes($pdo, $limit = 100);
registrar_movimentacao($pdo, $produto_id, $tipo, $quantidade, $descricao);
```

### Dashboard
```php
obter_estatisticas($pdo); // Retorna array com stats
```

## 🎨 Customização

### Cores
As cores podem ser customizadas no arquivo `static/css/style.css`:

```css
:root {
    --primary: #2c3e50;      /* Azul escuro */
    --secondary: #3498db;    /* Azul claro */
    --success: #27ae60;      /* Verde */
    --warning: #f39c12;      /* Laranja */
    --danger: #e74c3c;       /* Vermelho */
}
```

### Estrutura SQL
O banco de dados é inicializado automaticamente. Para resetar:
1. Delete o arquivo `saep_db.sqlite`
2. Recarregue a página

## 🔒 Segurança

✅ Senhas armazenadas com BCRYPT
✅ Validação de entrada com htmlspecialchars()
✅ Verificação de autenticação em todas as páginas protegidas
✅ Prepared statements para evitar SQL injection
✅ Transações para integridade de dados

## 📞 Suporte

Para relatórios de bugs ou sugestões, entre em contato com o administrador.

## 📄 Licença

SAEP - Sistema de Controle de Estoque © 2025

---

**Versão:** 1.0  
**Última atualização:** 10 de Novembro de 2025
