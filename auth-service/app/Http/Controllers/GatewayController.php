<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GatewayController extends Controller
{
    protected $auth;
    protected $servicios;
    protected $auditoria;
    protected $notificaciones;
    protected $reportes;
    protected $reservas;
    protected $apiKey;

    public function __construct()
    {
        $this->auth = env('AUTH_URL');
        $this->servicios = env('SERVICIOS_URL');
        $this->auditoria = env('AUDITORIA_URL');
        $this->notificaciones = env('NOTIFICACIONES_URL');
        $this->reportes = env('REPORTES_URL');
        $this->reservas = env('RESERVAS_URL');
        $this->apiKey = env('API_KEY');
    }

    private function send($method, $url, $data = [])
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey
        ])->$method($url, $data)->json();
    }

    // ------------------- AUTENTICACIÓN -------------------
    public function auth_login(Request $req)
    { return $this->send('post', "$this->auth/login", $req->all()); }

    public function auth_create_user(Request $req)
    { return $this->send('post', "$this->auth/create_user", $req->all()); }

    public function auth_change_password(Request $req)
    { return $this->send('post', "$this->auth/change_password", $req->all()); }

    public function auth_forgot(Request $req)
    { return $this->send('post', "$this->auth/forgot_password", $req->all()); }

    // ------------------- SERVICIOS -------------------
    public function servicios_index()
    { return $this->send('get', "$this->servicios/servicios"); }

    public function servicios_store(Request $req)
    { return $this->send('post', "$this->servicios/servicios", $req->all()); }

    public function servicios_update(Request $req, $id)
    { return $this->send('put', "$this->servicios/servicios/$id", $req->all()); }

    public function servicios_delete($id)
    { return $this->send('delete', "$this->servicios/servicios/$id"); }

    // ------------------- RESERVAS -------------------
    public function reservas_index()
    { return $this->send('get', "$this->reservas/reservas"); }

    public function reservas_store(Request $req)
    { return $this->send('post', "$this->reservas/reservas", $req->all()); }

    // ------------------- REPORTES -------------------
    public function reportes_excel()
    { return Http::get("$this->reportes/excel"); }

    public function reportes_pdf()
    { return Http::get("$this->reportes/pdf"); }

    // ------------------- AUDITORÍA -------------------
    public function auditoria_index()
    { return $this->send('get', "$this->auditoria/auditoria"); }

    // ------------------- NOTIFICACIONES -------------------
    public function notificaciones_send(Request $req)
    { return $this->send('post', "$this->notificaciones/send", $req->all()); }
}
