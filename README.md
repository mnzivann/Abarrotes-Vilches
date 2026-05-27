# Sistema de Control de Inventario y Ventas - Abarrotes Vilches

Este proyecto es un sistema de gestión web desarrollado en **PHP** utilizando **Microsoft SQL Server** como sistema gestor de base de datos. El entorno completo está contenerizado con **Docker**, lo que garantiza que el sistema funcione exactamente igual en cualquier sistema operativo (Windows y macOS).

---

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente en tu equipo:
* [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Debe estar encendido).
* [Visual Studio Code](https://code.visualstudio.com/).
* [Git](https://git-scm.com/).
* *Opcional para Windows:* [SQL Server Management Studio (SSMS)](https://learn.microsoft.com/es-es/sql/ssms/download-sql-server-management-studio-ssms).

---

## Estructura del Repositorio

```text
├── docker-compose.yml     # Configuración de los contenedores (Servidor + BD)
├── index.php              # Catálogo de Productos (Pantalla Principal)
├── inventario.php         # Control de Entradas y Salidas
├── ventas.php             # Historial de Ventas
├── nueva_venta.php        # Punto de Venta operativo (Cajero)
├── mermas.php             # Registro de Pérdidas
├── empleados.php          # Directorio de Usuarios
├── reportes.php           # Módulo de Reportes Gerenciales
└── database.sql           # Script de creación y población de la BD

```

## Instalación y Arranque del Entorno

Sigue estos pasos para levantar el proyecto en tu computadora local:

### 1. Clonar el repositorio
Abre una terminal en tu computadora y ejecuta:
```bash
git clone [https://github.com/tu-usuario/nombre-del-repositorio.git](https://github.com/tu-usuario/nombre-del-repositorio.git)
cd nombre-del-repositorio
```
### 2. Levantar los contenedores de Docker
En la terminal del proyecto, ejecuta el siguiente comando para construir y encender el servidor web y el motor de base de datos en segundo plano:
```bash
docker-compose up -d
```
### 3. Acceder al sistema
Una vez que Docker termine el proceso, abre tu navegador web e ingresa a:
* **Sistema Web:** [http://localhost:8080/index.php](http://localhost:8080/index.php)

---

## Conexión e Infraestructura de Base de Datos

El archivo `docker-compose.yml` despliega una instancia de **Azure SQL Edge**, la cual es la versión optimizada de Microsoft SQL Server para una compatibilidad multiplataforma absoluta (funcionando de manera nativa y veloz tanto en Windows como en arquitecturas ARM/Apple Silicon).

### Credenciales de Conexión (Modo Local)
* **Servidor (Host):** `localhost,1433`
* **Usuario (User):** `SA`
* **Contraseña (Password):** `AbarrotesVilches2026!`
* **Base de Datos:** `AbarrotesVilchesDB`

---

## Cómo Poblar la Base de Datos

Para crear las tablas correspondientes a los requerimientos del sistema e insertar los datos iniciales de prueba, se debe ejecutar el archivo `database.sql` dentro del gestor.

### Opción A (Desde Windows con SSMS):
1. Abre SQL Server Management Studio.
2. Conéctate al servidor usando `localhost,1433`, autenticación de SQL Server, usuario `SA` y la contraseña indicada arriba.
3. Abre el archivo `database.sql` del repositorio, selecciónalo todo y haz clic en **Execute**.

### Opción B (Desde la terminal de Docker para cualquier S.O.):
Si no cuentas con un gestor gráfico, ejecuta este comando en tu terminal para inyectar el script directamente al contenedor:
```bash
docker exec -i abarrotes_db /opt/mssql-tools/bin/sqlcmd -S localhost -U SA -P "AbarrotesVilches2026!" -i /var/www/html/database.sql




