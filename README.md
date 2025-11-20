# Isla Transfers – Aplicación MVC en PHP

Aplicación web para la gestión de transfers aeropuerto–hotel desarrollada con **PHP (sin frameworks)** utilizando una **arquitectura MVC limpia**, rutas controladas por `index.php` y conexión a MySQL.

Este README explica cómo clonar, instalar y ejecutar correctamente el proyecto.

---

## 🚀 Funcionalidades principales

### 🔹 1. FrontEnd  
- Landing page informativa.  
- Interfaz clara y moderna.  
- Se muestran **descriptores (nombres)** y no IDs.  

### 🔹 2. Registro y Login  
- Registro de usuarios particulares y corporativos (hoteles).  
- Validación de errores.  
- Sesiones seguras.  
- Menús dinámicos según rol:  
  - **Admin**  
  - **Hotel**  
  - **Usuario particular**

### 🔹 3. Panel de Administración  
El administrador puede:  
- Crear, editar y eliminar **reservas**.  
- Añadir, modificar y eliminar **vehículos**.  
- Añadir, modificar y eliminar **hoteles/destinos**.  
- Ver reservas en **calendario por día, semana y mes**.  
- Ver estadísticas rápidas.

### 🔹 4. Panel Usuario Particular  
- Ve todas las reservas asociadas a su email.  
- Puede crear reservas igual que un admin.  
- **Regla obligatoria:**  
  No puede reservar si faltan menos de **48 horas**.  
- Puede editar o cancelar sus reservas **solo si faltan más de 48 h**.  
- Ve quién creó la reserva (él mismo o el administrador).

### 🔹 5. Perfil  
Todos los usuarios pueden modificar:  
- Nombre  
- Email  
- Contraseña  

### 🔹 6. Hoteles (Usuarios corporativos)  
- Ven solo las reservas asociadas a su hotel.  
- No pueden modificar el sistema.  

---

## 📦 Requisitos

Antes de ejecutar el proyecto, necesitas:

- PHP 8.x  
- MySQL 5.7 o superior  
- Apache/Nginx **o** Docker  
- Extensión PHP `pdo_mysql`

---

## 📥 Instalación del proyecto

### 🔹 1. Clonar el repositorio

```bash
git clone https://github.com/TU_REPO/isla_transfers_mvc.git
cd isla_transfers_mvc
```

---

## 🗄️ Base de datos

### 🔹 2. Importar la base de datos

En MySQL Workbench / Sequel Ace / phpMyAdmin:

1. Crear una BD llamada:  

```sql
CREATE DATABASE isla_transfer CHARACTER SET utf8mb4;
```

2. Importar el archivo incluido:  

```
database/isla_transfer.sql
```

---

## ⚙️ Configuración

### 🔹 3. Configurar `/app/config.php`

Edita:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root'); 
define('DB_NAME', 'isla_transfer');
```

---

## ▶️ Ejecutar el proyecto

### Opción A – Con PHP local

Desde el directorio del proyecto:

```bash
php -S localhost:8000 -t public
```

Abrir:  
👉 http://localhost:8000

---

### Opción B – Con Docker (opcional)

Si usas Docker Compose:

```bash
docker-compose up -d
```

---

## 🧪 Usuarios de prueba

### Admin  
```
email: admin@isla.com
pass: admin
```

### Usuario particular  
```
email: user@correo.com
pass: user
```

### Hotel  
```
email: hotel@hotel.com
pass: hotel
```

---

## 📁 Estructura del proyecto

```
app/
  Controllers/
  Core/
  Models/
  Views/
public/
  assets/
  index.php
database/
README.md
```

---

## 🙌 Git – Trabajo en equipo

Incluye:

- Crear repositorio  
- Clonar  
- Commit + push  
- Pull  
- Sincronización  

---

## 🎥 Vídeo demostrativo

Debe mostrarse:

✔ Login, registro y errores  
✔ Panel admin completo  
✔ CRUD reservas + hoteles + vehículos  
✔ Calendario  
✔ Panel usuario particular con regla 48h  
✔ Perfil  
✔ Git + AWS  

---

## ✔️ Proyecto listo

Si sigues estos pasos, podrás ejecutar el proyecto completo sin problemas.  
Cualquier duda, escríbeme 😊

---

## 📝 Autor

Proyecto creado por el equipo Isla Transfers (FP.064)

