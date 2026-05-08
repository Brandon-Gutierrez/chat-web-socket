# Chat Web Socket - Laravel 12 & PostgreSQL 16

Este proyecto es un sistema de chat en tiempo real desarrollado como parte de una investigación en Ingeniería de Sistemas. El stack está completamente contenedorizado para garantizar que todos los desarrolladores trabajen sobre el mismo entorno exacto.

---

## Requisitos Previos

Antes de iniciar, asegúrate de tener instalado:
1. **Docker Desktop** (con soporte para WSL 2 en Windows).
2. **WSL 2** (Ubuntu recomendado) para un rendimiento óptimo de archivos.
3. **Git**.

---

## Guía de Instalación 

Sigue estos pasos en tu terminal (Bash/WSL) para levantar el proyecto:

1. Clonar el Repositorio

```bash
git clone [https://github.com/brandon-gutierrez/chat-web-socket.git](https://github.com/brandon-gutierrez/chat-web-socket.git)
cd chat-web-socket

2. Crear el Archivo de Entorno

bash
cp .env.example .env
## Importante: El archivo .env contiene las credenciales de la base de datos y llaves de seguridad. Nunca se sube al repositorio.

3. Instalación de Dependencias (Sin PHP local)
##Ejecuta este comando para que un contenedor temporal de Docker instale las librerías de PHP. Esto evita conflictos de versiones en tu máquina host:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd)":/opt \
    -w /opt \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs

4. Levantar el Entorno con Laravel Sail
##Sail es un envoltorio (wrapper) de Docker Compose. Levanta todos los microservicios (PHP, Postgres, Redis):

```bash
./vendor/bin/sail up -d

5. Configuración de la Aplicación
##Una vez los contenedores estén activos (healthy), ejecuta:

```bash
# Generar la llave única de la aplicación
./vendor/bin/sail artisan key:generate

# Correr las migraciones para crear las tablas en PostgreSQL
./vendor/bin/sail artisan migrate

6. WebSockets (Tiempo Real)
##Para que la mensajería funcione instantáneamente, utilizamos Laravel Reverb. Debes mantener una terminal abierta con el servidor de sockets activo:

```bash
./vendor/bin/sail artisan reverb:start

7. Arquitectura y Conexiones
##Mapeo de Puertos
###Para conectar herramientas externas (Navicat, DBeaver, pgAdmin), utiliza:

Host: 127.0.0.1 o localhost
Puerto Externo: 5434 (mapeado al 5432 interno)
Base de Datos: chat-web-socket
Usuario: sail
Contraseña: password

8. Comandos Frecuentes

Acción	Comando
Entrar al shell del contenedor	./vendor/bin/sail shell
Crear un modelo	./vendor/bin/sail artisan make:model Nombre
Ver logs en tiempo real	./vendor/bin/sail logs -f
Detener contenedores	./vendor/bin/sail down

9. Solución de Problemas Comunes
----------------------------------------------------------------------
Error de Permisos (EACCES):
##Si no puedes editar archivos desde VS Code, ejecuta en tu terminal:
sudo chown -R $USER:$USER .
----------------------------------------------------------------------
Error "pgsql: unknown host":
##Asegúrate de que los contenedores estén encendidos con ./vendor/bin/sail up -d. Si persiste, reinicia con ./vendor/bin/sail down && ./vendor/bin/sail up -d
##Conflicto de Puertos:
##Si el puerto 5434 está ocupado, cámbialo en la variable FORWARD_DB_PORT dentro de tu archivo .env.

10. Stack

Framework: Laravel 12.x
Runtime: PHP 8.3/8.4 (Dockerizado)
DB: PostgreSQL 16
Real-time: Laravel Reverb (WebSockets)
