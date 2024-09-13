# Sistema de Cadastro de Despesas via API

## Visão Geral

Este projeto é um sistema para gerenciamento e cadastro de despesas, acessível através de uma API. Ele permite que os usuários criem, atualizem e excluam despesas. A API foi desenvolvida para ser simples e eficiente.

## Funcionalidades

- Cadastro de Despesas: Adicione novas despesas ao sistema.
- Listagem de Despesas: Consulte todas as despesas cadastradas.
- Atualização de Despesas: Atualize detalhes de uma despesa existente
- Exclusão de Despesas: Remova despesas do sistema.

## Stack utilizada

**Back-end:** Laravel v11

**Banco de dados:** MariaDB

## Instalação e Execução

Clone o repositório:

```bash
  git clone https://github.com/rafa-gil/despesas-api.git
```

Navegue até o diretório do projeto:

```bash
  cd despesas-api/
```

Crie um arquivo .env baseado no .env.example

```bash
  cp .env.example. .env
```

```bash
  docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

```bash
 ./vendor/bin/sail up -d 
```

## Ligar o Worker

```bash
 ./vendor/bin/sail horizon

```

## Exucutar Testes Funcionais

```bash
 ./vendor/bin/sail pest

```

## Documentação 

```bash
http://localhost/api/documentation
```

## Collections Postman

As collections estão na pasta collections

# API Rotas - implementadas

## Cadastro de usuário

1.  Cadastro

```
POST http://localhost/api/register
```

2.  Login

```
POST http://localhost/api/login
```

3. Logout

```
GET http://localhost/api/login
```

## Despesas

1. Index

```
GET http://localhost/api/v1/expenses
```

2. Criar Despesa

```
POST http://localhost/api/v1/expenses
```

3.  Atualizar despesa

```
PATCH http://localhost/api/v1/expenses/:ID
```

4. Deletar

```
DELETE http://localhost/api/v1/expenses/:ID
```
