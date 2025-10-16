from locust import HttpUser, task, between
import random
import datetime

class ReservasUser(HttpUser):
    wait_time = between(1, 3)  # tiempo entre tareas por usuario (simula uso real)

    # Datos base
    servicios = ["Manicure", "Pedicure", "Uñas Acrílicas", "Spa de manos"]
    empleados = ["Laura", "Diana", "Camila", "Andrea"]
    clientes = ["Vanessa", "Lucía", "Sara", "Paula", "Natalia"]

    @task(2)
    def listar_reservas(self):
        """Simula que el usuario consulta todas las reservas"""
        self.client.get("/reservas")

    @task(3)
    def crear_reserva(self):
        """Crea una nueva reserva con datos aleatorios"""
        fecha = datetime.date.today().strftime("%Y-%m-%d")
        hora = f"{random.randint(9, 17)}:00"

        data = {
            "cliente": random.choice(self.clientes),
            "servicio": random.choice(self.servicios),
            "fecha": fecha,
            "hora": hora,
            "empleado": random.choice(self.empleados),
            "estado": "pendiente"
        }

        response = self.client.post("/reservas", json=data)
        if response.status_code == 201:
            reserva_id = response.json().get("id")
            # Guarda el id en memoria para otras tareas si lo deseas
            self.last_reserva = reserva_id

    @task(1)
    def obtener_reserva(self):
        
        # Solo si hay una reserva previa creada
        if hasattr(self, "last_reserva"):
            self.client.get(f"/reservas/{self.last_reserva}")

    @task(1)
    def eliminar_reserva(self):
        """Elimina una reserva existente"""
        if hasattr(self, "last_reserva"):
            self.client.delete(f"/reservas/{self.last_reserva}")
