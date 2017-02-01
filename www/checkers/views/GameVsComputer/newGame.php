<div class="row">
    <h1>Игра против компьютера</h1>
    <hr />
    <div class="btn-group btn-group-justified" role="group" >
        <a class="btn btn-success difficultyLvlBtn lightDiffButton">Легкий</a>
        <a class="btn btn-default difficultyLvlBtn mediumDiffButton">Средний</a>
        <a class="btn btn-danger  difficultyLvlBtn hardDiffButton">Сложный</a>
    </div>
    <div class="panel panel-success newGameConf" style="display: none">
        <div class="panel-body bg-success">
            <form class="form-horizontal" method="POST">
                <div class="form-group has-feedback">
                    <label for="color" class="col-sm-6 control-label">Выберите цвет</label>
                    <div class="col-sm-6">
                       <span class="sideLabel blackSideLabel"></span>
                       <span class="sideLabel whiteSideLabel selectedSideLabel"></span>
                    </div>
                </div>
                <div class="form-group has-feedback">
                    <label for="showTips" class="col-sm-6 control-label">Показывать подсказки</label>
                    <div class="col-sm-6">
                        <input type="checkbox" name="showTips" id="showTips" value="true" checked>
                    </div>
                </div>
                <div class="form-group has-feedback">
                    <label for="showMoveRecord" class="col-sm-6 control-label">Показывать запись ходов</label>
                    <div class="col-sm-6">
                        <input type="checkbox" name="showMoveRecord" id="showMoveRecord" value="true" checked>
                    </div>
                </div>
                <input type="hidden" name="color" id="color" value="white">
                <input type="hidden" name="difficultyLevel" id="difficultyLevel" value="light">
                <div class="form-group">
                    <div class="col-sm-offset-6 col-sm-6">
                        <button type="submit" class="btn btn-primary">Начать</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>