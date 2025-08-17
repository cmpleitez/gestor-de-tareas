#!/bin/bash

# Script de despliegue para Forge - Copia assets estáticos
# Este script debe ejecutarse después de git pull en Forge

echo "🚀 Iniciando despliegue de assets estáticos..."

# Crear directorio de assets si no existe
mkdir -p public/assets

# Copiar CSS y assets
echo "📁 Copiando CSS y assets..."
cp -r resources/css/app-assets public/assets/

# Copiar JavaScript
echo "📁 Copiando JavaScript..."
cp -r resources/js public/assets/

# Copiar CSS principal
echo "📁 Copiando CSS principal..."
cp resources/css/app.css public/assets/

# Establecer permisos correctos
echo "🔐 Estableciendo permisos..."
chmod -R 755 public/assets

echo "✅ Despliegue de assets completado!"
echo "📂 Los archivos están disponibles en: public/assets/"
