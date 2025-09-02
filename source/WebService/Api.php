<?php

namespace Source\WebService;

use Source\Core\JWTToken;

class Api
{
    protected $headers;
    protected $response;
    protected $userAuth = false;

    public function __construct()
    {
        header('Content-Type: application/json; charset=UTF-8');
        $this->headers = getallheaders();
    }

    protected function call(int $code, string $status = null, string $message = null, $type = null): Api
    {
        http_response_code($code);
        if (!empty($status)) {
            $this->response = [
                "code" => $code,
                "type" => $type,
                "status" => $status,
                "message" => (!empty($message) ? $message : null)
            ];
        }
        return $this;
    }

    protected function back(array $data = null): Api
    {
        if ($data) {
            $this->response["data"] = $data;
        }
        echo json_encode($this->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $this;
    }

    protected function auth(): void
    {
        $token = $this->headers['token'] ?? null;

        if (!$token) {
            $this->call(401, "unauthorized", "Token não fornecido", "error")->back();
            exit();
        }

        $jwt = new JWTToken();
        $decoded = $jwt->decode($token);

        if (!$decoded) {
            $this->call(401, "unauthorized", "Token inválido ou expirado", "error")->back();
            exit();
        }

        $this->userAuth = $decoded->data;
    }

    protected function getRequestData(): array
    {
        $rawInput = file_get_contents("php://input");
        $result = [];

        if (!empty($rawInput)) {
            $jsonData = json_decode($rawInput, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                $result = $jsonData;
            } else {
                parse_str($rawInput, $parsedData);
                if (!empty($parsedData)) {
                    $result = $parsedData;
                }
            }
        }

        if (empty($result)) {
            $result = array_merge($_GET, $_POST, $_REQUEST);
        }

        return $result;
    }
}
