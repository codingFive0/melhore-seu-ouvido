$(function () {

    function url(uri = null) {
        return window.url + (uri ? uri : '');
    }

    var fileTarget;
    var player = $("#jquery_jplayer_1");

    player.jPlayer({
        ended: function (event) {
            $('[data-play-note]').removeAttr("disabled")
        },
        timeupdate: function (event) {
            if (event.jPlayer.status.currentTime >= 2.5) {
                $(this).jPlayer("stop")
                $('[data-play-note]').removeAttr("disabled")
            }
        },
        swfPath: "./jPlayer-master/dist/jplayer/jquery.jplayer.swf",
        supplied: "mp3",
        volume: 1
    }, 'jquery_jplayer_1');

    reLoadQuestion()

    $('body').on("click", '[data-play-note]', function () {
        player.jPlayer("play")
        $(this).attr("disabled", true)
    })

    $(document).keyup(function (e) {
        if (e.which == 32) {
            e.preventDefault()
            e.stopPropagation()

            player.jPlayer("play")
            $(this).attr("disabled", true)

            return false
        }
    })

    function putOptions(options) {
        $.each(options.responseOptions, function (key, element) {
            var target = $(".j_opt_target:eq(" + key + ")");
            target.text(element)
            target.parents("label").parent().find("input[type='radio']").val(element)
            $("input[name='soundName']").val(fileTarget)
        })
    }

    $("form[name='j_response']").on("change", "input[name='response']", function (e) {
        $(this).parents("form").submit()
    })

    $('body').on("submit", "form[name='j_response']", function (e) {
        e.preventDefault();
        var form = $(this)

        $.ajax({
            url: form.attr("action"),
            method: 'post',
            data: form.serialize(),
            dataType: "json",
            async: false,
            beforeSend: function (e) {
                $('.ui.basic.modal').modal('show')
            },
            success: function (response) {
                showResponseModal(response.responseStatus)

                reLoadQuestion(form)
            }
        });
    })

    function reLoadQuestion(form = null) {
        $.ajax({
            url: url("/questoes"),
            method: 'post',
            data: {action: 'selectQuestion'},
            dataType: "json",
            async: false,
            success: function (response) {
                fileTarget = response.midiaFile

                putOptions(response)

                $('.ui.basic.modal').modal('hide')
            }
        });

        var audioFolder = url("/uploads/" + encodeURIComponent(fileTarget))
        
        player.jPlayer("setMedia", {
            title: "Ouça, Memorize, Busque Referencias e Responsa",
            mp3: audioFolder
        });

        $('[data-play-note]').removeAttr("disabled")

        if (form != null) {
            form[0].reset()
        }

        setTimeout(() => {
            hiddenResponseModal()
        }, window.timeOut);
    }

    function showResponseModal(status) {
        var icons = (status ? "fa-grin-stars" : "fa-dizzy")
        var className = (status ? "happy" : "sad")
        var msg = (status ? 'Showwwww. Você acertou! Continue mostrando como se faz!' : 'Iiihhhhhhh. Quase brother. Vamos de novo!')

        var modalBox = $('.my_alert')
        var modalboxBg = $('.my_alert_mensage_box')
        var html = "<i class=\"fa " + icons + "\"></i> <span>" + msg + "</span>"

        modalboxBg.addClass(className)
        modalboxBg.html(html)
        modalBox.fadeIn(window.efectTime)
    }

    function hiddenResponseModal() {
        var modalBox = $('.my_alert')
        var modalboxBg = $('.my_alert_mensage_box')
        modalBox.fadeOut(window.efectTime, function () {
            modalboxBg.removeClass("sad").removeClass("happy")
            modalboxBg.html('')
        })

    }
});