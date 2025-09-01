<?php
$this->layout("theme", []);
?>

    <div class="profile-container">
        <section class="card">
            <div class="profile-photo-container">
                <img class="foto-perfil" src="" alt="Foto de perfil">
                <label for="photoInput" class="upload-btn">+</label>
                <input type="file" id="photoInput" accept="image/*" style="display: none;">
            </div>

            <h1>Carregando...</h1>
            <p class="arroba">@carregando</p>
            <h2>Biografia</h2>
            <p class="bio">Carregando biografia...</p>

            <button class="seguir" onclick="editProfile()">
                Editar perfil
            </button>
        </section>
    </div>

    <!-- Modal para edição -->
    <div class="modal hidden">
        <div class="modal-content">
            <input type="text" placeholder="Nome" id="nameInput">
            <textarea placeholder="Biografia" id="bioInput"></textarea>
            <button onclick="saveProfile()">Salvar</button>
            <button onclick="closeModal()">Cancelar</button>
        </div>
    </div>

<?php $this->start("specific-css"); ?>
    <link rel="stylesheet" href="<?= url("assets/web/css/profile.css"); ?>">
<?php $this->end(); ?>

<?php $this->start("specific-script"); ?>
    <script src="<?= url("assets/app/js/profile.js"); ?>"></script>
<?php $this->end(); ?>