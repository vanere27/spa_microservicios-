from locust import HttpUser, task, between

class ReportesUser(HttpUser):
    wait_time = between(1, 3)

    @task
    def generar_reporte(self):
        self.client.get("/reportes/generar")

    @task
    def listar_reportes(self):
        self.client.get("/reportes")
