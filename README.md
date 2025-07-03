# Boleiros de Sábado ⚽🔥

Este é um sistema web em PHP desenvolvido para o time amador **Boleiros de Sábado**, com foco em interação, gestão de jogadores, votação, finanças e organização dos jogos. O sistema possui uma área pública com informações e uma área administrativa para gerenciar o conteúdo.

---

## 📌 Funcionalidades

- ✅ Votação da enquete da semana
- ✅ Confirmação de presença para os jogos
- ✅ Sorteio automático dos times
- ✅ Votação de "Melhor em Campo" por posição
- ✅ Ranking dos jogadores por pontuação
- ✅ Controle de mensalidades e receitas/despesas
- ✅ Cadastro de provisões financeiras
- ✅ Cadastro e exibição de patrocinadores
- ✅ Álbum de fotos com carrossel
- ✅ Página “Sobre o Time”
- ✅ Área administrativa com permissões

---

## 💻 Tecnologias utilizadas

- PHP 7+
- MySQL/MariaDB
- Bootstrap 4
- HTML5, CSS3, JavaScript
- Composer (dependências PHP)
- DOMPDF (se geração de PDFs estiver ativa)

---

## 🚀 Instalação local

### 1. Clone o repositório

```bash
git clone https://github.com/valdecirsevergnini/site-jogo.git
cd site-jogo
2. Instale as dependências PHP

composer install
⚠️ Se não tiver o Composer: https://getcomposer.org/

🛠️ Configuração do Banco de Dados
1. Crie o banco de dados:

CREATE DATABASE u981260588_boleiros DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
2. Importe o arquivo SQL
O arquivo de estrutura e dados está em: database/u981260588_boleiros.sql

No phpMyAdmin:

Selecione o banco u981260588_boleiros

Clique em Importar

Envie o arquivo u981260588_boleiros.sql

Ou via terminal MySQL:


mysql -u root -p u981260588_boleiros < database/u981260588_boleiros.sql
3. Configure a conexão no projeto
No arquivo config/database.php (ou similar):


$host = 'localhost';
$dbname = 'boleiros';
$user = 'root';
$pass = '';
▶️ Como rodar o sistema
Inicie seu servidor local (XAMPP, Laragon ou similar)

Coloque o projeto em htdocs ou www

Acesse no navegador:

http://localhost/site-jogo/
A área administrativa geralmente está em:

http://localhost/site-jogo/painel/
Usuário padrão no banco:

Usuário: admin

Senha: crie uma nova senha com hash

⚠️ Você pode atualizar a senha direto no banco ou implementar sistema de login seguro.

📂 Estrutura de diretórios
/painel: área administrativa

/config: configurações de banco e autenticação

/img: imagens do site

/database: dump SQL do banco

/uploads: arquivos enviados pelos usuários

👨‍💻 Autor
Desenvolvido por Valdecir Severgnini

📝 Licença
Uso livre para fins pessoais e educacionais. Projeto open-source.
