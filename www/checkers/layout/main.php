<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="expires" content="0">
    <meta name="description" content="Русские шашки онлайн">
    <meta name="keywords" content="Русские шашки, исскуственный интелект шашек">
    <meta name="author" content="Холоднюк Федор">
	<title>Руские шашки</title>

	<link rel="stylesheet" type="text/css" href="../../lib/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="../../lib/css/bootstrap-theme.min.css">
    <link rel="stylesheet" type="text/css" href="../../front-end/css/mainLayout.css">
	<? foreach ($this->parameters['css'] as $cssHref): ?>
		<link rel="stylesheet" type="text/css" href="<?= $this->parameters['pathPrefix'] . $cssHref?> ">
	<? endforeach;?>
</head>
<body>
<header class="navbar navbar-inverse">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Шашки</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="/">Главная</a></li>
                <li>
                    <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        Игра
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu inverse-dropdown">
                        <li><a href="/GameVsComputer/">Против компьютера</a></li>
                        <li class="disabled"><a href="#">Против игрока</a></li>
                    </ul>
                </li>
                <li><a href="/Home/Rules">Правила</a>
                <li><a href="/Home/About">О сайте</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <? if ($_SESSION['auth']): ?>
                    <li><a href="/Auth/Profile"><span class="glyphicon glyphicon-user"></span> Личный кабинет</a></li>
                    <li><a href="/Auth/Logout"><span class="glyphicon glyphicon-log-out"></span> Выйти</a></li>
                <? else: ?>
                    <li><a href="/Auth/Register" ><span class="glyphicon glyphicon-user"></span> Регистрация</a></li>
                    <li><a href="/Auth/Login"><span class="glyphicon glyphicon-log-in"></span> Войти</a></li>
                <? endif ?>
            </ul>
        </div>
    </div>
</header>
<div class="container">
    <div class="modal" id="ajaxErrorDialog" tabindex="-1" role="dialog"  data-agreed="false">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header errorDialogHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-exclamation-sign"></span> Ошибка</h4>
                </div>
                <div class="modal-body ajaxErrorText text-danger">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger drawResponseClose" data-dismiss="modal">ОК</button>
                </div>
            </div>
        </div>
    </div>
	<?php include $this->parameters['viewPath'] . $this->parameters['contentView']; ?>
</div>

<script type="text/javascript" src="../../lib/js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="../../lib/js/bootstrap.min.js"></script>
<script type="text/javascript" src="../../front-end/js/main.js"></script>
<? foreach ($this->parameters['javascript'] as $jsSrc): ?>
	<script type="text/javascript" src="<?= $this->parameters['pathPrefix'] . $jsSrc?> "></script>
<? endforeach;?>
</body>
</html>