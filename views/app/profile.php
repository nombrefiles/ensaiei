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

            <button class="edit-profile-btn" onclick="editProfile()">
                Editar perfil
            </button>
        </section>
    </div>

    <div class="modal hidden" id="editModal">
        <div class="modal-content">
            <h3>Editar perfil</h3>
            <input type="text" placeholder="Nome" id="nameInput">
            <span id="username-error" class="error"></span>
            <input type="text" placeholder="Usuário" id="usernameInput">
            <span id="username-error" class="error"></span>
            <textarea placeholder="Biografia" id="bioInput"></textarea>
            <span id="username-error" class="error"></span>
            <a href="http://localhost/ensaiei-main/app/password">Trocar senha</a>
            <div class="modal-buttons">
                <button class="btn-primary" onclick="saveProfile()">Salvar</button>
                <button class="btn-secondary" onclick="closeModal()">Cancelar</button><br>
            </div>
        </div>
    </div>

<?php $this->start("specific-css"); ?>
    <link rel="stylesheet" href="<?= url("assets/app/css/profile.css"); ?>">
<?php $this->end(); ?>

<?php $this->start("specific-script"); ?>
    <script src="<?= url("assets/app/js/profile.js"); ?>"></script>
<?php $this->end(); ?>