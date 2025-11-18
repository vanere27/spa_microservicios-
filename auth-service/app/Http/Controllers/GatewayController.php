<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class GatewayController extends Controller
{
    protected $auth_url;
    protected $servicios_url;
    protected $auditoria_url;
    protected $notificaciones_url;
    protected $reportes_url;
    protected $reservas_url;
    protected $apiKey;
    protected $timeout;

    public function __construct()
    {
        // URLs hacia los microservicios (ajusta en .env)
        $this->auth_url = rtrim(env('AUTH_SERVICE_URL', 'http://localhost:8001/api'), '/');
        $this->servicios_url = rtrim(env('SERVICIOS_URL', 'http://localhost:8002/api'), '/');
        $this->auditoria_url = rtrim(env('AUDITORIA_URL', 'http://localhost:5003'), '/');
        $this->notificaciones_url = rtrim(env('NOTIFICACIONES_URL', 'http://localhost:5004'), '/');
        $this->reportes_url = rtrim(env('REPORTES_URL', 'http://localhost:5005'), '/');
        $this->reservas_url = rtrim(env('RESERVAS_URL', 'http://localhost:5001'), '/');

        // API key interna que el Gateway añade a todas las peticiones
        $this->apiKey = env('API_KEY', 'OnceCaldasQuerido');

        // Timeout en segundos para peticiones salientes
        $this->timeout = (int) env('OUTBOUND_TIMEOUT', 30);
    }

    /**
     * Método genérico para reenviar la petición al servicio indicado.
     *
     * - $method: 'get','post','put','patch','delete'
     * - $url: URL completa hacia el microservicio (ej: http://localhost:8001/api/login)
     * - $data: array con payload (body)
     */
    private function forwardRequest(string $method, string $url, array $data = [], Request $incoming = null)
    {
        try {
            // Construir headers a reenviar
            $headers = [
                'X-API-Key' => $this->apiKey,
                'Accept' => 'application/json',
            ];

            // Si el cliente envió Authorization, lo reenviamos (permite validar token con Sanctum)
            if ($incoming && $incoming->bearerToken()) {
                $headers['Authorization'] = 'Bearer ' . $incoming->bearerToken();
            }

            $client = Http::withHeaders($headers)->timeout($this->timeout);

            // Ejecutar según método
            switch (strtolower($method)) {
                case 'get':
                    $resp = $client->get($url);
                    break;
                case 'post':
                    $resp = $client->post($url, $data);
                    break;
                case 'put':
                    $resp = $client->put($url, $data);
                    break;
                case 'patch':
                    $resp = $client->patch($url, $data);
                    break;
                case 'delete':
                    // Algunos downstreams esperan body vacío para DELETE; Laravel acepta segundo arg opcional
                    $resp = $client->delete($url, $data ?: []);
                    break;
                default:
                    return response()->json(['message' => 'Método no permitido por Gateway'], 405);
            }

            // Reenviamos el contenido y el código de estado recibido
            $contentType = $resp->header('Content-Type', 'application/json');
            $status = $resp->status();

            if (str_contains(strtolower($contentType ?? ''), 'application/json')) {
                return response()->json($resp->json(), $status);
            }

            // Fallback: devolver body tal cual (por ejemplo para archivos o binarios)
            return response($resp->body(), $status)->header('Content-Type', $contentType);
        } catch (\Throwable $e) {
            Log::error("Gateway.forwardRequest error calling {$url} -> " . $e->getMessage());
            return response()->json([
                'message' => 'Error comunicándose con el microservicio',
                'error' => $e->getMessage()
            ], Response::HTTP_BAD_GATEWAY); // 502
        }
    }

    // ------------------- AUTH (reenvío a auth-service) -------------------
    public function auth_login(Request $req)
    {
        $url = $this->auth_url . '/login';
        return $this->forwardRequest('post', $url, $req->all(), $req);
    }

    public function auth_create_user(Request $req)
    {
        $url = $this->auth_url . '/create_user';
        return $this->forwardRequest('post', $url, $req->all(), $req);
    }

    public function auth_change_password(Request $req)
    {
        $url = $this->auth_url . '/change_password';
        return $this->forwardRequest('post', $url, $req->all(), $req);
    }

    public function auth_forgot(Request $req)
    {
        $url = $this->auth_url . '/forgot_password';
        return $this->forwardRequest('post', $url, $req->all(), $req);
    }

    public function auth_reset(Request $req)
    {
        $url = $this->auth_url . '/reset_password';
        return $this->forwardRequest('post', $url, $req->all(), $req);
    }

    // ------------------- SERVICIOS -------------------
    public function servicios_index(Request $req)
    {
        $url = $this->servicios_url . '/servicios';
    
        if ($q = $req->getQueryString()) $url .= '?' . $q;
        return $this->forwardRequest('get', $url, [], $req);
    }

    public function servicios_store(Request $req)
    {
        $url = $this->servicios_url . '/servicios';
        return $this->forwardRequest('post', $url, $req->all(), $req);
    }

    public function servicios_update(Request $req, $id)
    {
        $url = $this->servicios_url . '/servicios/' . $id;
        return $this->forwardRequest('put', $url, $req->all(), $req);
    }

    public function servicios_delete(Request $req, $id)
    {
        $url = $this->servicios_url . '/servicios/' . $id;
        return $this->forwardRequest('delete', $url, [], $req);
    }

    // ------------------- RESERVAS  -------------------
    public function reservas_index(Request $req)
    {
        $url = $this->reservas_url . '/reservas';
        if ($q = $req->getQueryString()) $url .= '?' . $q;
        return $this->forwardRequest('get', $url, [], $req);
    }

    public function reservas_store(Request $req)
    {
        $url = $this->reservas_url . '/reservas';
        return $this->forwardRequest('post', $url, $req->all(), $req);
    }

    // ------------------- REPORTES -------------------
    public function reportes_excel(Request $req)
    {
        $url = $this->reportes_url . '/excel';
        if ($q = $req->getQueryString()) $url .= '?' . $q;
        return $this->forwardRequest('get', $url, [], $req);
    }

    public function reportes_pdf(Request $req)
    {
        $url = $this->reportes_url . '/pdf';
        if ($q = $req->getQueryString()) $url .= '?' . $q;
        return $this->forwardRequest('get', $url, [], $req);
    }

    // ------------------- AUDITORÍA (Flask) -------------------
    public function auditoria_index(Request $req)
    {
        $url = $this->auditoria_url . '/auditoria';
        if ($q = $req->getQueryString()) $url .= '?' . $q;
        return $this->forwardRequest('get', $url, [], $req);
    }

    // ------------------- NOTIFICACIONES (Flask) -------------------
    public function notificaciones_send(Request $req)
    {
        $url = $this->notificaciones_url . '/send';
        return $this->forwardRequest('post', $url, $req->all(), $req);
    }
}
