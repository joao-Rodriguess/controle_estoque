# 📦 SAEP - Sistema de Controle de Estoque

Um **sistema web moderno, robusto e totalmente funcional** para gerenciar estoque, produtos e movimentações com autenticação segura, rastreamento de operações e interface intuitiva.

---

## ✨ Características Principais

- ✅ **Autenticação Segura** - Login e cadastro com hash BCRYPT
- ✅ **Recuperação de Desconexão** - Retry automático em caso de perda de conexão com DB
- ✅ **Dashboard em Tempo Real** - Estatísticas e alertas de produtos com baixo estoque
- ✅ **CRUD Completo de Produtos** - Criar, editar, deletar com rastreamento
- ✅ **Controle de Movimentações** - Entradas/saídas automáticas com atualização de qtd
- ✅ **Histórico Auditável** - Registro completo de todas as operações
- ✅ **Perfil de Usuário** - Gerenciamento de dados pessoais
- ✅ **Design Totalmente Responsivo** - Funciona perfeitamente em desktop, tablet e mobile
- ✅ **Interface Moderna** - CSS com variáveis de tema, animações e transições suaves
- ✅ **JavaScript Utilities** - Validações, formatações e ações avançadas no cliente
- ✅ **MySQL/MariaDB Integrado** - Suporte a banco de dados robusto
- ✅ **Zero Dependências Externas** - Apenas PHP, sem Composer

---

## 🚀 Quick Start (Início Rápido)

### 1️⃣ Instalação Básica

```bash
# Copie a pasta saep_estoque para:
C:\xampp\htdocs\controle_estoque\saep_estoque\

# Acesse no navegador:
http://localhost/saep_estoque/app.php?action=login
```

### 2️⃣ Primeiros Usuários

**Opção A: Via Interface Web**
```
1. Clique em "Criar Conta"
2. Preenchaa os dados
3. Faça login
```

**Opção B: Via Script (seeds)**
```bash
# Acesse no navegador:
http://localhost/saep_estoque/seed_users.php
# Cria: admin (123456) e user1 (senha123)
```

### 3️⃣ Carregar Dados de Exemplo

```bash
# Acesse:
http://localhost/saep_estoque/import_seed.php
# Importa 5 produtos e movimentações de teste
```

### 4️⃣ Verificar Sistema

```bash
# Acesse o diagnóstico:
http://localhost/saep_estoque/diagnostico.php
# Mostra status de conexão e número de registros
```

---

## 📁 Estrutura Completa do Projeto

```
saep_estoque/
│
├── 🔵 ARQUIVOS PRINCIPAIS
│   ├── app.php ........................... Router principal (MVC simples)
│   ├── db.php ............................ Conexão + Funções DB + Retry logic
│   ├── index.php ......................... Redireciona para login
│   │
│   ├── 📋 SCRIPTS DE UTILITÁRIOS
│   ├── seed_users.php ................... Cria usuários iniciais
│   ├── import_seed.php .................. Importa dados de exemplo
│   ├── migrate_to_mysql.php ............. Migração SQLite → MySQL
│   ├── diagnostico.php .................. Verifica saúde do sistema
│   └── test_login.php ................... Testa autenticação
│
├── 🎨 TEMPLATES (Páginas)
│   └── templates/
│       ├── login.php .................... Página de login/registro
│       ├── cadastro.php ................. Formulário de cadastro
│       ├── dashboard.php ................ Dashboard com estatísticas
│       ├── produtos.php ................. CRUD de produtos
│       ├── movimentacoes.php ............ Registrar entradas/saídas
│       ├── historico.php ................ Histórico completo
│       ├── perfil.php ................... Perfil do usuário
│       └── base.php ..................... Template base (referência)
│
├── 🎭 STATIC (Assets)
│   └── static/
│       ├── css/
│       │   └── style.css ................ CSS completo (900+ linhas)
│       │                              Colors, Grid, Responsive, Dark mode ready
│       └── js/
│           └── script.js ................ JavaScript utilities (validação, export)
│
├── 🗄️ BANCO DE DADOS
│   ├── saep_db_mysql.sql ............... Schema MySQL/MariaDB
│   ├── saep_seed.sql ................... Dados de exemplo (SQL)
│   └── saep_db.sqlite .................. DB arquivo (se usando SQLite)
│
└── 📚 DOCUMENTAÇÃO
    ├── README.md ....................... Este arquivo
    ├── requirements.txt ................ Dependências de sistema
    └── ARQUIVOS_UTEIS.txt .............. Inventário de arquivos

```

