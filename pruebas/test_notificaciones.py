from locust import HttpUser, task, between
import random
import string
import json

class NotificacionesUser(HttpUser):
    wait_time = between(1, 3)  

    @task
    def enviar_correo(self):
        
        destinatario = "tucorreo@gmail.com"  
        asunto = "Prueba automática Locust"
        mensaje = ''.join(random.choices(string.ascii_letters, k=50))  
        payload = {
            "destinatario": destinatario,
            "asunto": asunto,
            "mensaje": mensaje
        }

        headers = {'Content-Type': 'application/json'}
        self.client.post("/enviar", data=json.dumps(payload), headers=headers)
