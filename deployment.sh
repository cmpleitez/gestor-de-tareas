#!/bin/bash

# Script de despliegue para Azure App Service
echo "Iniciando configuración post-deployment para Laravel..."

# Verificar que estamos en el directorio correcto
cd /home/site/wwwroot

# Limpiar cache de configuración
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar conexión a base de datos
echo "Verificando conexión a base de datos..."
php artisan migrate:status

# Ejecutar migraciones si es necesario
echo "Ejecutando migraciones..."
php artisan migrate --force

# Ejecutar seeders (solo en primer despliegue)
echo "Ejecutando seeders..."
php artisan db:seed --force

# Configurar permisos de storage
chmod -R 775 storage bootstrap/cache

echo "Configuración post-deployment completada!"
