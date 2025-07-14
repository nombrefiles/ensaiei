<?php
$this->layout("theme", []);
?>

 <div class="faq-section">
        <h2>Conta e acesso</h2>

        <div class="faq-item">
            <div class="faq-title" onclick="toggleFAQ(this)">Como criar uma nova conta?<span class="arrow">▼</span></div>
            <div class="faq-content">
                Basta clicar no botão de cadastro na página inicial e preencher as informações básicas: nome, e-mail e senha. Após isso, você receberá um link de confirmação.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-title" onclick="toggleFAQ(this)">Esqueci minha senha, como recuperar?<span class="arrow">▼</span></div>
            <div class="faq-content">
                Na tela de login, clique em "Esqueci minha senha". Um e-mail com um link de redefinição será enviado para você.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-title" onclick="toggleFAQ(this)">Como atualizar meus dados de perfil?<span class="arrow">▼</span></div>
            <div class="faq-content">
                Para atualizar seus dados, é necessário estar logado e com um token JWT válido. Envie uma requisição PUT com os dados que deseja modificar: nome, email, senha ou foto.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-title" onclick="toggleFAQ(this)">Como encontrar um usuário específico?<span class="arrow">▼</span></div>
            <div class="faq-content">
                Use a barra de pesquisa na aba "perfil" e digite o nome ou e-mail do usuário desejado.
            </div>
        </div>
    </div>

    <div class="faq-section">
        <h2 class="section-title">Gerenciamento de peças</h2>

        <div class="faq-item">
            <div class="faq-title" onclick="toggleFAQ(this)">Como cadastrar uma nova peça?<span class="arrow">▼</span></div>
            <div class="faq-content">
                Vá até a aba “peças” e clique em “Nova Peça”. Preencha o nome, sinopse, autores, datas e elenco para finalizar o cadastro.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-title" onclick="toggleFAQ(this)">É possível importar elenco de outra peça?<span class="arrow">▼</span></div>
            <div class="faq-content">
                Sim! Ao criar uma nova peça, você pode importar o elenco de peças anteriores e fazer ajustes conforme necessário.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-title" onclick="toggleFAQ(this)">Como funciona o agendamento de ensaios?<span class="arrow">▼</span></div>
            <div class="faq-content">
                Você pode agendar ensaios acessando o menu da peça e clicando em “Agendar Ensaio”. Escolha data, hora, local e participantes.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-title" onclick="toggleFAQ(this)">Posso acompanhar a frequência dos participantes?<span class="arrow">▼</span></div>
            <div class="faq-content">
                Sim, na aba de ensaios, você pode marcar presença e visualizar relatórios de frequência por usuário ou por peça.
            </div>
        </div>
    </div>

    <div class="faq-section">
        <h2 class="section-title">Erros comuns</h2>

        <div class="faq-item">
            <div class="faq-title" onclick="toggleFAQ(this)">Por que recebo "Token expirado"?<span class="arrow">▼</span></div>
            <div class="faq-content">
                Seu token de autenticação dura 90 minutos. Após esse tempo, será necessário fazer login novamente para continuar utilizando o sistema.
            </div>
        </div>
    </div>

    <?php  $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/web/css/faqs.css"); ?>">
    <?php $this->end(); ?>

    <?php  $this->start("specific-script"); ?>
<script src="<?= url("assets/web/js/faqs.js"); ?>"></script>
<?php $this->end(); ?>
