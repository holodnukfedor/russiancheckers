/**
 * Created by Fedor on 15.01.2017.
 */
$(function() {
    $('.lightDiffButton').on('click', function () {
        $('.newGameConf').attr('class', 'panel panel-success newGameConf');
        $('.newGameConf > .panel-body').attr('class', 'panel-body bg-success');
        $('#difficultyLevel').val('light');
    });
    $('.mediumDiffButton').on('click', function () {
        $('.newGameConf').attr('class', 'panel panel-default newGameConf');
        $('.newGameConf > .panel-body').attr('class', 'panel-body bg-default');
        $('#difficultyLevel').val('medium');
    });
    $('.hardDiffButton').on('click', function () {
        $('.newGameConf').attr('class', 'panel panel-danger newGameConf');
        $('.newGameConf > .panel-body').attr('class', 'panel-body bg-danger');
        $('#difficultyLevel').val('hard');
    });

    $('.difficultyLvlBtn').on('click', function() {
        $('.newGameConf').hide();
        $('.newGameConf').slideDown()
    });

    $('.blackSideLabel').on('click', function () {
        $('#color').val('black');
        $('.whiteSideLabel').removeClass('selectedSideLabel');
        $('.blackSideLabel').addClass('selectedSideLabel');
    });
    $('.whiteSideLabel').on('click', function () {
        $('#color').val('white');
        $('.blackSideLabel').removeClass('selectedSideLabel');
        $('.whiteSideLabel').addClass('selectedSideLabel');
    });

});