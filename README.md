# desafio_helpay

<h1> Para iniciar o projeto, os passos são os seguintes:</h1>



- rodar o comando : docker-compose up -d
- rodar o comando : docker-compose exec app php artisan key:generate
- rodar o comando : docker-compose exec app php artisan config:cache
- rodar o comando : docker-compose exec app php artisan migrate

seu projeto deve estar disponivel em http://localhost


* edite seu arquivo .env se necessario