---

## 🏗️ Arquitetura Técnica

### 📊 Banco de Dados

**Tipo Atual:** MySQL/MariaDB  
**Host:** 127.0.0.1:3306  
**User:** root  
**Database:** controle_estoque  

**Tabelas:**
- `usuarios` - Autenticação (2+ registros)
- `produtos` - Catálogo (SKU, nome, preço, quantidade)
- `movimentacoes` - Histórico (entrada/saída com FK)
- `auditoria` - Log de ações (JSON)

### 🔄 Fluxo de Requisição

```
Cliente (Browser)
    ↓
app.php (Router)
    ↓
[Session Check] → [Autenticação] → [CRUD/Handlers]
    ↓
db.php (DB Layer + Retry Logic)
    ↓
MySQL/MariaDB ← [db_call_retry() + recreate_pdo()]
    ↓
Template Renderer
    ↓
HTML Response
```

### 🔐 Segurança Implementada

| Aspecto | Implementação |
|---------|---------------|
| Autenticação | Session PHP + password_hash (BCRYPT) |
| SQL Injection | Prepared Statements em todas as queries |
| XSS | htmlspecialchars() em outputs |
| CSRF | Validação de SESSION |
| Transações | BeginTransaction/Commit em operações críticas |
| Reconexão DB | Retry automático com `db_call_retry()` |
| Permissões | Redirecionamento para login se não autenticado |

---

## 📋 Funcionalidades Detalhadas

### 🔐 Autenticação & Usuários

```
/app.php?action=login
├── Form POST validação
├── verificar_login() → Busca usuário
├── password_verify() → Valida senha
├── SESSION → Armazena dados
└── Redireciona para dashboard

/app.php?action=cadastro
├── Validação de força de senha (6+ chars)
├── Verificação de duplicata (usuario_existe)
├── password_hash() → Criptografa
├── criar_usuario() → INSERT
└── Redireciona para login

/app.php?action=logout
└── session_destroy() → Limpa sessão
```

**Funções Disponíveis:**
```php
criar_usuario($pdo, $username, $password, $nome) 
  → ['sucesso' => true/false, 'mensagem'/'erro']

verificar_login($pdo, $username, $password)
  → ['sucesso' => true/false, 'usuario' => [id, username, nome]]

usuario_existe($pdo, $username)
  → boolean
```

### 📊 Dashboard

```
/app.php?action=dashboard
│
├── [Cards de Estatísticas]
│   ├── Total de Produtos
│   ├── Valor Total em Estoque
│   ├── Movimentações Hoje
│   └── Produtos com Estoque Baixo
│
├── [Tabela: Produtos com Baixa Quantidade]
│   └── Filter: quantidade < 5
│
└── [Tabela: Últimas 5 Movimentações]
    ├── Data/Hora
    ├── Produto
    ├── Tipo (Entrada/Saída)
    └── Quantidade
```

**Função:**
```php
obter_estatisticas($pdo)
  → [total_produtos, valor_total, movimentacoes_hoje, produtos_baixos]
```

### 📦 Produtos (CRUD)

```
POST /app.php?action=produtos [subaction=criar]
├── Validação de sku + nome
├── criar_produto() → INSERT
└── Redireciona com mensagem

GET /app.php?action=produtos&editar=123
├── obter_produto(123) → SELECT
├── Pré-preenche formulário
└── Renderiza template

POST /app.php?action=produtos [subaction=editar]
├── atualizar_produto() → UPDATE
└── Redireciona com mensagem

POST /app.php?action=produtos [subaction=deletar]
├── BEGIN TRANSACTION
├── DELETE FROM movimentacoes (CASCADE)
├── DELETE FROM produtos
└── COMMIT + Redireciona
```

**Funções:**
```php
listar_produtos($pdo) → Array[id, sku, nome, qtd, preco]
obter_produto($pdo, $id) → Array|null
criar_produto($pdo, $sku, $nome, $quantidade, $preco) → [sucesso, id, mensagem]
atualizar_produto($pdo, $id, $sku, $nome, $preco) → [sucesso, mensagem]
deletar_produto($pdo, $id) → [sucesso, mensagem]
```

### 📥 Movimentações

