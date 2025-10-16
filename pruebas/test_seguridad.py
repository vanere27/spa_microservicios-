from locust import HttpUser, task, between
import random

class AuthUser(HttpUser):
    wait_time = between(1, 3)

    def on_start(self):
        # Datos base de usuarios simulados
        self.users = [
            {"name": "Vanessa", "email": "vanessa@example.com", "password": "123456", "role": "Cliente"},
            {"name": "Diana", "email": "diana@example.com", "password": "123456", "role": "Empleado"},
            {"name": "Admin", "email": "admin@example.com", "password": "admin123", "role": "Administrador"}
        ]
        self.token = None

    @task(1)
    def registrar_usuario(self):
        """Simula el registro de un nuevo usuario"""
        nuevo = {
            "name": f"User{random.randint(1,9999)}",
            "email": f"user{random.randint(1,9999)}@test.com",
            "password": "123456",
            "role": random.choice(["Cliente", "Empleado"])
        }
        self.client.post("/api/register", json=nuevo)

    @task(3)
    def login(self):
        """Simula el login de usuarios registrados"""
        usuario = random.choice(self.users)
        response = self.client.post("/api/login", json={
            "email": usuario["email"],
            "password": usuario["password"]
        })
        if response.status_code == 200:
            self.token = response.json().get("access_token")

    @task(2)
    def cambiar_password(self):
        """Simula el cambio de contraseña (requiere token)"""
        if self.token:
            headers = {"Authorization": f"Bearer {self.token}"}
            self.client.put("/api/change-password", json={
                "current_password": "123456",
                "new_password": "654321"
            }, headers=headers)

    @task(1)
    def logout(self):
        """Simula el cierre de sesión (requiere token)"""
        if self.token:
            headers = {"Authorization": f"Bearer {self.token}"}
            self.client.post("/api/logout", headers=headers)
            self.token = None
