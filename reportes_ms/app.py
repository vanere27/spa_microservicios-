from flask import Flask, jsonify, request, send_file
from pymongo import MongoClient
from bson import ObjectId
from flask_cors import CORS
from dotenv import load_dotenv
import os
import datetime
import pandas as pd
from reportlab.pdfgen import canvas
from io import BytesIO

load_dotenv()

app = Flask(__name__)
CORS(app)


mongo_client = MongoClient(os.getenv("MONGO_URI"))
db = mongo_client[os.getenv("DB_NAME")]
reportes_collection = db.reportes


@app.route('/reportes', methods=['POST'])
def crear_reporte():
    data = request.get_json()
    nuevo_reporte = {
        "tipo": data.get("tipo", "general"),
        "fecha": datetime.datetime.now(),
        "descripcion": data.get("descripcion", ""),
        "resultado": data.get("resultado", {}),
    }
    result = reportes_collection.insert_one(nuevo_reporte)
    return jsonify({"mensaje": "Reporte creado correctamente", "id": str(result.inserted_id)}), 201



@app.route('/reportes', methods=['GET'])
def listar_reportes():
    reportes = []
    for r in reportes_collection.find():
        r["_id"] = str(r["_id"])
        reportes.append(r)
    return jsonify(reportes)



@app.route('/reportes/<id>', methods=['GET'])
def obtener_reporte(id):
    reporte = reportes_collection.find_one({"_id": ObjectId(id)})
    if reporte:
        reporte["_id"] = str(reporte["_id"])
        return jsonify(reporte)
    return jsonify({"mensaje": "Reporte no encontrado"}), 404



@app.route('/reportes/generar', methods=['GET'])
def generar_reporte_simulado():
    datos = {
        "servicio_mas_solicitado": "Masaje relajante",
        "reservas_mes": 123,
        "ingresos_mensuales": 5400000
    }
    nuevo_reporte = {
        "tipo": "estadístico",
        "fecha": datetime.datetime.now(),
        "descripcion": "Reporte automático generado con datos simulados",
        "resultado": datos
    }
    result = reportes_collection.insert_one(nuevo_reporte)
    return jsonify({"mensaje": "Reporte simulado creado", "id": str(result.inserted_id)})


@app.route('/reportes/<id>/pdf', methods=['GET'])
def exportar_pdf(id):
    reporte = reportes_collection.find_one({"_id": ObjectId(id)})
    if not reporte:
        return jsonify({"mensaje": "Reporte no encontrado"}), 404


    buffer = BytesIO()
    pdf = canvas.Canvas(buffer)
    pdf.setTitle("Reporte Spa")

    pdf.drawString(100, 800, f"REPORTE SPA - {reporte.get('tipo', '').upper()}")
    pdf.drawString(100, 780, f"Fecha: {reporte.get('fecha').strftime('%Y-%m-%d %H:%M')}")
    pdf.drawString(100, 760, f"Descripción: {reporte.get('descripcion', '')}")

    y = 730
    pdf.drawString(100, y, "Resultados:")
    y -= 20
    for k, v in reporte.get("resultado", {}).items():
        pdf.drawString(120, y, f"- {k}: {v}")
        y -= 20

    pdf.showPage()
    pdf.save()
    buffer.seek(0)

    return send_file(buffer, as_attachment=True, download_name="reporte.pdf", mimetype='application/pdf')


@app.route('/reportes/<id>/excel', methods=['GET'])
def exportar_excel(id):
    reporte = reportes_collection.find_one({"_id": ObjectId(id)})
    if not reporte:
        return jsonify({"mensaje": "Reporte no encontrado"}), 404

    df = pd.DataFrame(list(reporte.get("resultado", {}).items()), columns=["Indicador", "Valor"])

 
    buffer = BytesIO()
    with pd.ExcelWriter(buffer, engine='xlsxwriter') as writer:
        df.to_excel(writer, index=False, sheet_name='Reporte')
    buffer.seek(0)

    return send_file(buffer, as_attachment=True, download_name="reporte.xlsx", mimetype='application/vnd.ms-excel')

#las  siguientes rutas son solo para hacer la prueba sin tener en cuenta el id 

@app.route('/reportes/pdf', methods=['GET'])
def generar_pdf_general():
    buffer = BytesIO()
    pdf = canvas.Canvas(buffer)
    pdf.drawString(100, 800, "REPORTE GENERAL DE PRUEBA - PDF")
    pdf.showPage()
    pdf.save()
    buffer.seek(0)
    return send_file(buffer, as_attachment=True, download_name="reporte_prueba.pdf", mimetype='application/pdf')


@app.route('/reportes/excel', methods=['GET'])
def generar_excel_general():
    df = pd.DataFrame([
        {"Indicador": "Reservas", "Valor": 100},
        {"Indicador": "Ingresos", "Valor": 5000000}
    ])
    buffer = BytesIO()
    with pd.ExcelWriter(buffer, engine='xlsxwriter') as writer:
        df.to_excel(writer, index=False, sheet_name='Reporte')
    buffer.seek(0)
    return send_file(buffer, as_attachment=True, download_name="reporte_prueba.xlsx", mimetype='application/vnd.ms-excel')



if __name__ == '__main__':
    app.run(debug=True, port=5002)
