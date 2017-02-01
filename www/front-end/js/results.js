/**
 * Created by Fedor on 22.01.2017.
 */
$(function() {
    $('.moveListBtn').on('click', function () {
        $(this).find('span').toggleClass('glyphicon-chevron-down');
        $(this).find('span').toggleClass('glyphicon-chevron-up');
        $('.moveList').toggleClass('collapse');
    });
});