# Sistema de Control de Inventario y Ventas - Abarrotes Vilches

Este proyecto es un sistema de gestión web desarrollado en **PHP** utilizando **Microsoft SQL Server** como sistema gestor de base de datos. El entorno completo está contenerizado con **Docker**, lo que garantiza que el sistema funcione exactamente igual en cualquier sistema operativo (Windows y macOS), incluyendo la instalación automatizada de los controladores ODBC de Microsoft.

---

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente en tu equipo:
* [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Debe estar encendido).
* [Visual Studio Code](https://code.visualstudio.com/).
* [Git](https://git-scm.com/).
* *Opcional para Windows:* [SQL Server Management Studio (SSMS)](https://learn.microsoft.com/es-es/sql/ssms/download-sql-server-management-studio-ssms).

---

## Mapeo de Requerimientos Funcionales (RF)

El sistema ha sido modularizado para cumplir estrictamente con la matriz de requerimientos del proyecto. A continuación se detalla qué archivo controla cada funcionalidad:

| Módulo | Archivo PHP | Requerimientos Cumplidos |
| :--- | :--- | :--- |
| **Productos** | `index.php` | **RF_01:** Agregar Producto<br>**RF_02:** Consultar / Buscar<br>**RF_03:** Modificar Datos<br>**RF_04:** Baja Lógica (Cambio de estatus) |
| **Inventario** | `inventario.php` | **RF_05:** Registrar Entrada<br>**RF_06:** Registrar Salida (Validando stock físico)<br>**RF_07:** Consultar Existencias |
| **Punto de Venta** | `nueva_venta.php` | **RF_08:** Registrar Venta (Cálculo automático y validación de stock) |
| **Historial Ventas**| `ventas.php` | **RF_09:** Consultar Historial de tickets<br>**RF_10:** Cancelar Venta |
| **Empleados** | `empleados.php` | **RF_11:** Registrar Empleado<br>**RF_12:** Consultar / Buscar<br>**RF_13:** Actualizar Datos<br>**RF_14:** Baja Lógica |
| **Mermas** | `mermas.php` | **RF_15:** Registrar Producto Dañado/Caducado<br>**RF_16:** Consultar Mermas<br>**RF_17:** Anular Registro de Merma |
| **Reportes** | `reportes.php` | **RF_18:** Generar Reporte de Ingresos<br>**RNF_04:** Generar Respaldo de Base de Datos (.BAK) |
| **Categorías** | `categorias.php` | **RF_19:** Agregar Categoría<br>**RF_20:** Consultar Categorías<br>**RF_21:** Actualizar Datos<br>**RF_22:** Baja Lógica |

---

## Estructura del Repositorio

```text
├── Dockerfile             # Configuración para instalar PHP con drivers de SQL Server
├── docker-compose.yml     # Orquestación de los contenedores (Servidor Web + BD)
├── database.sql           # Script de creación y población de la BD
├── conexion.php           # Clase PDO para conexión con SQL Server
├── login.php              # Pantalla de acceso y control de roles
├── index.php              # Catálogo de Productos (Pantalla Principal)
├── categorias.php         # Gestión de Categorías
├── inventario.php         # Control de Entradas y Salidas
├── ventas.php             # Historial de Ventas
├── nueva_venta.php        # Punto de Venta operativo (Cajero)
├── mermas.php             # Registro de Pérdidas
├── empleados.php          # Directorio de Usuarios
└── reportes.php           # Módulo de Inteligencia de Negocio y Respaldo
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




