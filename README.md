# AlvoFlow — Laravel + Inertia (Vue) + MySQL

Este é o projeto AlvoFlow migrado do protótipo estático (HTML/CSS/JS) para uma aplicação Laravel real, usando Inertia.js com Vue 3 e MySQL. Foi preparado para correr no **Laragon**, no Windows.

## O que já está pronto

- Backend Laravel 11 completo: rotas, controllers, models Eloquent (`Pedido`, `Sentinela`, `User`), migrations e seeders com dados de exemplo.
- Autenticação própria (login/registo) com sessão, sem depender de nenhum pacote de terceiros — os formulários já submetem com Enter, tal como no protótipo.
- Frontend em Vue 3 + Inertia (SPA sem API separada), reaproveitando o mesmo CSS, paleta de cores e ícones SVG do protótipo.
- Páginas: Início, Criar Pedido, Pesquisar (com filtro por tipo de campo), Sentinela, Visualizar Pedidos (galeria + calendário, exportação `.ics` para o Google Agenda), Painel (gráficos Chart.js + exportação PDF), Perfil e Sobre.
- Modal de detalhes/edição de pedido (editar quantidade e observações, mudar estado, excluir) partilhado entre as páginas.

## Simplificações conscientes (para manter o projeto enxuto)

- O combobox de pesquisa de edições da Sentinela foi trocado por um `<select>` nativo (mais simples e já filtra pelo servidor).
- O relatório em PDF gera uma tabela organizada dos pedidos (cabeçalho + tabela), em vez de todas as secções do protótipo original.
- As páginas "Perfil" e "Sobre" foram simplificadas (sem upload de foto, preferências fictícias, etc.) — dá para expandir depois.
- Este projeto **não foi executado dentro deste ambiente** (sandbox sem acesso ao Packagist), por isso siga os passos abaixo com atenção. Todo o código PHP foi validado com `php -l` e os componentes Vue foram validados sintaticamente, mas o primeiro `composer install`/`npm install` reais só vão acontecer no seu Laragon.

## Como configurar no Laragon

1. **Extraia esta pasta** para `C:\laragon\www\alvoflow` (ou onde o Laragon guarda os seus projetos).
2. Abra o Laragon, clique em **Terminal** (ou use o terminal do seu editor) e navegue até à pasta do projeto.
3. Instale as dependências PHP:
   ```
   composer install
   ```
4. Instale as dependências JS:
   ```
   npm install
   ```
5. Copie o ficheiro de ambiente e gere a chave da aplicação:
   ```
   copy .env.example .env
   php artisan key:generate
   ```
6. No **HeidiSQL** (vem com o Laragon) ou no phpMyAdmin, crie uma base de dados chamada `alvoflow` (utf8mb4).
7. Confirme no `.env` que `DB_DATABASE=alvoflow`, `DB_USERNAME=root` e `DB_PASSWORD=` correspondem à sua instalação do Laragon (por omissão, o MySQL do Laragon usa utilizador `root` sem palavra-passe).
8. Corra as migrations com dados de exemplo:
   ```
   php artisan migrate --seed
   ```
9. Compile os assets (uma vez, ou deixe correndo durante o desenvolvimento):
   ```
   npm run dev
   ```
10. Com o Laragon a correr (Apache/Nginx ligado), ative o **Auto Virtual Hosts** do Laragon e aceda a `http://alvoflow.test` — ou simplesmente use `php artisan serve` e aceda a `http://127.0.0.1:8000`.

## Conta de acesso de exemplo

O seeder cria uma conta pronta a usar:

- **Email:** `publicador@congregacao.pt`
- **Palavra-passe:** `password`

Também pode registar uma conta nova pela própria tela de "Criar conta".

## Estrutura relevante

```
app/Models/            Pedido.php, Sentinela.php, User.php
app/Http/Controllers/  HomeController, PedidoController, SentinelaController, PainelController, PageController, Auth/*
database/migrations/   Tabelas pedidos e sentinelas
database/seeders/      Dados de exemplo (iguais aos do protótipo)
resources/js/Pages/    Páginas Vue (Home, Pedidos/*, Sentinela/*, Painel, Perfil, Sobre, Auth/*)
resources/js/Layouts/  AppLayout.vue — menu inferior, topo, submenu "Mais"
resources/js/Components/ PedidoCard, PedidoDetailModal, PedidoEditModal
resources/css/app.css  CSS completo herdado do protótipo (paleta, temas claro/escuro, componentes)
```

## Deploy (Aiven + Render)

O projeto já vem com `Dockerfile`, `docker-entrypoint.sh` e `render.yaml` prontos para o Render.

1. **Aiven (MySQL):** crie um serviço MySQL no [console da Aiven](https://console.aiven.io), copie host/porta/utilizador/senha/nome da base e o certificado `ca.pem` (a Aiven exige TLS).
2. **Render (Web Service):** conecte o repositório GitHub, escolha "Docker" como ambiente. Se usar o `render.yaml`, o Render já sugere as variáveis de ambiente que faltam preencher (`APP_KEY`, `APP_URL`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`, `DB_SSL_CA`).
3. Gere o `APP_KEY` localmente com `php artisan key:generate --show` e cole o valor no Render.
4. Em `DB_SSL_CA`, cole o conteúdo completo do `ca.pem` da Aiven (o container escreve isso num arquivo e configura a ligação TLS automaticamente).
5. No primeiro deploy, o `docker-entrypoint.sh` já roda `php artisan migrate --force` automaticamente — não precisa rodar `--seed` em produção (os dados de exemplo são só para desenvolvimento local).

## Próximos passos sugeridos

- Reforçar autorizações (por exemplo, mais do que um perfil de utilizador/publicador) se o sistema crescer para várias congregações.
- Trocar o servidor embutido (`php artisan serve`, usado no `docker-entrypoint.sh`) por php-fpm + nginx se o tráfego crescer além do que um balcão de publicações costuma gerar.
