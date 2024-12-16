from flask import Flask, request, jsonify
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from dotenv import load_dotenv
from flask_cors import CORS
import os

load_dotenv()
app = Flask(__name__)


SMTP_SERVER = os.getenv("SMTP_SERVER")
SMTP_PORT = int(os.getenv("SMTP_PORT"))
EMAIL_ADDRESS = os.getenv("EMAIL_ADDRESS")
EMAIL_PASSWORD = os.getenv("EMAIL_PASSWORD")
CORS(app)

@app.route("/send-email", methods=["POST"])
def send_email():
    try:
        # Datos del correo
        data = request.json
        recipient = data.get("recipient")
        subject = data.get("subject")
        message = data.get("message")
        nombre = data.get("nombre")
        apellido = data.get("apellido")

        if not recipient or not subject or not message or not nombre or not apellido:
            return jsonify({"error": "Faltan campos requeridos"}), 400

        with open("template_email.html", "r", encoding="utf-8") as file:
            html_content = file.read()

        html_content = html_content.replace("[nombre]", nombre)
        html_content = html_content.replace("[apellido]", apellido)
        html_content = html_content.replace("[message]", message)

    
        msg = MIMEMultipart()
        msg["From"] = EMAIL_ADDRESS
        msg["To"] = recipient
        msg["Subject"] = subject
        msg.attach(MIMEText(html_content, "html"))

        # Conectar al servidor SMTP y enviar el correo
        with smtplib.SMTP(SMTP_SERVER, SMTP_PORT) as server:
            server.starttls()
            server.login(EMAIL_ADDRESS, EMAIL_PASSWORD)
            server.sendmail(EMAIL_ADDRESS, recipient, msg.as_string())

        return jsonify({"message": "Correo enviado exitosamente"}), 200
    except Exception as e:
        return jsonify({"error": str(e)}), 500


if __name__ == "__main__":
    port = int(os.getenv("FLASK_PORT", 5000))
    app.run(debug=True, port=port)
