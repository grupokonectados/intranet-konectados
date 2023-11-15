# intranet-konectados


## NOTA IMPORTANTE: 
ACTUALMENTE LA RAMA QUE SE ENCUENTRA EN EJECUCION ES DEVELOP



## Instalacion: 

### Cambiar la rama: 

1. git checkout develop

### Instalar el proyecto. 

```bash
composer update
```
Luego debe copiar el .env.example

```bash
cp .env.examle .env
```

Luego debe configurar la Base de datos en el .env

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=database_name
DB_USERNAME=username
DB_PASSWORD=password
```

luego debe solicitar las key

```bash 
php artisan key:generate
```

instalar las dependencias de js

```bash 
npm i
```

luego ejecutar las migraciones

```bash 
php artisan migrate:fresh --seed
```

luego para ejecutar de manera local, abrir 2 terminales

1. `php artisan serve`
2. `npm run dev` 

y vaya a su navegador coloque 

http://localhost:8000



### usuarios


los genericos, los puedes encontrar en la carpeta 

app/database/seeders/CreateUserAdminSeeder.php


### Para que les funcione el API deben pegar el siguiente codigo a lo ultimo del .env

```

# API

API_URL=http://apiest.konecsys.com:8080

#ENDPOINTS

API_CLIENTS=/clientes
API_CLIENT=/cliente
API_CHANNELS=/canales
API_ESTRUCTURA=/estructura
API_ESTRATEGIAS=/estrategias
API_ESTRATEGIA=/estrategia
API_EMAILS=/email/templates/
API_EMAIL=/email/template/
```