<?php $v->layout("_theme", ["title" => CONF_SITE_NAME . " - Confirme da sua conta"]); ?>

<h2>Seja bem-vindo(a) ao <?= CONF_SITE_NAME; ?> <?= $first_name; ?>. Vamos confirmar seu cadastro?</h2>
<p>É importante confirmar seu cadastro para ativar todos os recursos da plataforma. Podendo se candidatar as vagas da plataforma e muito mais.</p>
<p><a title='Confirmar Cadastro' href='<?= $confirm_link; ?>'>CONFIRMAR MINHA CONTA</a></p>