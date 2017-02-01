<?php
    $profileModel = $this->parameters['data']["model"]['profileModel'];
    $userStatistics = $this->parameters['data']["model"]['userStatistics'];
?>
<div class="row">
    <h1>Личный кабинет</h1>
    <hr />
    <? $property = $profileModel->getPropertyByName("avatarPath");?>
    <img src="/<?=$property->value; ?>?<?= intval(microtime(1)) ?>" alt="Ваш аватар" class="img-rounded playerAvatar">
    <div class="playerInfo">
        <? $property = $profileModel->getPropertyByName("login");?>
        <p><?=$property->value; ?> <span class="description text-muted">логин</span></p>
        <? $property = $profileModel->getPropertyByName("email");?>
        <p><?=$property->value; ?> <span class="description text-muted">e-mail</span></p>
    </div>
    <div class="clearfix">
        <form class="form-inline" enctype="multipart/form-data" method="POST" class="changeAvatarForm">
            <div class="form-group">
                <input type="hidden" name="MAX_FILE_SIZE" value="<?=\utils\FileHelper::getUploadFileMaxSizeInBytes(); ?>" />
                <input name="avatar" type="file" id="changeAvatar"title="Сменить аватар"/>
            </div>
            <button type="submit" class="btn btn-success">Загрузить</button>
            <div class="text-danger" style="margin-top: 10px">
                <? $property = $profileModel->getPropertyByName("avatar");?>
                <?= \core\model\HTMLHelper::getList($property->getValidationErrors());?>
            </div>
        </form>
    </div>
</div>
<div class="row">
    <hr />
    <div class="col-md-12">
        <h4>Результаты игр</h4>
        <table class="table">
            <thead>
            <tr>
                <th>Имя игрока</th><th>Сыграно игр</th><th><span class="glyphicon glyphicon-star"></span> Побед</th><th>= Ничьих</th><th><span class="glyphicon glyphicon-fire"></span> Поражений</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th><?= $profileModel->getPropertyByName("login")->value; ?></th>
                <td><?= ($userStatistics['games_count']? $userStatistics['games_count'] : 0); ?></td>
                <td><?= ($userStatistics['success']? $userStatistics['success'] : 0); ?></td>
                <td><?= ($userStatistics['draw']? $userStatistics['draw'] : 0); ?></td>
                <td><?= ($userStatistics['fail']? $userStatistics['fail'] : 0) ?></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>