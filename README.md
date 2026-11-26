# Organizaê — Projeto Integrador UNISA

Aplicação acadêmica de gerenciamento de tarefas construída com PHP 8, MySQL, PDO, HTML, CSS e JavaScript.

## Requisitos
- XAMPP com Apache, MySQL e PHP 8.
- Navegador atualizado.

## Instalação local (Windows)
1. Copie a pasta `organizae` para `C:\xampp\htdocs\`.
2. Abra o XAMPP Control Panel e inicie Apache e MySQL.
3. Acesse `http://localhost/phpmyadmin`.
4. Clique em **Importar**, selecione `database/schema.sql` e execute.
5. Confira as credenciais em `config/bootstrap.php`. A configuração padrão é usuário `root` e senha vazia, comum em instalações locais do XAMPP.
6. Acesse `http://localhost/organizae/`.
7. Crie sua conta pela tela de cadastro.

## Funcionalidades incluídas
- Cadastro, login e logout.
- Senhas com `password_hash()` e `password_verify()`.
- Sessões e proteção CSRF nos formulários de alteração.
- Consultas preparadas PDO.
- Separação das tarefas e categorias por usuário.
- Dashboard com total, pendentes, concluídas e atrasadas.
- CRUD de tarefas, filtros e alternância de status.
- Cadastro e listagem de categorias.

## Observações
Este projeto é uma versão inicial para desenvolvimento e testes locais. Antes de publicar em hospedagem na internet, revise configurações, credenciais, HTTPS, mensagens de erro, política de backup e demais medidas de segurança. Os testes acadêmicos devem ser executados pelo aluno e registrados com seus resultados reais.
