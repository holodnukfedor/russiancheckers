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
    $('.moveListPanelPrev').on('click', showPrevMoveList);
    $('.moveListPanelNext').on('click', showNextMoveList);

    if ($('.checkersFieldPlace').data('player_side') == 'black') goneToLastPage = true;
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
    $('.gameAreaColumn').removeClass('col-md-12');
    $('.gameAreaColumn').addClass('col-md-9');
    viewConfigureSendServer();
}

function hideRecordMovePanel() {
    $('.recordMovePanel').hide();
    $('.checkersFieldPlace').data('show_move_records', '');
    $('.gameAreaColumn').removeClass('col-md-9');
    $('.gameAreaColumn').addClass('col-md-12');
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
var goneToLastPage = false;
function recordMove(options) {
    var moveStr = options.move.join(':');
    var side = options.side;

    if (goneToLastPage) {
        goneToLastPage = false;
        return;
    }

     var movePagesCount = Number($('.moveListPanelFooter').data('page_count'));
     var trs = $('.moveList').find('tbody').find('tr');
     var movesOnPage = Number($('.moveListPanelFooter').data('on_page'));

     if (movePagesCount != 1) {
         var currentPage = Number($('.moveListPanelFooter').data('page'));
         if (currentPage < movePagesCount) {
             goneToLastPage = true;
             goToMoveListPage(movePagesCount);
        }
     }

    if (side == 'white') {
        if (trs.length == movesOnPage) {
            $('.moveList').find('tbody').empty();

            var movesPage = $('.moveListPanelFooter').data('page');
            $('.moveListPanelFooter').data('page', movesPage + 1);
            $('.moveListPanelFooter').data('page_count', movePagesCount + 1);
            $('.moveListPanelFooter').show();
        }
        $('.moveList').find('tbody').append('<tr><td>' + options.moveNumber + '</td><td>' + moveStr + '</td><td></td></tr>');
    }
    else {
        var lastCell = trs.last().find('td').last();
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
function showPrevMoveList(event) {
    event.preventDefault();
    if (!$(this).hasClass('disabled')) {
        var prevPage = Number($('.moveListPanelFooter').data('page')) - 1;
        goToMoveListPage(prevPage);
    }
}
function showNextMoveList(event) {
    event.preventDefault();
    if (!$(this).hasClass('disabled')) {
        var nextPage =  Number($('.moveListPanelFooter').data('page')) + 1;
        goToMoveListPage(nextPage);
    }
}
function goToMoveListPage(page) {
    $('.moveListPanelPrev').removeClass('disabled');
    $('.moveListPanelNext').removeClass('disabled');
    var getMovesUrl =  $('.moveListPanelFooter').data('get_moves_url');
    $.ajax({
        type: 'POST',
        url: getMovesUrl,
        data: {
            page: page
        },
        success: function(data) {
            movesListLoad(data);
        },
        error: processAjaxError
    });
}
function goToLastMovePageIfNeed() {
    var movePagesCount = Number($('.moveListPanelFooter').data('page_count'));
    if (movePagesCount == 1) return;

    var currentPage = Number($('.moveListPanelFooter').data('page'));
    if (currentPage < movePagesCount) goToMoveListPage(movePagesCount);
}
function movesListLoad(data) {
    var moveListInfo = JSON.parse(data);
    var tbody = $('.moveListPanel').find('tbody').first();
    tbody.empty();

    var paginationInfo = moveListInfo.paginationInfo;
    $('.moveListPanelFooter').data('page', paginationInfo.pageNumber);
    $('.moveListPanelFooter').data('page_count', paginationInfo.pageCount);

    if (paginationInfo.pageNumber == 1) {
        $('.moveListPanelPrev').addClass('disabled');
    }
    else if (paginationInfo.pageNumber == paginationInfo.pageCount) {
        $('.moveListPanelNext').addClass('disabled');
    }

    var moveList = moveListInfo.items;

    for (var i = 0; i < moveList.length; ++i) {
        var moveTr = '<tr>';
        moveTr += '<td>' + moveList[i].number + '</td>';
        moveTr += '<td>' + moveList[i].white_move + '</td>';
        moveTr += '<td>' + moveList[i].black_move + '</td>';
        moveTr += '</tr>';
        tbody.append(moveTr);
    }
}
