# Sistema de Gerenciamento de Usuários

Um CRUD moderno, responsivo e seguro para gestão de usuários, feito com PHP, Bootstrap, MySQL e um toque especial de UX.

## ✨ Funcionalidades

- Autenticação (login seguro)
- Cadastro, edição e exclusão de usuários
- Visualização em tabela e cards
- Busca, filtro e ordenação dos usuários
- Exportação para Excel
- Ativação e inativação de usuários
- Dashboard com resumos e gráficos (Chart.js)
- Design clean e responsivo (verde/branco)
- Feedback visual amigável

## 🚀 Tecnologias Utilizadas

- PHP 7+
- MySQL
- Bootstrap 5
- Chart.js
- HTML5, CSS3, JavaScript

## 📦 Instalação e Uso

1. **Clone este repositório**
    ```bash
    git clone https://github.com/seuusuario/seurepo.git
    ```
2. **Crie o banco de dados MySQL**  
   Use o script disponível em `/database.sql` ou crie a tabela de `usuarios` conforme sua estrutura.

3. **Configure o acesso ao banco**
   - Edite o arquivo `conexao.php` com seus dados de conexão MySQL.

4. **Suba os arquivos em um servidor local (ex: XAMPP, WampServer, Laragon) ou servidor online**  
   - Exemplo: coloque na pasta `htdocs` se usar XAMPP.

5. **Acesse o sistema via navegador**
   - Vá até `http://localhost/seupasta/login.php`

## 📸 Prints do Sistema

![login](https://github.com/user-attachments/assets/4880aed6-9e4a-4d15-9a78-84fd031646bc)

![dash](https://github.com/user-attachments/assets/04542335-b001-44a7-8128-0694ae6af430)

![image](https://github.com/user-attachments/assets/89b25b86-7f05-4f19-b449-437ea4897211)



## 🗃️ Estrutura do Projeto
/conexao.php
/index.php
/dashboard.php
/login.php
/usuario-create.php
/usuario-edit.php
/usuario-view.php
/acoes.php
/exportar.php
/navbar.php
/mensagem.php
/styles.css
/README.md
