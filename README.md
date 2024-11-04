# Tecnologias

- PHP-8.3
- Laravel 12
- MySql
- Docker

# Como rodar o projeto?

1. `docker compose up -d --build --force-recreate`
2. Aguardar o container do banco de dados subir, você pode acompanhar pelo comando `docker logs -f app`
3. Acessar o link `http://localhost:8080/` via navegador

# Como utilizar a aplicação?

1. Efetuar o cadastro via tela de registro
2. Efetuar o login
3. Cadastrar documento
4. Para definir os valores das variáveis, utilizar o botão 'editar' para o arquivo cadastrado
5. Você pode visualizar, baixar em docx, baixar em pdf e excluir o documento.
6. Efetuar o logout
