from flask import Flask, jsonify, request
from pymongo import MongoClient
from bson import ObjectId
from dotenv import load_dotenv
from flask_cors import CORS
import datetime
import os
import pandas as pd
from reportlab.pdfgen import canvas




API_KEY = os.getenv("API_KEY")


app = Flask(__name__)
CORS(app)
load_dotenv()

mongo_client = MongoClient(os.getenv("MONGO_URI"))
db = mongo_client[os.getenv("DB_NAME")]
reservas_collection = db.reservas

@app.before_request
def validar_api_key():
    key = request.headers.get("X-API-Key")
    if key != API_KEY:
        return jsonify({"error": "Acceso no autorizado"}), 401


# Ruta de prueba
@app.route("/", methods=["GET"])
def home():
    return jsonify({"message": "Microservicio de Reservas - SPA de uñas funcionando"})


# Crear una reserva
@app.route("/reservas", methods=["POST"])
def crear_reserva():
    """
    Ejemplo:
    {
        "cliente": "Vanessa Restrepo",
        "servicio": "Manicure",
        "fecha": "2025-10-15",
        "hora": "10:00",
        "empleado": "Laura",
        "estado": "pendiente"
    }
    """
    data = request.get_json()

    nueva_reserva = {
        "cliente": data["cliente"],
        "servicio": data["servicio"],
        "fecha": data["fecha"],
        "hora": data["hora"],
        "empleado": data["empleado"],
        "estado": data.get("estado", "pendiente"),
        "creada_en": datetime.datetime.now()
    }

    resultado = reservas_collection.insert_one(nueva_reserva)
    return jsonify({"mensaje": "Reserva creada correctamente", "id": str(resultado.inserted_id)}), 201


# Listar todas las reservas
@app.route("/reservas", methods=["GET"])
def listar_reservas():
    reservas = []
    for r in reservas_collection.find():
        r["_id"] = str(r["_id"])  # con esto convierto ObjectId a string para enviarlo en JSON
        reservas.append(r)
    return jsonify(reservas)


# Consultar una reserva por ID
@app.route("/reservas/<id>", methods=["GET"])
def obtener_reserva(id):
    reserva = reservas_collection.find_one({"_id": ObjectId(id)})
    if reserva:
        reserva["_id"] = str(reserva["_id"])
        return jsonify(reserva)
    else:
        return jsonify({"error": "Reserva no encontrada"}), 404


# Actualizar una reserva
@app.route("/reservas/<id>", methods=["PUT"])
def actualizar_reserva(id):
    data = request.get_json()
    actualizacion = {
        "$set": {
            "cliente": data.get("cliente"),
            "servicio": data.get("servicio"),
            "fecha": data.get("fecha"),
            "hora": data.get("hora"),
            "empleado": data.get("empleado"),
            "estado": data.get("estado")
        }
    }

    resultado = reservas_collection.update_one({"_id": ObjectId(id)}, actualizacion)
    if resultado.matched_count > 0:
        return jsonify({"mensaje": "Reserva actualizada correctamente"})
    else:
        return jsonify({"error": "Reserva no encontrada"}), 404


# Eliminar una reserva
@app.route("/reservas/<id>", methods=["DELETE"])
def eliminar_reserva(id):
    resultado = reservas_collection.delete_one({"_id": ObjectId(id)})
    if resultado.deleted_count > 0:
        return jsonify({"mensaje": "Reserva eliminada correctamente"})
    else:
        return jsonify({"error": "Reserva no encontrada"}), 404


#ejecuto serv
if __name__ == "__main__":
    app.run(debug=True, port=5001)
