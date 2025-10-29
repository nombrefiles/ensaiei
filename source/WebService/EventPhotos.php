<?php

namespace Source\WebService;

use Source\Models\Event;
use Source\Models\EventPhoto;

class EventPhotos extends Api
{
    public function listPhotosByEvent(array $data): void
    {
        if (!isset($data["eventId"]) || !filter_var($data["eventId"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do evento inválido", "error")->back();
            return;
        }

        $eventPhoto = new EventPhoto();
        $photos = $eventPhoto->findByEventId($data["eventId"]);

        $response = array_map(function($photo) {
            return [
                'id' => $photo['id'],
                'photo' => CONF_URL_BASE . $photo['photo'],
                'isMain' => (bool)$photo['isMain']
            ];
        }, $photos);

        $this->call(200, "success", "Fotos do evento", "success")->back($response);
    }

    public function uploadPhotos(array $data): void
    {
        $this->auth();

        if (!isset($data["eventId"]) || !filter_var($data["eventId"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID do evento inválido", "error")->back();
            return;
        }

        $eventId = $data["eventId"];

        $event = new Event();
        if (!$event->findById($eventId)) {
            $this->call(404, "not_found", "Evento não encontrado", "error")->back();
            return;
        }

        if ($event->getOrganizerId() !== $this->userAuth->id) {
            $this->call(403, "forbidden", "Você não tem permissão para adicionar fotos a este evento", "error")->back();
            return;
        }

        error_log("FILES recebidos: " . print_r($_FILES, true));

        if (empty($_FILES["photos"]["name"][0])) {
            $this->call(400, "bad_request", "Nenhuma foto enviada", "error")->back();
            return;
        }

        $uploadedPhotos = [];
        $errors = [];

        $uploadDir = __DIR__ . '/../../storage/images/events/';

        error_log("Diretório de upload: " . $uploadDir);

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0777, true)) {
                error_log("ERRO: Não foi possível criar o diretório: " . $uploadDir);
                $this->call(500, "internal_server_error", "Erro ao criar diretório de upload", "error")->back();
                return;
            }
            error_log("Diretório criado com sucesso: " . $uploadDir);
        }


        if (!is_writable($uploadDir)) {
            error_log("ERRO: Diretório não tem permissão de escrita: " . $uploadDir);
            chmod($uploadDir, 0777);
        }

        $totalFiles = count($_FILES["photos"]["name"]);
        error_log("Total de arquivos a processar: " . $totalFiles);

        for ($i = 0; $i < $totalFiles; $i++) {
            $fileName = $_FILES["photos"]["name"][$i];
            $fileTmpName = $_FILES["photos"]["tmp_name"][$i];
            $fileSize = $_FILES["photos"]["size"][$i];
            $fileError = $_FILES["photos"]["error"][$i];

            error_log("Processando arquivo {$i}: {$fileName}");
            error_log("Temp name: {$fileTmpName}");
            error_log("Error: {$fileError}");

            if ($fileError !== UPLOAD_ERR_OK) {
                $errorMsg = $this->getUploadErrorMessage($fileError);
                $errors[] = "Erro ao fazer upload de {$fileName}: {$errorMsg}";
                error_log("ERRO no upload: " . $errorMsg);
                continue;
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $fileType = finfo_file($finfo, $fileTmpName);
            finfo_close($finfo);

            error_log("Tipo MIME detectado: {$fileType}");

            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "Tipo de arquivo inválido para {$fileName}. Tipo detectado: {$fileType}";
                error_log("ERRO: Tipo de arquivo inválido: {$fileType}");
                continue;
            }


            $maxSize = 5 * 1024 * 1024;
            if ($fileSize > $maxSize) {
                $errors[] = "Arquivo {$fileName} muito grande. Tamanho: " . ($fileSize / 1024 / 1024) . "MB";
                error_log("ERRO: Arquivo muito grande: " . $fileSize);
                continue;
            }

            if ($fileSize < 10240) {
                $errors[] = "Arquivo {$fileName} muito pequeno";
                error_log("ERRO: Arquivo muito pequeno: " . $fileSize);
                continue;
            }

            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if ($fileType === 'image/jpeg' && $fileExtension !== 'jpg' && $fileExtension !== 'jpeg') {
                $fileExtension = 'jpg';
            }

            $newFileName = 'event_' . $eventId . '_' . uniqid() . '.' . $fileExtension;
            $destination = $uploadDir . $newFileName;

            error_log("Tentando mover arquivo para: " . $destination);

            if (!move_uploaded_file($fileTmpName, $destination)) {
                $errors[] = "Erro ao mover {$fileName} para o destino";
                error_log("ERRO: Não foi possível mover o arquivo");
                error_log("Permissões do diretório: " . substr(sprintf('%o', fileperms($uploadDir)), -4));
                continue;
            }

            error_log("Arquivo movido com sucesso!");

            // Caminho relativo para salvar no banco
            $relativePath = 'storage/images/events/' . $newFileName;

            $eventPhoto = new EventPhoto();
            $eventPhoto->setEventId($eventId);
            $eventPhoto->setPhoto($relativePath);

            $photoModel = new EventPhoto();
            $photoCount = $photoModel->countByEventId($eventId);
            $eventPhoto->setIsMain($photoCount === 0);

            if (!$eventPhoto->insert()) {
                $errors[] = "Erro ao salvar {$fileName} no banco de dados";
                error_log("ERRO: Falha ao inserir no banco de dados");
                if (file_exists($destination)) {
                    unlink($destination);
                }
                continue;
            }

            error_log("Foto salva no banco com sucesso! ID: " . $eventPhoto->getId());

            $uploadedPhotos[] = [
                'id' => $eventPhoto->getId(),
                'photo' => CONF_URL_BASE . $relativePath,
                'isMain' => $eventPhoto->getIsMain()
            ];
        }

        if (empty($uploadedPhotos)) {
            $this->call(500, "internal_server_error", "Nenhuma foto foi enviada com sucesso. Erros: " . implode("; ", $errors), "error")->back();
            return;
        }

        $response = [
            'uploadedPhotos' => $uploadedPhotos,
            'totalUploaded' => count($uploadedPhotos),
            'errors' => !empty($errors) ? $errors : []
        ];

        $this->call(201, "created", "Fotos enviadas com sucesso", "success")->back($response);
    }

    private function getUploadErrorMessage($errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'O arquivo excede o tamanho máximo permitido pelo servidor';
            case UPLOAD_ERR_FORM_SIZE:
                return 'O arquivo excede o tamanho máximo permitido no formulário';
            case UPLOAD_ERR_PARTIAL:
                return 'O arquivo foi enviado parcialmente';
            case UPLOAD_ERR_NO_FILE:
                return 'Nenhum arquivo foi enviado';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Pasta temporária ausente';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Falha ao escrever o arquivo no disco';
            case UPLOAD_ERR_EXTENSION:
                return 'Uma extensão do PHP bloqueou o upload';
            default:
                return 'Erro desconhecido no upload';
        }
    }

