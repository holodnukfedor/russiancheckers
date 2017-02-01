/**
 * Created by Fedor on 24.12.2016.
 */
Checkers.defaultOptions = {//возможно нахзвания классов занести еще сюда и почему не prototype не знаю
    checkerFieldSize: 40,
    blackFieldClass: 'blackField',
    whiteFieldClass: 'whiteField',
    borderWidth: 10,
    borderClass: 'centerCheckFieldBorder',
    descriptionStrSize: 20,
    descriptionStrClass: 'blackField',
    checkDescFont: 'checkersDescFont',
    checkPosClass: 'checkPos',
    spawnClass: 'spawn',
    blackSpawnClass: 'spawnBlack',
    whiteSpawnClass: 'spawnWhite',
    chosenSpawnCs: 'chosenSpawn',
    canMoveCs: 'canMove'
};
function Checkers(options) //можно описать обработку ошибок, возможно стоит изменить не забивать классы магическими числами
{
    //куда прицеплять поле шашек
    this.placeSelector = options.placeSelector;//обработать ошибку
    this.gameServiceUrl = options.gameServiceUrl;
    this.resultUrl = options.resultUrl;
    this.fieldRowSize = 8;
    this.checkerFieldSize = options.checkerFieldSize || Checkers.defaultOptions.checkerFieldSize;
    this.blackFieldClass = options.blackFieldClass || Checkers.defaultOptions.blackFieldClass;
    this.whiteFieldClass = options.whiteFieldClass || Checkers.defaultOptions.whiteFieldClass;
    this.borderWidth = options.borderWidth || Checkers.defaultOptions.borderWidth;
    this.borderClass = options.borderClass || Checkers.defaultOptions.borderClass;
    this.descriptionStrSize = options.descriptionStrSize || Checkers.defaultOptions.descriptionStrSize;
    this.descriptionStrClass = options.descriptionStrClass || Checkers.defaultOptions.descriptionStrClass;
    this.checkDescFont = options.checkDescFont || Checkers.defaultOptions.checkDescFont;
    this.blackSpawnClass = options.blackSpawnClass || Checkers.defaultOptions.blackSpawnClass;
    this.whiteSpawnClass = options.whiteSpawnClass || Checkers.defaultOptions.whiteSpawnClass;
    this.checkers = options.checkers || [];
    this.availableMoves = options.availableMoves || [];
    this.spawnSizeDifferencePortion = 0.15625;
    this.queenImgPortion = 0.875;
    this.checkersQueenImgBlack = '/front-end/img/checkers/queenWhite64.png';
    this.checkersQueenImgWhite = '/front-end/img/checkers/queenBlack64.png';
    this.executingMove = null;
    this.isBattleMove = false;
    this.enemyMove = options.enemyMove || null;
    this._enemyAnimIndex = 0;

    this.chosenSpawnCs = options.chosenSpawnCs || Checkers.defaultOptions.chosenSpawnCs;
    this.canMoveCs = options.canMoveCs ||  Checkers.defaultOptions.canMoveCs;
    this.playState = {
        side: options.side,
        chosenPos: null,
        canSelect: true,
        isAnimating: false
    };

    this.afterPlayerMoveHandler = options.afterPlayerMoveHandler || null;
    this.afterEnemyMoveHandler = options.afterEnemyMoveHandler || null;
    this.afterSelect = options.afterSelect || null;
    this.inBattleMoveSelect = options.inBattleMoveSelect || null;

    this._whiteScore = 0;
    this._blackScore = 0;
    this._moveNumber = 1;

    this.init();
    this.placeCheckers();
    this.setSizes();

    if (options.isEnemyMove) {
        this.playState.canSelect = false;
        this.showEnemyMove();
    }
}
Checkers.prototype.init = function() {
    var upDescStr = $('<div class="' + this.descriptionStrClass + ' horDescStr"></div>');
    var leftDescStr = $('<div class="' + this.descriptionStrClass + ' floatLeft vertDescStr"></div>');

    for (var i = 0; i < this.fieldRowSize; ++i) {
        var horDescCell = $('<div class="checkDescCell ' + this.checkDescFont + ' horCheckDescCell">' + String.fromCharCode(97 + i)+ '</div>');
        upDescStr.append(horDescCell);
        var vertDescCell = $('<div class="checkDescCell floatLeft ' + this.checkDescFont + ' vertCheckDescCell">' + (this.fieldRowSize - i) + '</div>');
        leftDescStr.append(vertDescCell);
    }

    var rightDescStr = leftDescStr.clone();
    var downDescStr = upDescStr.clone();
    downDescStr.addClass('clearBoth');

    var checkFieldCenter = $('<div class="floatLeft ' + this.borderClass + ' checkFieldCenter"></div>');

    for (var i = this.fieldRowSize - 1; i >= 0; --i) {
        for (var j = 0; j < this.fieldRowSize; ++j) {
            var checkersField = $('<div class="floatLeft checkField" data-pos="' + String.fromCharCode(97 + j).toString() + (i + 1).toString() + '"></div>');

            if ((i + j) % 2 != 0) checkersField.addClass(this.whiteFieldClass);
            else checkersField.addClass(this.blackFieldClass);
            // checkersField.addClass(Checkers.defaultOptions.checkPosClass + String.fromCharCode(97 + j).toString() + (i + 1).toString());
            checkFieldCenter.append(checkersField);
        }
        checkFieldCenter.append('<div class="clearBoth"></div>');
    }
    $(this.placeSelector).append(upDescStr);
    $(this.placeSelector).append(leftDescStr);
    $(this.placeSelector).append(checkFieldCenter);
    $(this.placeSelector).append(rightDescStr);
    $(this.placeSelector).append(downDescStr);

    $('.checkField').on('click', Checkers.prototype.onCheckerClick.bind(this));
};
Checkers.prototype.setSizes = function () {
    var maxWidth = (this.checkerFieldSize * this.fieldRowSize + (this.descriptionStrSize + this.borderWidth) * 2);
    var maxHeight = (this.checkerFieldSize * 8 + this.borderWidth * 2);

    $(this.placeSelector).find('.horDescStr').css({
        'width' : maxWidth + 'px',
        'height' : this.descriptionStrSize,
        'padding' : '0px ' + (this.borderWidth + this.descriptionStrSize) + 'px'
    });
    $(this.placeSelector).find('.vertDescStr').css({
        'width' : this.descriptionStrSize,
        'height' : maxHeight + 'px',
        'padding': this.borderWidth + 'px 0px'
    });

    $(this.placeSelector).find('.horCheckDescCell').css({
        'width' : this.checkerFieldSize + 'px',
        'height' : this.descriptionStrSize + 'px'
    });
    $(this.placeSelector).find('.vertCheckDescCell').css({
        'width' : this.descriptionStrSize + 'px',
        'height' : this.checkerFieldSize + 'px'
    });
    $(this.placeSelector).find('.checkFieldCenter').css({
        'width' : maxHeight + 'px',
        'height' : maxHeight + 'px',
        'border-width': this.borderWidth + 'px'
    });
    $(this.placeSelector).find('.checkField').css({
        'width' : this.checkerFieldSize + 'px',
        'height' : this.checkerFieldSize + 'px'
    });

    var spawnSizeDiff = Math.round(this.checkerFieldSize * this.spawnSizeDifferencePortion);
    var spawnSize = this.checkerFieldSize - spawnSizeDiff;
    var spawnPadding = Math.round(spawnSizeDiff / 2);
    var borderRadius = Math.round(spawnSize / 2);
    $(this.placeSelector).find('.spawn').css({
        top: spawnPadding + 'px',
        left: spawnPadding + 'px',
        width: spawnSize + 'px',
        height: spawnSize + 'px',
        '-moz-border-radius': borderRadius + 'px',
        '-webkit-border-radius': borderRadius + 'px',
        'border-radius': borderRadius + 'px'
    });
    var queenImgSize = this.checkerFieldSize * this.queenImgPortion;
    $(this.placeSelector).find('.checkersQueenImg').css({
        width: queenImgSize + 'px',
        height: queenImgSize + 'px',
    });
};
Checkers.prototype.placeCheckers = function () {
    var spawn = $('<div class="' + Checkers.defaultOptions.spawnClass + '"></div>')
    for (var i = 0; i < this.checkers.length; ++i) {
        var currentSpawn = spawn.clone();
        if (this.checkers[i].isBlack) {
            currentSpawn.addClass(this.blackSpawnClass);
            if (this.checkers[i].isQueen) {
                currentSpawn.append('<img class="checkersQueenImg" src="' + this.checkersQueenImgBlack + '"/>');
            }
        }
        else {
            currentSpawn.addClass(this.whiteSpawnClass);
            if (this.checkers[i].isQueen) {
                currentSpawn.append('<img class="checkersQueenImg" src="' + this.checkersQueenImgWhite + '"/>');
            }
        }

        $(this.placeSelector).find('.checkField[data-pos=' + this.checkers[i].pos + ']').append(currentSpawn);
    }
}
Checkers.prototype.onCheckerClick = function (event, inBattleMoveCalled) {
    var checkField = $(event.currentTarget);
    if (!this.playState.isAnimating && checkField.hasClass(this.canMoveCs)) {
        this._moveSpawn(checkField);
    }
    else if ((this.playState.canSelect || inBattleMoveCalled) && checkField.children().length > 0) {
        if (!inBattleMoveCalled) this.isBattleMove = false;
        this._selectSpawn(checkField, inBattleMoveCalled);
    }
    return false;
}
Checkers.prototype._deselectMoves = function (pos) {
    $('.checkField[data-pos=' + pos + ']').removeClass(this.chosenSpawnCs);
    for (var key in this.availableMoves) {
        if (this.availableMoves[key][0] == pos) {
            var canMovePos = this.availableMoves[key][1];
            $('.checkField[data-pos=' + canMovePos + ']').removeClass(this.canMoveCs);
        }
    }
}
Checkers.prototype._selectMoves = function (pos) {
    $('.checkField[data-pos=' + pos + ']').addClass(this.chosenSpawnCs);
    for (var key in this.availableMoves) {
        if (this.availableMoves[key][0] == pos) {
            var canMovePos = this.availableMoves[key][1];
            $('.checkField[data-pos=' + canMovePos + ']').addClass(this.canMoveCs);
        }
    }
}
Checkers.prototype._deleteSpawns = function (move) {
    for (var i = 0; i < move.length - 1; ++i) {
        var curMove = move[i];
        var nextMove = move[i + 1];

        var curXPos = curMove.charCodeAt(0) + (curMove.charCodeAt(0) < nextMove.charCodeAt(0)?1 :-1);
        var curYPos = Number(curMove[1]) + (curMove[1] <  nextMove[1]?1 :-1);
        while(curXPos != nextMove.charCodeAt(0)) {
            if ($('.checkField[data-pos=' + (String.fromCharCode(curXPos).toString() +  curYPos.toString()) + ']').children().length > 0) {
                $('.checkField[data-pos=' + (String.fromCharCode(curXPos).toString() + curYPos.toString()) + ']').empty();
                this.isBattleMove = true;
            }
            curXPos += (curMove.charCodeAt(0) < nextMove.charCodeAt(0)?1 :-1);
            curYPos += (curMove[1] <  nextMove[1]?1 :-1);
        }
    }
}
Checkers.prototype._setSpawnPadding = function (chosenSpawn) {
    var spawnSizeDiff = Math.round(this.checkerFieldSize * this.spawnSizeDifferencePortion);
    var spawnPadding = Math.round(spawnSizeDiff / 2);
    chosenSpawn.css({
        top: spawnPadding + 'px',
        left: spawnPadding + 'px'
    });
}
Checkers.prototype._setAvailableMovesInBattleMove = function (newPos) {
    var newAvailableMoves = [];
    for (var key in this.availableMoves) {
        if (this.availableMoves[key][0] == this.playState.chosenPos && this.availableMoves[key][1] == newPos
            && this.availableMoves[key].length > 2) {
            newAvailableMoves.push(this.availableMoves[key].slice(1));
        }
    }
    return newAvailableMoves;
}
Checkers.prototype._selectSpawn = function (checkField, inBattleMoveCalled) {
    if (this.playState.side == 'black' && checkField.find('.spawn').eq(0).hasClass('spawnBlack') ||
        this.playState.side == 'white' && checkField.find('.spawn').eq(0).hasClass('spawnWhite')) {
        if (this.playState.chosenPos) this._deselectMoves(this.playState.chosenPos);
        var pos = checkField.data('pos');
        this.playState.chosenPos = pos;
        this._selectMoves(pos);
        if (inBattleMoveCalled) {
            if (typeof(this.inBattleMoveSelect) == "function") this.inBattleMoveSelect();
        }
        else {
            if (typeof(this.afterSelect) == "function") this.afterSelect();
        }
        if (!inBattleMoveCalled) this.executingMove = [pos];
    }
}
Checkers.prototype._moveSpawn = function (checkField) {
    var currentPos = checkField.data('pos');

    var xCordShift = this.playState.chosenPos.charCodeAt(0) - currentPos.charCodeAt(0);
    var yCordShift = this.playState.chosenPos[1] - currentPos[1];
    var chosenCheckField = $('.checkField[data-pos=' + this.playState.chosenPos + ']');
    var chosenSpawn = chosenCheckField.find('.spawn').eq(0);
    var oldZIndex = chosenSpawn.css('z-index');
    chosenSpawn.css('z-index', this._maxZIndex());

    var self = this;
    this.playState.isAnimating = true;
    chosenSpawn.animate({
        left: "-=" + (this.checkerFieldSize * xCordShift),
        top: "+=" + (this.checkerFieldSize * yCordShift)
    }, {
        complete: function () {
            self._setSpawnPadding(chosenSpawn);
            checkField.append(chosenSpawn);
            chosenSpawn.css('z-index', oldZIndex);
            self._checkBecameQueen(chosenSpawn, currentPos);

            self._deselectMoves(self.playState.chosenPos);
            self.playState.isAnimating = false;

            self.executingMove.push(currentPos);

            var newAvailableMoves = self._setAvailableMovesInBattleMove(currentPos);
            self.playState.canSelect = false;
            if (newAvailableMoves.length > 0) {
                self.availableMoves = newAvailableMoves;
                checkField.trigger('click', true);

            }
            else {
                self.playState.chosenPos = null;
                self._deleteSpawns(self.executingMove);
                $.ajax ({
                    type: 'POST',
                    url: self.gameServiceUrl,
                    data:  {
                        movePos: self.executingMove,
                        isBattleMove: self.isBattleMove
                    },
                    success: function (data) {
                        var response = JSON.parse(data);
                        if (response.result) self._redirectToResult();

                        self.enemyMove = response.enemy_move;
                        self.availableMoves = response.available_moves;
                        self._whiteScore = response.whiteScore;
                        self._blackScore = response.blackScore;
                        self._moveNumber = response.move_number;
                        if (typeof(self.afterPlayerMoveHandler) == "function") self.afterPlayerMoveHandler({
                            move: self.executingMove,
                            side: self.playState.side,
                            whiteScore: self._whiteScore,
                            blackScore: self._blackScore,
                            moveNumber: self._moveNumber
                        });
                        self.executingMove = null;
                        self.showEnemyMove();
                        // после хода врага проверить есть ли ходы игрока, если нет то показ на результат проигрыш
                    },
                    error: processAjaxError
                });
            }
        }
    });
}
Checkers.prototype._checkBecameQueen = function (currentSpawn, currentPos) {
    if (currentSpawn.children().length == 0)
        if (currentSpawn.hasClass(this.blackSpawnClass) && currentPos[1] == '1') {
            var queenImgSize = this.checkerFieldSize * this.queenImgPortion;
            currentSpawn.append('<img class="checkersQueenImg" src="' + this.checkersQueenImgBlack + '" style="width: ' + queenImgSize + 'px; height: ' + queenImgSize + 'px;"/>');
        }
        else if (currentSpawn.hasClass(this.whiteSpawnClass) && currentPos[1] == '8') {
            var queenImgSize = this.checkerFieldSize * this.queenImgPortion;
            currentSpawn.append('<img class="checkersQueenImg" src="' + this.checkersQueenImgWhite + '" style="width: ' + queenImgSize + 'px; height: ' + queenImgSize + 'px;"/>');
        }
}
Checkers.prototype.showEnemyMove = function () {
    this._enemyAnimIndex = 0;
    var chosenSpawn = $('.checkField[data-pos=' + this.enemyMove[0] + ']').find('.spawn').eq(0);
    var oldZIndex = chosenSpawn.css('z-index');
    chosenSpawn.css('z-index', this._maxZIndex());
    var self = this;

    for (var i = 0; i < this.enemyMove.length - 1; ++i) {
        var xCordShift = this.enemyMove[i].charCodeAt(0) - this.enemyMove[i + 1].charCodeAt(0);
        var yCordShift = this.enemyMove[i][1] - this.enemyMove[i + 1][1];

        var animationOptions = {
            'complete': function() {
                self._checkBecameQueen(chosenSpawn, self.enemyMove[self._enemyAnimIndex + 1])
                if (self._enemyAnimIndex == self.enemyMove.length - 2) {
                    chosenSpawn.css('z-index', oldZIndex);
                    self.enemyMoveAnimationComplete(chosenSpawn);
                }
                ++self._enemyAnimIndex;
            }
        }

        chosenSpawn.animate({
            left: "-=" + (this.checkerFieldSize * xCordShift),
            top: "+=" + (this.checkerFieldSize * yCordShift)
        }, animationOptions);
    }
}
Checkers.prototype.enemyMoveAnimationComplete = function (chosenSpawn) {
    this._deleteSpawns(this.enemyMove);
    var destinationCheckField = $('.checkField[data-pos=' + this.enemyMove[this.enemyMove.length - 1] + ']');
    this._setSpawnPadding(chosenSpawn);
    destinationCheckField.append(chosenSpawn);

    if (this.availableMoves.length == 0) this._redirectToResult();

    this.playState.canSelect = true;
    if (typeof(this.afterEnemyMoveHandler) == "function") this.afterEnemyMoveHandler({
        move: this.enemyMove,
        side: (this.playState.side == 'white' ? 'black' : 'white'),
        whiteScore: this._whiteScore,
        blackScore: this._blackScore,
        moveNumber: this._moveNumber
    });
}
Checkers.prototype._maxZIndex = function () {
    var highest_index = 0;
    var elements =document.getElementsByTagName('*');
    for (var i = 0; i < elements.length - 1; i++) {
        if (parseInt(elements[i].style.zIndex) > highest_index) {
            highest_index = parseInt(elements[i].style.zIndex);
        }
    }
    return highest_index + 1;
}
Checkers.prototype._redirectToResult = function() {
    document.location.replace(this.resultUrl);
}