```
POST /app.php?action=movimentacoes
├── Validação (produto_id, tipo, quantidade)
├── registrar_movimentacao() → BEGIN TRANS
│   ├── INSERT INTO movimentacoes
│   ├── UPDATE produtos SET quantidade = quantidade ± ?
│   └── COMMIT
└── Redireciona

GET /app.php?action=movimentacoes
└── Renderiza form + Últimas 10 movimentações
```

**Função:**
```php
registrar_movimentacao($pdo, $produto_id, $tipo, $quantidade, $descricao='')
  → [sucesso, mensagem/erro]
  // Tipo: 'entrada' ou 'saida'
  // Atualiza quantidade automaticamente
```

### 📜 Histórico

```
GET /app.php?action=historico
├── listar_movimentacoes($pdo, 500) → Todas
├── [Opcional] Filtro por data
│   └── DateTime::createFromFormat() → Validação
└── Renderiza tabela com 500+ registros

Colunas:
├── Data/Hora (TIMESTAMP)
├── Código (SKU)
├── Produto (Nome)
├── Tipo (Entrada/Saída com badge)
├── Quantidade
└── Descrição
```

### 👤 Perfil

```
GET /app.php?action=perfil
├── Renderiza informações do usuário
├── Displays: Nome, Username, ID
├── Links para logout
└── Info do sistema
```

---

## 🔧 Configuração e Customização

### 1. Banco de Dados

**MySQL/MariaDB (Recomendado):**
```php
// db.php - linha 8-15
$db_config = [
    'type' => 'mysql',
    'host' => '127.0.0.1',
    'port' => 3306,
    'user' => 'root',
    'password' => '',
    'database' => 'controle_estoque'
];
```

**SQLite (Alternativa):**
```php
// db.php - descomentar linhas 18-21
$db_config = [
    'type' => 'sqlite',
    'file' => __DIR__ . '/saep_db.sqlite'
];
```

### 2. Cores e Tema

**Arquivo:** `static/css/style.css` (linhas 1-11)
```css
:root {
    --primary: #2c3e50;      /* Cabeçalho/Primário */
    --secondary: #3498db;    /* Botões/Secundário */
    --success: #27ae60;      /* Entrada/Verde */
    --warning: #f39c12;      /* Alerta/Laranja */
    --danger: #e74c3c;       /* Erro/Vermelho */
    --light: #ecf0f1;        /* Fundo claro */
    --dark: #2c3e50;         /* Fundo escuro */
    --border: #bdc3c7;       /* Bordas */
    --shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}
```

### 3. Responsividade

O CSS inclui breakpoints para:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (480px - 767px)
- Micro (< 480px)

---

## 🛡️ Tratamento de Erros e Recuperação

### Reconexão Automática de BD

**Implementação:** `db.php` linhas 77-98
```php
function db_call_retry(PDO $p, callable $fn) {
    try {
        return $fn($p);
    } catch (PDOException $e) {
        // Detecta "MySQL server has gone away"
        if (stripos($msg, 'MySQL server has gone away') !== false || $errno == 2006) {
            // Tenta reconectar com recreate_pdo()
            $new = recreate_pdo();
            return $fn($new);  // Retry
        }
        throw $e;  // Rethrow se não for reconexão
    }
}
```

**Aplicado em:**
- `usuario_existe()` - Verifica duplicata
- `criar_usuario()` - Inserção de novo usuário
- `verificar_login()` - Autenticação (2 queries com retry)
- `listar_produtos()` / `obter_produto()`
- `criar_produto()` / `atualizar_produto()` / `deletar_produto()`
- `listar_movimentacoes()` / `registrar_movimentacao()`
- `obter_estatisticas()` - 4 queries com retry

---

## 📝 API de Funções PHP

### Módulo: Usuários (`db.php`)
```php
// Criar novo usuário
criar_usuario(PDO $pdo, string $username, string $password, string $nome)
  → Array ['sucesso' => true, 'mensagem' => '...']
  → Array ['sucesso' => false, 'erro' => '...']

// Verificar login
verificar_login(PDO $pdo, string $username, string $password)
  → Array ['sucesso' => true, 'usuario' => [id, username, nome]]
  → Array ['sucesso' => false, 'erro' => '...']

// Verificar existência
usuario_existe(PDO $pdo, string $username)
  → boolean

// Inicializar banco (SQLite)
init_database(PDO $pdo)
  → boolean
```

