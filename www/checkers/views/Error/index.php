<div class="well">
    <? if ($this->parameters['data']['errorData']['message']): ?>
    <?= $this->parameters['data']['errorData']['message']; ?>
    <? else: ?>
    <?= "Данной страницы не существует"; ?>
    <? endif ?>
</div>
<? if ($this->parameters['data']['errorData']['stackTrace']): ?>
    <pre class="bg-danger">
        <?= $this->parameters['data']['errorData']['stackTrace']; ?>
    </pre>
<? endif ?>