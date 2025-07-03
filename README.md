# Boleiros de Sábado 🏆⚽

Este é um sistema web desenvolvido em PHP para gerenciar o time amador **Boleiros de Sábado**, permitindo controle de enquetes, presenças, sorteio de times, histórico do time, patrocinadores, melhor em campo, ranking de jogadores, álbum de fotos e muito mais.

---

## 📌 Funcionalidades

- ✅ Votação da enquete da semana
- ✅ Confirmação de presença para os jogos
- ✅ Sorteio automático dos times
- ✅ Votação do "Melhor em Campo" por posição
- ✅ Ranking de jogadores por desempenho
- ✅ Cadastro e exibição de patrocinadores
- ✅ Álbum de fotos com carrossel
- ✅ Página "Sobre o Time"
- ✅ Área administrativa com painel de controle
- ✅ Integração com banco de dados MySQL/MariaDB

---

## 💻 Tecnologias utilizadas

- PHP 7+
- HTML5, CSS3, JavaScript
- Bootstrap (para o layout)
- MySQL ou MariaDB
- Composer (gerenciador de dependências PHP)
- DOMPDF (para geração de PDFs, se necessário)

---

## 🚀 Instalação local

### 1. Clone o repositório

```bash
git clone https://github.com/valdecirsevergnini/site-jogo.git
2. Entre na pasta do projeto

cd site-jogo
3. Instale as dependências do PHP
composer install
Se você não tem o Composer instalado, baixe em: https://getcomposer.org/

🛠️ Configuração do Banco de Dados
1. Crie o banco de dados MySQL com o nome: de sua escolha

CREATE DATABASE boleiros DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
2. Importe o arquivo .sql que acompanha o projeto
mysql -u root -p boleiros < boleiros.sql
3. Configure a conexão com o banco
No arquivo config/database.php:

$host = 'localhost';
$dbname = 'boleiros';
$user = 'root';
$pass = '';
▶️ Como rodar o sistema
Inicie um servidor local (XAMPP, Laragon, Wamp etc.)

Coloque o projeto na pasta htdocs

Acesse:

http://localhost/site-jogo/
A área administrativa fica em /painel

👨‍💻 Autor
Projeto desenvolvido por Valdecir Severgnini