### Módulo: Produtos (`db.php`)
```php
// Listar todos
listar_produtos(PDO $pdo)
  → Array [
      ['id' => 1, 'sku' => 'SKU-001', 'nome' => '...', 'quantidade' => 100, 'preco' => 29.90],
      ...
    ]

// Obter um
obter_produto(PDO $pdo, int $id)
  → Array ['id' => 1, ...] | null

// Criar
criar_produto(PDO $pdo, string $sku, string $nome, int $quantidade, float $preco)
  → Array ['sucesso' => true, 'id' => 5, 'mensagem' => '...']

// Atualizar
atualizar_produto(PDO $pdo, int $id, string $sku, string $nome, float $preco)
  → Array ['sucesso' => true, 'mensagem' => '...']

// Deletar (com CASCADE)
deletar_produto(PDO $pdo, int $id)
  → Array ['sucesso' => true, 'mensagem' => '...']
```

### Módulo: Movimentações (`db.php`)
```php
// Listar com JOIN
listar_movimentacoes(PDO $pdo, int $limit = 100)
  → Array [
      ['id' => 1, 'produto_id' => 5, 'produto_nome' => '...', 'sku' => '...', 'tipo' => 'entrada', 'quantidade' => 50, 'data' => '2025-11-12 10:30:00', 'descricao' => '...'],
      ...
    ]

// Registrar nova (com transação)
registrar_movimentacao(PDO $pdo, int $produto_id, string $tipo, int $quantidade, string $descricao = '')
  → Array ['sucesso' => true, 'mensagem' => '...']
  // $tipo: 'entrada' ou 'saida'
  // Atualiza quantidade de forma automática
```

### Módulo: Dashboard (`db.php`)
```php
// Obter estatísticas
obter_estatisticas(PDO $pdo)
  → Array [
      'total_produtos' => 42,
      'valor_total' => '12345,67',
      'movimentacoes_hoje' => 15,
      'produtos_baixos' => 3
    ]
```

### Helpers (`db.php`)
```php
// Recriar conexão (usado internamente)
recreate_pdo()
  → PDO

// Executar com retry automático (usado internamente)
db_call_retry(PDO $pdo, callable $fn)
  → mixed (resultado de $fn)
```

---

## 🎨 Frontend & JavaScript

### Arquivo: `static/js/script.js`

**Funcionalidades:**
- ✅ Auto-fechar alertas após 5s
- ✅ Confirmação em deleções
- ✅ Máscara de moeda (preço)
- ✅ Hover efeitos em tabelas
- ✅ Exportar tabela para CSV
- ✅ Imprimir tabela
- ✅ Validações client-side

**Funções Principais:**
```javascript
// Auto-fechar alerts
document.querySelectorAll('.alert').forEach(...)

// Confirmar antes de deletar
form[method="POST"] submit event listener

// Máscara de moeda
input[name*="preco"] blur event

// Formatação de números
formatarNumeros()

// Efeito hover em tabelas
adicionarHoverTabelas()

// Exportar CSV
exportarCSV(nomeArquivo = 'exportacao.csv')

// Imprimir
imprimirTabela()
```

---

## 🌍 URLs e Rotas

| Rota | Método | Descrição |
|------|--------|-----------|
| `/app.php?action=login` | GET/POST | Login |
| `/app.php?action=cadastro` | GET/POST | Registrar novo usuário |
| `/app.php?action=dashboard` | GET | Dashboard com estatísticas |
| `/app.php?action=produtos` | GET/POST | Gerenciar produtos (CRUD) |
| `/app.php?action=movimentacoes` | GET/POST | Registrar movimentações |
| `/app.php?action=historico` | GET | Histórico completo |
| `/app.php?action=perfil` | GET | Perfil do usuário |
| `/app.php?action=logout` | GET | Sair |
| `/seed_users.php` | GET | Criar usuários iniciais |
| `/import_seed.php` | GET | Importar dados de exemplo |
| `/migrate_to_mysql.php` | GET | Migrar SQLite → MySQL |
| `/diagnostico.php` | GET | Verificar saúde do sistema |

---

## 📊 Modelos de Dados

### Tabela: `usuarios`
```sql
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    ativo INT DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabela: `produtos`
```sql
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(100) UNIQUE,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    quantidade INT DEFAULT 0,
    quantidade_minima INT DEFAULT 5,
    preco DOUBLE DEFAULT 0,
    categoria VARCHAR(100),
    ativo INT DEFAULT 1,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Tabela: `movimentacoes`
```sql
CREATE TABLE movimentacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    usuario_id INT,
    tipo VARCHAR(50) COMMENT 'entrada ou saida',
    quantidade INT NOT NULL,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    descricao TEXT,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);
```

