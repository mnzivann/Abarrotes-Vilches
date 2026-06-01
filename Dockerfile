FROM php:8.3-apache

# 1. Instalar herramientas básicas y dependencias
RUN apt-get update && apt-get install -y \
    apt-transport-https \
    gnupg2 \
    curl \
    unixodbc-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. Agregar el repositorio de Microsoft (Método moderno)
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg \
    && curl -fsSL https://packages.microsoft.com/config/debian/12/prod.list > /etc/apt/sources.list.d/mssql-release.list

# 3. Instalar el driver ODBC 18 de Microsoft
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18

# 4. Instalar y habilitar extensiones de SQL Server en PHP
RUN pecl install sqlsrv pdo_sqlsrv \
    && docker-php-ext-enable sqlsrv pdo_sqlsrv