#!/bin/bash

echo "1. Copiar repositorio"
sudo git clone https://github.com/grupokonectados/intranet-konectados.git

echo "2. cambiar de rama"
sudo git checkout "(feature)develop/repetidos"

echo "3. Copiar el .env"
sudo cp .env.example .env

echo "4. Actualizar"
sudo composer update

echo "5. instalar dependecias NPM"
sudo npm install 

echo "6. Compilar Vite"
sudo npm run build

echo "Crear key laravel" 
sudo php artisan key:generate