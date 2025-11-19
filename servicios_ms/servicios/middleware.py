from django.http import JsonResponse
import os

class ApiKeyMiddleware:
    def __init__(self, get_response):
        self.get_response = get_response
        self.api_key = os.getenv('API_KEY')  # La API KEY que debe venir en .env

    def __call__(self, request):
        api_key = request.headers.get('X-API-Key')  # Obtiene la API KEY enviada por el Gateway

        # Compara correctamente la API KEY del request vs la del .env
        if api_key != self.api_key:
            return JsonResponse({'error': 'Acceso no autorizado'}, status=401)

        return self.get_response(request)
