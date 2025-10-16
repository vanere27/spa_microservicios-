from locust import HttpUser, task, between
import random

class AuthUser(HttpUser):
    wait_time = between(1, 3)



    @task
    def create_user(self):
        if not self.token:
            return
        headers = {"Authorization": f"Bearer {self.token}"}
        data = {
            "name": f"User{random.randint(100,999)}",
            "email": f"user{random.randint(1000,9999)}@mail.com",
            "password": "123456",
            "role_id": 1
        }
        self.client.post("/api/create_user", json=data, headers=headers)

    
    def on_start(self): 
        # Login inicial
        response = self.client.post("/api/login", json={
            "email": "admin@mail.com",
            "password": "123456"
        })
        if response.status_code == 200:
            self.token = response.json().get("access_token")
        else:
            self.token = None

    @task
    def change_password(self):
        if not self.token:
            return
        headers = {"Authorization": f"Bearer {self.token}"}
        data = {
            "old_password": "123456",
            "new_password": "654321",
            "confirm_password": "654321"
        }
        self.client.post("/api/change_password", json=data, headers=headers)

    @task
    def logout(self):
        if not self.token:
            return
        headers = {"Authorization": f"Bearer {self.token}"}
        self.client.post("/api/logout", headers=headers)
