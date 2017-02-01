<div class="row">
    <h1>Регистрация</h1>
    <hr />
    <form class="form-horizontal" style="width: 500px" method="POST">
        <? $property = $this->parameters['data']["model"]->getPropertyByName("login");?>
        <div class="form-group <?= \core\model\HTMLHelper::getValErrorClass($property) ;?> has-feedback">
            <label for="login" class="col-sm-2 control-label"> <?= $property->label;?></label>
            <div class="col-sm-10">
                <?= \core\model\HTMLHelper::callTypeInput($property);?>
                <?= \core\model\HTMLHelper::getPropErrorFromControl($property);?>
            </div>
        </div>

        <? $property = $this->parameters['data']["model"]->getPropertyByName("email");?>
        <div class="form-group <?= \core\model\HTMLHelper::getValErrorClass($property) ;?> has-feedback">
            <label for="email" class="col-sm-2 control-label"><?= $property->label;?></label>
            <div class="col-sm-10">
                <?= \core\model\HTMLHelper::callTypeInput($property);?>
                <?= \core\model\HTMLHelper::getPropErrorFromControl($property);?>
            </div>
        </div>

        <? $property = $this->parameters['data']["model"]->getPropertyByName("password");?>
        <div class="form-group <?= \core\model\HTMLHelper::getValErrorClass($property) ;?> has-feedback">
            <label for="password" class="col-sm-2 control-label"><?= $property->label;?></label>
            <div class="col-sm-10">
                <?= \core\model\HTMLHelper::callTypeInput($property);?>
                <?= \core\model\HTMLHelper::getPropErrorFromControl($property);?>
            </div>
        </div>

        <? $property = $this->parameters['data']["model"]->getPropertyByName("password_repeat");?>
        <div class="form-group <?= \core\model\HTMLHelper::getValErrorClass($property) ;?> has-feedback">
            <label for="password" class="col-sm-2 control-label"><?= $property->label;?></label>
            <div class="col-sm-10">
                <?= \core\model\HTMLHelper::callTypeInput($property);?>
                <?= \core\model\HTMLHelper::getPropErrorFromControl($property);?>
            </div>
        </div>
        <?= \core\model\HTMLHelper::getModelErrorMessage($this->parameters['data']["model"]);?>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default">Зарегистрироваться</button>
            </div>
        </div>
    </form>
    <hr>
    <a href="/Auth/Login">Авторизация</a>
</div>