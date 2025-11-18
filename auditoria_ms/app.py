from flask import Flask, jsonify, request
from pymongo import MongoClient
from bson import ObjectId
from flask_cors import CORS
from dotenv import load_dotenv
import os
import datetime


API_KEY = os.getenv("API_KEY")


# Cargar variables de entorno (.env)
load_dotenv()

app = Flask(__name__)
CORS(app)

# Conectar a MongoDB
mongo_client = MongoClient(os.getenv("MONGO_URI"))
db = mongo_client[os.getenv("DB_NAME")]
logs_collection = db.auditoria

@app.before_request
def validar_api_key():
    key = request.headers.get("X-API-Key")
    if key != API_KEY:
        return jsonify({"error": "Acceso no autorizado"}), 401


# Registrar un nuevo evento (log)
@app.route('/logs', methods=['POST'])
def crear_log():
    try:
        data = request.get_json(force=True)

        if not data.get("accion") or not data.get("usuario"):
            return jsonify({"status": "error", "message": "Campos 'accion' y 'usuario' son requeridos"}), 400

        nuevo_log = {
            "accion": data["accion"],
            "usuario": data["usuario"],
            "fecha": datetime.datetime.now(),
            "detalles": data.get("detalles", {})
        }

        result = logs_collection.insert_one(nuevo_log)
        return jsonify({"status": "success", "message": "Log creado", "id": str(result.inserted_id)}), 201

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

#  Listar todos los logs
@app.route('/logs', methods=['GET'])
def listar_logs():
    try:
        logs = []
        for log in logs_collection.find():
            log["_id"] = str(log["_id"])
            logs.append(log)
        return jsonify({"status": "success", "data": logs}), 200

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500


# Obtener un log por ID
@app.route('/logs/<id>', methods=['GET'])
def obtener_log(id):
    try:
        log = logs_collection.find_one({"_id": ObjectId(id)})
        if log:
            log["_id"] = str(log["_id"])
            return jsonify({"status": "success", "data": log}), 200
        return jsonify({"status": "error", "message": "Log no encontrado"}), 404

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500
    
@app.route('/logs/usuario/<usuario>', methods=['GET'])
def filtrar_por_usuario(usuario):
    try:
        logs = []
        for log in logs_collection.find({"usuario": usuario}):
            log["_id"] = str(log["_id"])
            logs.append(log)

        if not logs:
            return jsonify({"status": "warning", "message": "No hay logs para este usuario"}), 404

        return jsonify({"status": "success", "data": logs}), 200

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500


# Eliminar un log
@app.route('/logs/<id>', methods=['DELETE'])
def eliminar_log(id):
    try:
        result = logs_collection.delete_one({"_id": ObjectId(id)})
        if result.deleted_count > 0:
            return jsonify({"status": "success", "message": "Log eliminado"}), 200
        return jsonify({"status": "error", "message": "Log no encontrado"}), 404

    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True, port=5003)
