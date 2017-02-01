<div class="row">
    <?php
        $moves = $this->parameters['data']['gameMoves']['items'];
        $result = $this->parameters['data']['gameResult']['result'];
        $cause = $this->parameters['data']['gameResult']['cause'];
        $gameResult = $this->parameters['data']['gameResult'];
        $movesCount = $this->parameters['data']['gameResult']['moveNumber'];
        $whiteDefeated = $this->parameters['data']['gameResult']['whiteDefeated'];
        $blackDefeated = $this->parameters['data']['gameResult']['blackDefeated'];
        $color = ($this->parameters['data']['gameResult']['is_player_color_black'] ? 'Черный' : 'Белый');
        $profileData = $this->parameters['data']['profileData'];
    ?>
    <h1
        <? switch($result): ?><? case (\checkersBLL\ResultsEnum::success): ?>
            class="text-success"
            <? case (\checkersBLL\ResultsEnum::draw): ?>
            class="text-default"
            <? case (\checkersBLL\ResultsEnum::fail):?>
            class="text-danger"
        <? endswitch ?>

        ><?= $result;?> </h1>
    <a href="/GameVsComputer/Index">Новая игра</a>
    <hr/>

    <div class="col-md-12 ">
        <dl class="dl-horizontal">
            <dt>Количество ходов</dt>
            <dd><?=$movesCount;?></dd>
            <dt>Причина</dt>
            <dd><?=$cause;?></dd>
            <dt>Цвет игрока</dt>
            <dd><?=$color;?></dd>
            <dt>Черных сбито</dt>
            <dd><?=$blackDefeated;?></dd>
            <dt>Белых сбито</dt>
            <dd><?=$whiteDefeated;?></dd>
        </dl>
        <h4 class="center-block">
            Запись ходов игры
            <button class="btn btn-default btn-sm moveListBtn">Показать <span class="glyphicon glyphicon-chevron-down"></span></button></h4>

        <table class="table table-condensed moveList collapse">
            <thead>
            <tr>
                <th>#</th><th>Белые</th><th>Черные</th>
            </tr>
            </thead>
            <tbody>
            <? foreach ($moves as $move): ?>
                <tr>
                    <td>
                        <?= $move['number']; ?>
                    </td>
                    <td>
                        <?= $move['white_move']; ?>
                    </td>
                    <td>
                        <?= $move['black_move']; ?>
                    </td>
                </tr>
            <? endforeach ?>
            </tbody>
        </table>
    </div>

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
                <th><?= $profileData['login'];?></th>
                <td><?= $gameResult['games_count']; ?></td>
                <td><?= $gameResult['success']; ?></td>
                <td><?= $gameResult['draw']; ?></td>
                <td><?= $gameResult['fail']; ?></td>
            </tr>
            </tbody>
        </table>
    </div>
    <p style="text-align: center">
        <a class="btn btn-primary" href="/GameVsComputer/Index" >Новая игра</a>
    </p>

</div>