    public function setMainPhoto(array $data): void
    {
        $this->auth();

        if (!isset($data["photoId"]) || !filter_var($data["photoId"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID da foto inválido", "error")->back();
            return;
        }

        $eventPhoto = new EventPhoto();
        if (!$eventPhoto->findById($data["photoId"])) {
            $this->call(404, "not_found", "Foto não encontrada", "error")->back();
            return;
        }

        $event = new Event();
        if (!$event->findById($eventPhoto->getEventId())) {
            $this->call(404, "not_found", "Evento não encontrado", "error")->back();
            return;
        }

        if ($event->getOrganizerId() !== $this->userAuth->id) {
            $this->call(403, "forbidden", "Você não tem permissão para alterar fotos deste evento", "error")->back();
            return;
        }

        if (!$eventPhoto->setAsMain()) {
            $this->call(500, "internal_server_error", "Erro ao definir foto principal", "error")->back();
            return;
        }

        $this->call(200, "success", "Foto principal definida com sucesso", "success")->back([
            'id' => $eventPhoto->getId(),
            'photo' => CONF_URL_BASE . $eventPhoto->getPhoto(),
            'isMain' => true
        ]);
    }

    public function deletePhoto(array $data): void
    {
        $this->auth();

        if (!isset($data["photoId"]) || !filter_var($data["photoId"], FILTER_VALIDATE_INT)) {
            $this->call(400, "bad_request", "ID da foto inválido", "error")->back();
            return;
        }

        $eventPhoto = new EventPhoto();
        if (!$eventPhoto->findById($data["photoId"])) {
            $this->call(404, "not_found", "Foto não encontrada", "error")->back();
            return;
        }

        $event = new Event();
        if (!$event->findById($eventPhoto->getEventId())) {
            $this->call(404, "not_found", "Evento não encontrado", "error")->back();
            return;
        }

        if ($event->getOrganizerId() !== $this->userAuth->id) {
            $this->call(403, "forbidden", "Você não tem permissão para deletar fotos deste evento", "error")->back();
            return;
        }

        if (!$eventPhoto->deletePhoto()) {
            $this->call(500, "internal_server_error", "Erro ao deletar foto", "error")->back();
            return;
        }

        $this->call(200, "success", "Foto deletada com sucesso", "success")->back();
    }
}