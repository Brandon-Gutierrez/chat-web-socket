# Chat Web Socket 

Sistema de chat en tiempo real con autenticación OAuth de Google y WebSockets nativos de Laravel.

---

## Inicio Rápido

### Requisitos
- Docker Desktop (WSL 2 en Windows)
- Git
- Terminal Bash/WSL

### Instalación (5 minutos)

```bash
# 1. Clonar
git clone https://github.com/Brandon-Gutierrez/chat-web-socket.git
cd chat-web-socket

# 2. Copiar .env
cp .env.example .env

# 3. Instalar PHP (Docker)
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd)":/opt -w /opt \
    laravelsail/php83-composer:latest composer install --ignore-platform-reqs

# 4. Levantar contenedores
./vendor/bin/sail up -d

# 5. BD
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate

# 6. Instalar dependencias frontend
npm install

# 7. Ejecutar (abre 3 terminales)
# Terminal 1: WebSocket server
./vendor/bin/sail artisan reverb:start

# Terminal 2: Servidor Laravel
./vendor/bin/sail artisan serve --host=0.0.0.0

# Terminal 3: frontend
npm run dev
```

**O todo junto:**
```bash
composer run dev  # Ejecuta todos los servicios simultáneamente
```

---

## Stack

| Componente | Versión | Puerto |
|-----------|---------|--------|
| PHP | 8.3/8.4 | 8000 |
| PostgreSQL 16 | - | 5434 (ext) |
| Reverb WebSocket | - | 8080 |
| Vite Dev Server | - | 5173 |

---

## Base de Datos

**Conexión Externa** (Navicat, DBeaver, pgAdmin):
```
Host: 127.0.0.1
Puerto: 5434
BD: chat-web-socket
Usuario: sail
Contraseña: password
```

---

## Comandos Frecuentes

```bash
./vendor/bin/sail shell                    # Entrar al contenedor
./vendor/bin/sail artisan make:model Nom   # Crear modelo
./vendor/bin/sail logs -f                  # Ver logs 
./vendor/bin/sail down                     # Detener contenedores
./vendor/bin/sail up -d                    # Reiniciar
```

---

## Solución de Problemas

**Error de permisos (EACCES):**
```bash
sudo chown -R $USER:$USER .
```

**Contenedores no inician / "pgsql: unknown host":**
```bash
./vendor/bin/sail down && ./vendor/bin/sail up -d
```

**Puerto 5434 ocupado:**
Cambiar `FORWARD_DB_PORT` en `.env`

---
.
