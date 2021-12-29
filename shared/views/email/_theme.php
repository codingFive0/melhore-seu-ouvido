<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <title><?= $title; ?></title>
    <style>
        body {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            font-family: Helvetica, sans-serif;
        }

        table {
            max-width: 500px;
            padding: 0 10px;
            background: #ffffff;
        }

        .content {
            font-size: 16px;
            margin-bottom: 25px;
            padding-bottom: 5px;
            border-bottom: 1px solid #EEEEEE;
        }

        .content p {
            margin: 30px 0;
        }

        .content ul.tbody{
            background: #c1c1c1;
        }

        .content ul.tbody > li{
            margin: 10px 0;
        }

        .content ul{
            display: flex;
            justify-content: space-around;
            list-style: none;
        }

        .content ul li{
            width: 140px;
        }

        .footer {
            font-size: 14px;
            color: #888888;
            font-style: italic;
        }

        .footer p {
            margin: 0 0 2px 0;
        }
    </style>
</head>
<body>
<table role="presentation" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="content">
                <?= $v->section("content"); ?>
                <p>Atenciosamente, equipe <?= CONF_SITE_NAME; ?>.</p>
            </div>
            <div class="footer">
                <p>E-mail enviado por <?= CONF_SITE_NAME; ?> em <?= date_fmt(null); ?></p>
                <p>Qualquer dúvida entrar em contato pelo e-mail: <?= CONF_MAIL_SUPPORT; ?></p>
            </div>
        </td>
    </tr>
</table>
</body>
</html>
