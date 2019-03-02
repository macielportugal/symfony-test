# Symfony Test

## Resumo

Teste de api  de usuário no Symfony 4.

## Requisitos
* <a href="https://www.docker.com/">Docker</a>
* <a href="https://docs.docker.com/compose/">Docker Compose</a>

## Instalação
```bash
git clone git@github.com:macielportugal/symfony-test.git
cd symfony-test
docker-compose build
docker-compose up -d
docker-compose exec php composer install
```

Crie o banco de dados e carrege as informações dos usuários.
```bash
docker-compose exec php bin/console doctrine:database:create
docker-compose exec php bin/console doctrine:fixtures:load
```

## Uso

Listagem de usuários.

```bash
curl -X GET http://127.0.0.1:8000/api/user
```

No json de retorno você terá a quantidade usuário e o link para próxima página ou anterior.

```bash
{
    'count': 51,
    'items': [
        ...
    ],
    'links': {
        'previous': '/api/user?page=1',
        'next': '/api/user?page=3'
    }
}
```

É possivel fazer a filtragem usando o parâmetros email, firstName e lastName. 

```bash
curl -X GET http://127.0.0.1:8000/api/user?email=user01&firstName=Fulano
```


Mostar um usuário.

```bash
curl -X GET http://127.0.0.1:8000/api/user/12 #ID do ususário
```

Para adição, edição e remoção é necessário criar um token antes.

```bash
curl -X POST  http://127.0.0.1:8000/api/login_check  -H "Content-Type:application/json"  -d '{"username": "user1@user1.com", "password": "123456" }'
```

Adição.

```bash
curl -X POST  http://127.0.0.1:8000/api/user/create  -H "Authorization: Bearer COLOQUE_AQUI_O_TOKEN"  -H "Content-Type:application/json"  -d '{"email": "fulano@fulano.com", "password": "123456", "firstName": "Fulano", "lastName": "Ciclano" }'
```

Edição.

```bash
curl -X PUT  http://127.0.0.1:8000/api/user/12  -H "Authorization: Bearer COLOQUE_AQUI_O_TOKEN"  -H "Content-Type:application/json"  -d '{"email": "fulano@fulano.com", "password": "123456", "firstName": "Fulano", "lastName": "Ciclano" }'
```

Remoção.
```bash
curl -X DELETE  http://127.0.0.1:8000/api/user/12  -H "Authorization: Bearer COLOQUE_AQUI_O_TOKEN"
```

## Testes

```bash
docker-compose exec php bin/phpunit
```