### Tabela: `auditoria`
```sql
CREATE TABLE auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    acao VARCHAR(100),
    tabela VARCHAR(100),
    registro_id INT,
    dados_anteriores JSON,
    dados_novos JSON,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);
```

---

## 🔧 Requisitos de Sistema

| Componente | Versão Mínima | Recomendada |
|-----------|---------------|-------------|
| **PHP** | 7.4 | 8.1+ |
| **MySQL/MariaDB** | 5.7 | 8.0+ |
| **Apache** | 2.4 | 2.4.x |
| **Browser** | ES6 support | Chrome 90+, Firefox 88+, Safari 14+ |

### Extensões PHP Necessárias
- `pdo` (PDO)
- `pdo_mysql` (MySQL) ou `pdo_sqlite` (SQLite)
- `json` (JSON)
- `session` (Sessions)

### Permissões de Arquivo
```bash
chmod 755 saep_estoque/
chmod 664 saep_estoque/*.sql
chmod 664 saep_estoque/saep_db.sqlite (se criado)
```

---

## 🚀 Performance & Otimizações

### Índices do Banco
- `usuarios.username` - UNIQUE (busca rápida)
- `produtos.sku` - UNIQUE (código rápido)
- `movimentacoes.produto_id` - FK (JOIN rápido)
- `movimentacoes.data` - INDEX (filtro por data)

### Queries Otimizadas
- Prepared Statements (previne SQL Injection)
- Transações em operações críticas
- SELECT only needed columns
- LIMIT em listagens

### Caching Potencial (Futuro)
- Cache de estatísticas (5 min)
- Cache de produtos (1 min)
- Cache de movimentações (30 seg)

---

## 🛠️ Troubleshooting

### ❌ "Erro na conexão com banco de dados"
**Solução:**
1. Verificar se MySQL está rodando: `netstat -ano | findstr :3306`
2. Verificar credenciais em `db.php` (linha 8-15)
3. Verificar se database `controle_estoque` existe
4. Acessar `/diagnostico.php` para mais info

### ❌ "MySQL server has gone away"
**Solução:**
1. O sistema tenta reconectar automaticamente (máx 1x)
2. Se persistir, aumentar `wait_timeout` no MySQL:
   ```sql
   SET GLOBAL wait_timeout = 28800;  -- 8 horas
   ```

### ❌ CSS não carrega
**Solução:**
1. Verificar path `/saep_estoque/static/css/style.css`
2. Verificar permissões do arquivo (744)
3. Limpar cache do browser (Ctrl+Shift+Del)

### ❌ Sessão expira rapidinho
**Solução:**
1. Aumentar `session.gc_maxlifetime` em `php.ini`:
   ```ini
   session.gc_maxlifetime = 86400  ; 24 horas
   session.cookie_lifetime = 86400
   ```

---

## 📞 Desenvolvimento Futuro (Roadmap)

- [ ] Relatórios em PDF/Excel
- [ ] Integração com QR Code para produtos
- [ ] API REST para mobile app
- [ ] Sistema de permissões por role
- [ ] Backup automático do BD
- [ ] Notificações de estoque baixo
- [ ] Gráficos de movimentações
- [ ] Multi-departamento
- [ ] Two-Factor Authentication (2FA)
- [ ] Dark Mode nativo

---

## 📄 Licença

**SAEP - Sistema de Controle de Estoque © 2025**

Desenvolvido com ❤️ em PHP + MySQL

---

## 📋 Checklist de Deploy

- [ ] MySQL/MariaDB instalado e rodando
- [ ] Database `controle_estoque` criado
- [ ] Pasta em `C:\xampp\htdocs\controle_estoque\saep_estoque\`
- [ ] Permissões corretas (755 pastas, 644 arquivos)
- [ ] `db.php` configurado com credenciais corretas
- [ ] Apache/Nginx rodando
- [ ] Acessar `http://localhost/saep_estoque/app.php?action=login`
- [ ] Rodar `/seed_users.php` para criar usuários
- [ ] Testar `/diagnostico.php`
- [ ] Fazer login com admin/123456
- [ ] Criar primeiro produto
- [ ] Registrar movimentação
- [ ] ✅ Sistema pronto!

---

**Versão:** 1.1 (com Retry Logic e MySQL)  
**Última atualização:** 12 de Novembro de 2025  
**Status:** ✅ Pronto para Produção
