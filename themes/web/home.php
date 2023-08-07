<?php $v->layout('_theme', ['title' => CONF_SITE_NAME]); ?>

    <!--  BOTÃO PRINCIPAL - EMISSOR DE SOM  -->
    <div class="container box_feature_button">
        <div class="content flex jc-center">
            <div id="jquery_jplayer_1"></div>
            <div id="jp_container_1"></div>
            <button data-play-note class="box_feature_buttom_circle circle transition">
                <i class="fas fa-volume-down f-size-super"></i>
            </button>
        </div>
    </div>

    <!-- BOTÕES DE RESPOSTA -->
    <div class="container">
        <div class="content flex jc-center">
            <form name="j_response" class="flex form_format" method="post" action="<?= url("/resposta"); ?>">
                <input type="hidden" name="soundName">
                <div class="flex">
                    <input id="primeiro" type="radio" name="response" value="" class="off-visible">
                    <label class="radius btn btn-outline-theme transition" for="primeiro">
                        <i class="fa fa-check"></i><span class="j_opt_target">C</span>
                    </label>
                </div>

                <div class="flex">
                    <input id="segundo" type="radio" name="response" value="" class="off-visible">
                    <label class="radius btn btn-outline-theme transition" for="segundo">
                        <i class="fa fa-check"></i><span class="j_opt_target">G#</span>
                    </label>
                </div>
                <div class="flex">
                    <input id="terceiro" type="radio" name="response" value="" class="off-visible">
                    <label class="radius btn btn-outline-theme transition" for="terceiro">
                        <i class="fa fa-check"></i><span class="j_opt_target">D</span>
                    </label>
                </div>

                <div class="flex">
                    <input id="quarto" type="radio" name="response" value="" class="off-visible">
                    <label class="radius btn btn-outline-theme transition" for="quarto">
                        <i class="fa fa-check"></i><span class="j_opt_target">Eb</span>
                    </label>
                </div>
            </form>
        </div>
    </div>

<?php $v->start("modals"); ?>
    <div class="ui basic modal">
        <div class="ui loader"></div>
    </div>

    <div class="my_alert">
        <div class="my_alert_mensage_box">

        </div>
    </div>
<?php $v->end(); ?>