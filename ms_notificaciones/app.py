from flask import Flask, request, jsonify
from flask_cors import CORS
from flask_mail import Mail, Message
import os
API_KEY = os.getenv("API_KEY")


app = Flask(__name__)
CORS(app)

# === CONFIGURACIÓN DEL SERVIDOR SMTP ===
app.config['MAIL_SERVER'] = 'smtp.gmail.com'
app.config['MAIL_PORT'] = 587
app.config['MAIL_USE_TLS'] = True
app.config['MAIL_USERNAME'] = 'spadeunas376@gmail.com'  # tu correo Gmail
app.config['MAIL_PASSWORD'] = 'qnno lcyy xjwd rncx'  # la contraseña de aplicación
app.config['MAIL_DEFAULT_SENDER'] = ('Notificaciones VitalCare', 'spadeunas376@gmail.com')

mail = Mail(app)

@app.before_request
def validar_api_key():
    key = request.headers.get("X-API-Key")
    if key != API_KEY:
        return jsonify({"error": "Acceso no autorizado"}), 401
@app.route('/')
def home():
    return jsonify({'message': 'Microservicio de notificaciones activo 🚀'})

@app.route('/enviar', methods=['POST'])
def enviar_correo():
    try:
        data = request.json
        destinatario = data.get('destinatario')
        asunto = data.get('asunto')
        mensaje = data.get('mensaje')

        if not destinatario or not asunto or not mensaje:
            return jsonify({'error': 'Faltan campos obligatorios'}), 400

        msg = Message(asunto, recipients=[destinatario])
        msg.body = mensaje
        mail.send(msg)

        return jsonify({'status': 'Correo enviado exitosamente ✅'}), 200

    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(port=5004, debug=True)
