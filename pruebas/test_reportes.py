from locust import HttpUser, task, between

class ReportesUser(HttpUser):
    wait_time = between(1, 3)  
    @task
    def generar_pdf(self):
        
        self.client.get("/reportes/pdf", name="Generar Reporte PDF")

    @task
    def generar_excel(self):
        self.client.get("/reportes/excel", name="Generar Reporte Excel")
