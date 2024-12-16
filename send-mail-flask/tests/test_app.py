import os
import unittest
from unittest.mock import patch, mock_open, MagicMock
from flask import Flask
from app import app
import json

class TestSendEmail(unittest.TestCase):

    def setUp(self):
        self.app = app.test_client()
        self.app.testing = True

    @patch("builtins.open", new_callable=mock_open, read_data="<html><body>Hola [username], [message]</body></html>")
    @patch("smtplib.SMTP")
    def test_send_email_success(self, mock_smtp, mock_file):
        # Mock del servidor SMTP
        mock_server = MagicMock()
        mock_smtp.return_value.__enter__.return_value = mock_server

        # Datos a enviar en el correo
        payload = {
            "recipient": "test@example.com",
            "subject": "Bienvenido",
            "message": "Este es un mensaje de prueba",
            "username": "Usuario Test"
        }

        # Enviar solicitud POST al endpoint
        response = self.app.post(
            "/send-email",
            data=json.dumps(payload),
            content_type="application/json"
        )

        # Verificar respuesta
        self.assertEqual(response.status_code, 200)
        self.assertIn("Correo enviado exitosamente", response.get_data(as_text=True))

        # Verificar que se haya llamado al servidor SMTP
        mock_smtp.assert_called_once_with(os.getenv('SMTP_SERVER'), int(os.getenv('SMTP_PORT')))
        mock_server.starttls.assert_called_once()
        mock_server.login.assert_called_once_with(os.getenv('EMAIL_ADDRESS'), os.getenv('EMAIL_PASSWORD'))
        mock_server.sendmail.assert_called_once()

    @patch("builtins.open", new_callable=mock_open, read_data="<html><body>Hola [username], [message]</body></html>")
    def test_send_email_missing_fields(self, mock_file):
        # Datos incompletos
        payload = {
            "recipient": "",
            "subject": "",
            "message": "",
            "username": ""
        }

        # Enviar solicitud POST al endpoint
        response = self.app.post(
            "/send-email",
            data=json.dumps(payload),
            content_type="application/json"
        )

        # Verificar respuesta
        self.assertEqual(response.status_code, 400)
        self.assertIn("Faltan campos requeridos", response.get_data(as_text=True))

    @patch("builtins.open", side_effect=Exception("Error al abrir el archivo"))
    def test_send_email_template_error(self, mock_file):
        # Datos del correo
        payload = {
            "recipient": "test@example.com",
            "subject": "Bienvenido",
            "message": "Este es un mensaje de prueba",
            "username": "Usuario Test"
        }

        # Enviar solicitud POST al endpoint
        response = self.app.post(
            "/send-email",
            data=json.dumps(payload),
            content_type="application/json"
        )

        # Verificar respuesta
        self.assertEqual(response.status_code, 500)
        self.assertIn("Error al abrir el archivo", response.get_data(as_text=True))

if __name__ == "__main__":
    unittest.main()
