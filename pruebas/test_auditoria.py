from locust import HttpUser, task, between

class AuditoriaUser(HttpUser):
    wait_time = between(1, 2)

    @task(2)
    def listar_logs(self):
        self.client.get("/logs")

    @task(1)
    def crear_log(self):
        data = {
            "accion": "Prueba de carga",
            "usuario": "tester",
            "detalles": {"prueba": "locust"}
        }
        self.client.post("/logs", json=data)
