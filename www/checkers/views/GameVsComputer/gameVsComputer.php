<div class="row" xmlns="http://www.w3.org/1999/html">
    <?php
        $showTips = $this->parameters['data']['gameInfo']['viewConfigure']['showTips'];
        $showMoveRecords = $this->parameters['data']['gameInfo']['viewConfigure']['showMoveRecord'];
        $isPlayerMove = $this->parameters['data']['gameInfo']['is_player_move'];
    ?>
    <div class="modal fade" id="configureDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header dialogHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"><span class="glyphicon glyphicon-cog"></span> Настройки</h4>
                </div>
                <div class="modal-body">
                    <form class="form-horizontal viewConfigureForm">
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="showTips" value="true" id="showTipsInput"> Показывать подсказки
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="showMoveRecord" value="true" id="showMoveRecordsInput"> Показывать запись ходов
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                    <button type="button" class="btn btn-primary saveViewConfigure">Сохранить</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="surrenderDialog" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header dialogHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-warning-sign"></span> Cдаться?</h4>
                </div>
                <div class="modal-body">
                    Вы уверены что хотите сдаться?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Нет</button>
                    <a type="button"  href="<?= $this->parameters['data']['surrenderHref']; ?>" class="btn btn-danger">Да</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="offerDrawDialog" tabindex="-1" role="dialog" data-result_url="/GameVsComputer/Result">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header dialogHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-warning-sign"></span> Ничья?</h4>
                </div>
                <div class="modal-body">
                    Предложить ничью?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Нет</button>
                    <button type="button"   href="<?= $this->parameters['data']['offerDrawHref']; ?>" class="btn btn-warning offerDrawBtn">Да</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="drawResponseDialog" tabindex="-1" role="dialog"  data-agreed="false">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header dialogHeader">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel"><span class="glyphicon glyphicon-info-sign"></span> Ничья. Ответ</h4>
                </div>
                <div class="modal-body drawResponseText">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info drawResponseClose" data-dismiss="modal">ОК</button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-9 text-center">
        <div class="gameArea">
            <div class="gameAreaLeft">
                <div class="playerName playerInfo"><?= $this->parameters['data']['profileData']['login']; ?></div>
                <img src="/<?= $this->parameters['data']['profileData']['avatarPath']; ?>" class="img-rounded playerAvatar"/>
                <div>
                    <span>Играет за: </span>
                    <div class="<?= ($this->parameters['data']['gameInfo']['is_player_color_black'] ?'blackSideLabel' :'whiteSideLabel' ) ?>"></div>
                </div>
                <div class="scoreContainer">
                    <span>Сбил:</span>
                         <? if($this->parameters['data']['gameInfo']['is_player_color_black']): ?>
                             <span class="blackScoreContainer">
                             <?= $this->parameters['data']['gameInfo']['black_score']; ?>
                             </span>
                         <? else: ?>
                             <span class="whiteScoreContainer">
                             <?= $this->parameters['data']['gameInfo']['white_score']; ?>
                             </span>
                         <? endif ?>
                    </span>
                    <span class="<?= ($this->parameters['data']['gameInfo']['is_player_color_black'] ?'scoreSpawnWhite' :'scoreSpawnBlack' ) ?>"></span>
                </div>
                <div>
                    <a class="btn btn-default" href="#" role="button"  data-toggle="modal" data-target="#offerDrawDialog">Предложить ничью</a>
                </div>
                <div>
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#surrenderDialog">Сдаться <span class="glyphicon glyphicon-flag"></span></button>
                </div>
                <div>
                    <button type="button" class="btn btn-info" role="button" data-toggle="modal" data-target="#configureDialog">Настройки <span class="glyphicon glyphicon-cog"></span></button>
                </div>
            </div>
            <div class="gameAreaCenter">

                <div class="whoseMoveLabelParent">
                    <? if ($isPlayerMove): ?>
                        <span class="yourMoveLabel">Ваш ход</span>
                    <? else: ?>
                        <span class="opponentMoveLabel">Ход оппонента</span>
                    <? endif ?>

                </div>
                <div class="checkersFieldPlace" data-white_checkers="<?= $this->parameters['data']['gameInfo']['white_checkers']; ?>" data-black_checkers="<?= $this->parameters['data']['gameInfo']['black_checkers']; ?>"
                    data-available_moves='<?= json_encode($this->parameters['data']['gameInfo']['availableMoves']); ?>'
                    data-enemy_move='<?= json_encode($this->parameters['data']['gameInfo']['enemyMove']); ?>'
                    data-is_enemy_move='<?= !$isPlayerMove;?>'
                    data-player_side='<?= ($this->parameters['data']['gameInfo']['is_player_color_black']?'black':'white'); ?>'
                    data-show_tips='<?= $showTips ?>'
                    data-show_move_records='<?= $showMoveRecords; ?>'
                ></div>
                <div class="lastOpponentMoveContainer">
                    <span>Последник ход противника: </span>
                    <? $moveCount = count($this->parameters['data']['gameMoves']['items'])?>
                    <span id="lastEnemyMove">
                       <? if ($moveCount): ?>
                           <?=($this->parameters['data']['gameInfo']['is_player_color_black'] ?$this->parameters['data']['gameMoves']['items'][$moveCount - 1]['white_move'] :$this->parameters['data']['gameMoves']['items'][$moveCount - 1]['black_move'] ) ?>
                       <? else: ?>
                           -
                       <? endif ?>
                    </span>
                </div>
                <div class="gameHelp" <?= ($showTips ? '' : 'style="display:none"'); ?>>
                    <table>
                        <tr>
                            <td>
                                <span class="glyphicon glyphicon-info-sign"></span>
                                <span class="gameHelpText">
                                    <? if ($isPlayerMove): ?>
                                        Для выбора фигуры, которой будете совершать ход кликните по ней левой кнопкой мыши.
                                    <? else: ?>
                                        Подождите пока выполниться ход противника.
                                    <? endif ?>
                                </span>
                            </td>
                            <td class="gameHelpCloseParent">
                                <button type="button" class="close gameHelpClose">
                                    <span>&times;</span>
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="gameAreaRight">
                <div class="playerName playerInfo"><?= $this->parameters['data']['opponentData']['login']; ?></div>
                <img src="/<?= $this->parameters['data']['opponentData']['avatarPath']; ?>" class="img-rounded playerAvatar"/>
                <div>
                    <span>Играет за: </span>
                    <div class="<?= (!$this->parameters['data']['gameInfo']['is_player_color_black'] ?'blackSideLabel' :'whiteSideLabel' ) ?>"></div>
                </div>
                <div class="scoreContainer">
                    <span>Сбил:</span>
                    <span>
                        <? if(!$this->parameters['data']['gameInfo']['is_player_color_black']): ?>
                            <span class="blackScoreContainer">
                            <?= $this->parameters['data']['gameInfo']['black_score']; ?>
                            </span>
                        <? else: ?>
                            <span class="whiteScoreContainer">
                            <?= $this->parameters['data']['gameInfo']['white_score']; ?>
                            </span>
                        <? endif ?>
                    </span>
                    <span class="<?= (!$this->parameters['data']['gameInfo']['is_player_color_black'] ?'scoreSpawnWhite' :'scoreSpawnBlack' ) ?>"></span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="panel panel-success recordMovePanel" <?= ($showMoveRecords ? '' : 'style="display:none"'); ?>>
            <div class="panel-heading">
                Запись ходов
                <button type="button" class="close closeRecordMovePanel">
                    <span>&times;</span>
                </button></div>
            <div class="panel-body moveListPanel">
                <table class="table table-condensed moveList">
                    <thead>
                        <tr>
                            <th>#</th><th>Белые</th><th>Черные</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach ($this->parameters['data']['gameMoves']['items'] as $move): ?>
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
        </div>
    </div>
</div>