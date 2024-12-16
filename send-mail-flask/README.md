# Crear entorno de Python

```bash
# Crear el entorno:
python -m venv venv

# Activar el entorno:
venv\Scripts\activate

# Desactivar el entorno:
venv\Scripts\deactivate
```

# Instalación de paquetes
[requirements](requirements.txt)

```bash
pip install -r requirements.txt
```

## Variables de entorno
Para modificar las variables de entorno dirigete a [.env](.env)

```bash
SMTP_SERVER='smtp.gmail.com'
SMTP_PORT=587
EMAIL_ADDRESS=''
EMAIL_PASSWORD=''
FLASK_PORT=8000
```

## Comando para iniciar el servicio

```bash
python app.py
```

## Comando para ejecutar los test unitarios

```bash
python -m unittest tests/test_app.py
```

## Ejemplo de cómo usar el endpoint para envio de emails

```bash
POST /send-email HTTP/1.1
Host: 127.0.0.1:8000
Content-Type: application/json

# Body

{
  "recipient": "correoprueba@gmail.com",
  "subject": "Prueba de asunto",
  "message": "¡Hola! es un placer contar con tu talento en nuestro equipo.",
  "username": "Roberto"
}
```