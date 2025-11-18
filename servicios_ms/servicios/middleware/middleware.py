from django.http import JsonResponse
import os

class ApiKeyMiddleware:
    def __init__(self, get_response):
        self.get_response = get_response
        self.api_key = os.getenv('API_KEY')

    def __call__(self, request):
        key = request.headers.get('X-API-Key')

        if key != self.api_key:
            return JsonResponse({'error': 'Acceso no autorizado'}, status=401)

        return self.get_response(request)
