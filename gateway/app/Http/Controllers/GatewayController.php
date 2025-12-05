<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GatewayController extends Controller
{
    private $auth;
    private $servicios;
    private $reservas;
    private $reportes;
    private $auditoria;
    private $notificaciones;

    public function __construct()
    {
        $this->auth = rtrim(env('AUTH_SERVICE_URL'), '/');
        $this->servicios = rtrim(env('SERVICIOS_URL'), '/');
        $this->reservas = rtrim(env('RESERVAS_URL'), '/');
        $this->reportes = rtrim(env('REPORTES_URL'), '/');
        $this->auditoria = rtrim(env('AUDITORIA_URL'), '/');
        $this->notificaciones = rtrim(env('NOTIFICACIONES_URL'), '/');
    }

    // ======= MÉTODO GENERAL PARA REENVIAR PETICIONES =======

    private function forward($method, $url, Request $req)
    {
        $client = Http::withHeaders([
            "Accept" => "*/*",
            "X-API-Key" => env("API_KEY"),
            "Authorization" => $req->bearerToken() ? "Bearer " . $req->bearerToken() : null,
        ]);

        $response = $client->{$method}($url, $req->all());

        
        if ($response->header('Content-Type') !== null &&
            !str_contains($response->header('Content-Type'), 'application/json')) {

            return response($response->body(), $response->status())
                ->header('Content-Type', $response->header('Content-Type'))
                ->header('Content-Disposition', $response->header('Content-Disposition'));
        }

        
        return $response->json();
    }


    // ========================= AUTH =========================

    public function createUser(Request $req)
    {
<<<<<<< HEAD
        return $this->forward("post", "{$this->auth}/create_user", $req);
=======
        return $this->forward("post", env('AUTH_SERVICE_URL')."/create_user", $req);
>>>>>>> desarrollo
    }

    public function login(Request $req)
    {
<<<<<<< HEAD
        return $this->forward("post", "{$this->auth}/login", $req);
=======
        return $this->forward("post", env('AUTH_SERVICE_URL')."/login", $req);
>>>>>>> desarrollo
    }

    public function logout(Request $req)
    {
<<<<<<< HEAD
        return $this->forward("post", $this->auth . "/logout", $req);
=======
        return $this->forward("post", env('AUTH_SERVICE_URL')."/logout", $req);
>>>>>>> desarrollo
    }

    public function forgotPassword(Request $req)
    {
        return $this->forward("post", $this->auth . "/forgot", $req);
    }

    public function resetPassword(Request $req)
    {
        return $this->forward("post", $this->auth . "/reset", $req);
    }
    // ====================== SERVICIOS ========================

    public function serviciosIndex(Request $req)
    {
        return $this->forward("get", "{$this->servicios}/servicios/", $req);
    }
   
    public function serviciosStore(Request $req)
    {
        return $this->forward("post", "{$this->servicios}/servicios/", $req);
    }

    public function serviciosUpdate($id, Request $req)
    {
        return $this->forward("put", "{$this->servicios}/servicios/$id/", $req);
    }

    public function serviciosDelete($id, Request $req)
    {
        return $this->forward("delete", "{$this->servicios}/servicios/$id/", $req);

    }
    public function serviciosShow(Request $req, $id)
    {
        return $this->forward("get", $this->servicios . "/servicios/$id/", $req);
    }

    // ======================= RESERVAS ========================

   public function reservasIndex()
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => env('API_KEY')
            ])->get(env('RESERVAS_URL') . '/reservas');

            return $response->json();
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Error comunicándose con el microservicio de reservas",
                "error" => $e->getMessage()
            ], 500);
        }
    }
    public function reservasStore(Request $req)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key'    => env('API_KEY'),
                'Accept'       => 'application/json',
                'Content-Type' => $req->header('Content-Type', 'application/json'),
            ])->post($this->reservas . '/reservas', $req->all());

            return response()->json($response->json(), $response->status());
        } catch (\Throwable $e) {
            Log::error("Gateway.reservasStore error: " . $e->getMessage());
            return response()->json([
                "message" => "Error comunicándose con el microservicio de reservas",
                "error"   => $e->getMessage()
            ], 500);
        }
    }

    public function reservasShow($id)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => env('API_KEY')
            ])->get($this->reservas . "/reservas/{$id}");

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Error comunicándose con MS Reservas",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function reservasUpdate(Request $request, $id)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => env('API_KEY')
            ])->put($this->reservas . "/reservas/{$id}", $request->all());

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Error comunicándose con MS Reservas",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function reservasDelete($id)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => env('API_KEY')
            ])->delete($this->reservas . "/reservas/{$id}");

            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            return response()->json([
                "message" => "Error comunicándose con MS Reservas",
                "error" => $e->getMessage()
            ], 500);
        }
    }



    // ======================= REPORTES ========================

    public function reportePDF(Request $req)
    {
        return $this->forward("get", "{$this->reportes}/reportes/pdf", $req);
    }

    public function reporteExcel(Request $req)
    {
        return $this->forward("get", "{$this->reportes}/reportes/excel", $req);
    }


    // ====================== AUDITORÍA ========================

    public function auditoriaIndex(Request $req)
    {
        return $this->forward("get", $this->auditoria . "/logs", $req);
    }

    public function auditoriaStore(Request $req)
    {
        return $this->forward("post", $this->auditoria . "/logs", $req);
    }

    public function auditoriaShow($id, Request $req)
    {
        return $this->forward("get", $this->auditoria . "/logs/" . $id, $req);
    }

    public function auditoriaByUser($usuario, Request $req)
    {
        return $this->forward("get", $this->auditoria . "/logs/usuario/" . $usuario, $req);
    }

    public function auditoriaDelete($id, Request $req)
    {
        return $this->forward("delete", $this->auditoria . "/logs/" . $id, $req);
    }


    // ==================== NOTIFICACIONES =====================

   // Enviar notificación por correo
    public function notificacionesCorreo(Request $req)
    {
        return $this->forward("post", "$this->notificaciones/enviar", $req);
    }

}
