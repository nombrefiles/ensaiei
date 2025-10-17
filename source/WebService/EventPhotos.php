<?php

namespace Source\WebService;

use SorFabioSantos\Uploader\Uploader;
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


        if (empty($_FILES["photos"]["name"][0])) {
            $this->call(400, "bad_request", "Nenhuma foto enviada", "error")->back();
            return;
        }

        $uploadedPhotos = [];
        $errors = [];
        $upload = new Uploader();

        $totalFiles = count($_FILES["photos"]["name"]);

        for ($i = 0; $i < $totalFiles; $i++) {
            $file = [
                'name' => $_FILES["photos"]["name"][$i],
                'type' => $_FILES["photos"]["type"][$i],
                'tmp_name' => $_FILES["photos"]["tmp_name"][$i],
                'error' => $_FILES["photos"]["error"][$i],
                'size' => $_FILES["photos"]["size"][$i]
            ];

            $path = $upload->Image($file);

            if (!$path) {
                $errors[] = "Erro ao fazer upload de {$file['name']}: " . $upload->getMessage();
                continue;
            }

            $eventPhoto = new EventPhoto();
            $eventPhoto->setEventId($eventId);
            $eventPhoto->setPhoto($path);

            $photoModel = new EventPhoto();
            $photoCount = $photoModel->countByEventId($eventId);
            $eventPhoto->setIsMain($photoCount === 0);

            if (!$eventPhoto->insert()) {
                $errors[] = "Erro ao salvar {$file['name']} no banco de dados";
                // Deletar arquivo que foi feito upload
                if (file_exists(__DIR__ . '/../../' . $path)) {
                    unlink(__DIR__ . '/../../' . $path);
                }
                continue;
            }

            $uploadedPhotos[] = [
                'id' => $eventPhoto->getId(),
                'photo' => CONF_URL_BASE . $path,
                'isMain' => $eventPhoto->getIsMain()
            ];
        }

        if (empty($uploadedPhotos)) {
            $this->call(500, "internal_server_error", "Nenhuma foto foi enviada com sucesso: " . implode(", ", $errors), "error")->back();
            return;
        }

        $response = [
            'uploadedPhotos' => $uploadedPhotos,
            'totalUploaded' => count($uploadedPhotos),
            'errors' => $errors
        ];

        $this->call(201, "created", "Fotos enviadas com sucesso", "success")->back($response);
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