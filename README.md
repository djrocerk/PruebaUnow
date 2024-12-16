# PruebaUnow
 
# 🚀 Guía de Instalación de Proyectos

Este documento te guiará paso a paso para instalar y ejecutar los proyectos de React, Symfony y el servicio de Email en Flask.


## ⚛️ **Proyecto React**

### 1. Instalar dependencias
- npm install

### 2. Iniciar el servidor
- npm start

## 🖥️ **Proyecto Symfony**

### 1. Instalar dependencias
- composer install

### 2 .Configuración de la base de datos
- Abre el archivo .env <br>
- Configura la variable DATABASE_URL con los datos locales de tu base de datos.

   DATABASE_URL="mysql://usuario:contraseña@127.0.0.1:3306/nombre_bd"

### 3. Crea y ejecuta migraciones de la BD
- php bin/console make:migration <br>
- php bin/console doctrine:migrations:migrate


### 4. Iniciar el servidor
- php -S 127.0.0.1:8001 -t public


## 📧 **Servicio de Email en Flask**

### 1. Crear el entorno virtual
- python -m venv venv

### 2. Activar el entorno virtual
- venv\Scripts\activate

### 3. Instalar dependencias
- pip install -r requirements.txt

### 4.Configurar variables de entorno
- Abre el archivo .env.
- Configura la variable EMAIL_ADDRESS con la dirección de tu cuenta de email.
- Configura la variable EMAIL_PASSWORD con la contraseña de tu cuenta de email.

![alt text](image.png)

### 4. Iniciar el servidor
- python app.py





