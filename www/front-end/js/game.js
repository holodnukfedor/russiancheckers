/**
 * Created by Fedor on 03.01.2017.
 */
$(function() {
    initializeGame();
    $('.closeRecordMovePanel').on('click', hideRecordMovePanel);
    $('.gameHelpClose').on('click', hideGameHelp);
    $('#configureDialog').on('show.bs.modal', showViewConfigure);
    $('.saveViewConfigure').on('click', saveViewConfigure);
    $('.offerDrawBtn').on('click', offerDraw);
    $('#drawResponseDialog').on('hidden.bs.modal', offerDrawResponseClose);
});

function initializeGame() {
    var white_checkers_str = $('.checkersFieldPlace').data('white_checkers');
    var black_checkers_str = $('.checkersFieldPlace').data('black_checkers');
    var checkers = parseCheckersString(white_checkers_str, 0);
    checkers = checkers.concat(parseCheckersString(black_checkers_str, 1));
    var checkersGame = new Checkers({
        placeSelector : '.checkersFieldPlace',
        gameServiceUrl : '/GameVsComputer/MakeMove',
        resultUrl: '/GameVsComputer/Result',
        checkers: checkers,
        availableMoves: $('.checkersFieldPlace').data('available_moves'),
        enemyMove: $('.checkersFieldPlace').data('enemy_move'),
        isEnemyMove: $('.checkersFieldPlace').data('is_enemy_move'),
        side: $('.checkersFieldPlace').data('player_side'),
        afterPlayerMoveHandler: function(options) {
            recordMove(options);
            showOpponentMoveLabel();
            updateScoreInfo(options);
            setGameHelp(' Подождите пока выполниться ход противника.');
        },
        afterEnemyMoveHandler: function (options) {
            recordMove(options);
            recordLastEnemyMove(options);
            showPlayerMoveLabel();
            updateScoreInfo(options);
            setGameHelp(' Для выбора фигуры, которой будете совершать ход кликните по ней левой кнопкой мыши.');
        },
        afterSelect: function () {
            setGameHelp(' Зеленым цветом выделены клетки на которые вы можете переместить выбранную фигуру.' +
                ' Если есть свободные поля, но они не выделены. Значит на данный момент вы должны совершить ход боем. ' +
                'Обратите внимание на другие фигуры');
        },
        inBattleMoveSelect: function () {
            setGameHelp('В данный момент вы выполняете боевой ход. Вы не можете выбрать другую фигуру до окончания боевого хода.' +
                ' Вы можете переместить пешку на выделенные зеленым клетки.' +
                ' По окончании хода фигуры через которые вы переместились будут сняты с доски. ');
        }
    });
}

function showGameHelp() {
    $('.gameHelp').show();
    $('.checkersFieldPlace').data('show_tips', true);
    viewConfigureSendServer();
}

function hideGameHelp() {
    $('.gameHelp').hide();
    $('.checkersFieldPlace').data('show_tips', '');
    viewConfigureSendServer();
}

function showRecordMovePanel() {
    $('.recordMovePanel').show();
    $('.checkersFieldPlace').data('show_move_records', true);
    $('.gameAreaColumn').toggleClass('col-md-9');
    $('.gameAreaColumn').toggleClass('col-md-12');
    viewConfigureSendServer();
}

function hideRecordMovePanel() {
    $('.recordMovePanel').hide();
    $('.checkersFieldPlace').data('show_move_records', '');
    $('.gameAreaColumn').toggleClass('col-md-9');
    $('.gameAreaColumn').toggleClass('col-md-12');
    viewConfigureSendServer();
}

function viewConfigureSendServer() {
    var viewConfigure = {
        showTips: $('.checkersFieldPlace').data('show_tips'),
        showMoveRecord: $('.checkersFieldPlace').data('show_move_records')
    };
    $.ajax({
        type: 'POST',
        url: '/GameVsComputer/SetViewConfigure',
        data: viewConfigure,
        success: function(data) {
        },
        error: processAjaxError
    });
}

function saveViewConfigure() {
    if ($('#showTipsInput').prop('checked')) showGameHelp();
    else hideGameHelp();

    if ($('#showMoveRecordsInput').prop('checked')) showRecordMovePanel();
    else hideRecordMovePanel();


    $('#configureDialog').modal('hide');
}

function parseCheckersString(checkers_str, isBlack) {
    var checkers = new Array();
    for (var i = 0; i < checkers_str.length; ++i) {
        var checker = { pos: checkers_str.substr(i, 2), isBlack: isBlack, isQueen: +checkers_str.charAt(i + 2) }
        checkers.push(checker);
    }
    return checkers;
}
function recordMove(options) {
    var moveStr = options.move.join(':');
    var side = options.side;


    if (side == 'white') {
        if (options.moveNumber == 1 && $('.moveList').find('tbody').find('tr').length == 1) return;
        $('.moveList').find('tbody').append('<tr><td>' + options.moveNumber + '</td><td>' + moveStr + '</td><td></td></tr>');
    }
    else {
        var lastCell = $('.moveList').find('tbody').find('tr').last().find('td').last();
        lastCell.text(lastCell.text() + ' ' + moveStr);
    }
}
function recordLastEnemyMove(options) {
    $('#lastEnemyMove').text( options.move.join(':'));
}
function showPlayerMoveLabel () {
    $('.whoseMoveLabelParent').empty();
    $('.whoseMoveLabelParent').append('<span class="yourMoveLabel">Ваш ход</span>');
}
function showOpponentMoveLabel () {
    $('.whoseMoveLabelParent').empty();
    $('.whoseMoveLabelParent').append(' <span class="opponentMoveLabel">Ход оппонента</span>');
}
function updateScoreInfo(options) {
    if (options.side == 'black') $('.blackScoreContainer').text(options.blackScore);
    if (options.side == 'white') $('.whiteScoreContainer').text(options.whiteScore);
}

function showViewConfigure() {
    var showTips = $('.checkersFieldPlace').data('show_tips');
    var showMoveRecords = $('.checkersFieldPlace').data('show_move_records');

    if (showTips) $('#showTipsInput').prop('checked', true);
    else $('#showTipsInput').prop('checked', false);

    if (showMoveRecords) $('#showMoveRecordsInput').prop('checked', true);
    else $('#showMoveRecordsInput').prop('checked', false);
}

function setGameHelp(message) {
    $('.gameHelpText').text(message);
}

function offerDraw () {
    $('#offerDrawDialog').modal('hide');
    $.ajax({
        type: 'POST',
        url: $(this).attr('href'),
        data: '',
        success: function(data) {
            var response = JSON.parse(data);
            if (response.agreed) {
                $('#drawResponseDialog').data('agreed', true);
                $('.drawResponseText').first().text('Игрок согласился на ничью. ');

                $('#drawResponseDialog').modal('show');
            }
            else {
                $('.drawResponseText').first().text('Игрок отказался от ничьи. ');
                $('#drawResponseDialog').modal('show');
            }
        },
        error: processAjaxError
    });
    $('#offerDrawDialog').modal('hide');
}
function offerDrawResponseClose() {
    var resultUrl = $('#offerDrawDialog').data('result_url');
    if ($('#drawResponseDialog').data('agreed')) document.location.replace(resultUrl);
}
