# Teste Tecnofit

API criada para o teste da Tecnofit

## Endpoints

| Método | Rota              | Descrição                                           |
| ------ | ----------------- | --------------------------------------------------- |
| POST   | /movements/search | Deve receber um JSON com o id ou name do movimento. |

## Instalação

```bash
git clone https://github.com/ektoplasm/teste-tecnofit.git
docker compose -f 'docker-compose.yml' up -d --build
```

Acesse em `http://localhost:8080`.

## Configuração

As variáveis de ambiente estão no arquivo `docker/dev.env`, alterar conforme necessário.

## Testes

Para executar os testes existentes, execute o comando:

```bash
./phpunit
```

## Licença

Privativa.
