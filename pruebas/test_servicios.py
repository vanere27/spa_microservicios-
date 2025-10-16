from locust import HttpUser, task, between

class ServicioUser(HttpUser):
    wait_time = between(1, 5)  # tiempo entre tareas en segundos

    @task(2)
    def listar_servicios(self):
        self.client.get("/api/servicios/")

    @task(1)
    def crear_servicio(self):
        data = {
            "nombre": "Manicure clásico",
            "descripcion": "Servicio básico de manicure",
            "precio": "25000.00",
            "duracion": 30,
            "estado": True
        }
        self.client.post("/api/servicios/", json=data)

    @task(1)
    def ver_servicio(self):
        self.client.get("/api/servicios/1/")  # ajusta el ID si es necesario

    @task(1)
    def actualizar_servicio(self):
        data = {
            "nombre": "Manicure Deluxe",
            "descripcion": "Manicure con exfoliación e hidratación",
            "precio": "40000.00",
            "duracion": 45,
            "estado": True
        }
        self.client.put("/api/servicios/1/", json=data)

    @task(1)
    def eliminar_servicio(self):
        self.client.delete("/api/servicios/5/")  
