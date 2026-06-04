-- 1. Nos movemos a la base de datos principal por seguridad
USE master;
GO

-- 2. Quitamos a cualquier usuario o sistema conectado temporalmente (para que nos deje sobreescribir)
ALTER DATABASE AbarrotesVilchesDB SET SINGLE_USER WITH ROLLBACK IMMEDIATE;
GO

-- 3. Inyectamos el respaldo reemplazando lo que haya actualmente
RESTORE DATABASE AbarrotesVilchesDB
FROM DISK = '/var/opt/mssql/data/AbarrotesVilches_Respaldo_20260603_173237.bak'
WITH REPLACE;
GO

-- 4. Volvemos a abrir las puertas para que el sistema web pueda conectarse
ALTER DATABASE AbarrotesVilchesDB SET MULTI_USER;
GO