/**
 * Created by Fedor on 29.01.2017.
 */
$(function() {
    $('#ajaxErrorDialog').on('hidden.bs.modal', ajaxErrorDialogHide);
});
function processAjaxError(xhr, textStatus, errorThrown){
    var response = JSON.parse(xhr.responseText);

    $('.ajaxErrorText').text('Возникла ошибка: ' + response.message);
    $('#ajaxErrorDialog').data('code', response.code);
    $('#ajaxErrorDialog').modal('show');
}
function ajaxErrorDialogHide() {
    if ($('#ajaxErrorDialog').data('code') == 401) document.location.replace('/Auth/Login');
}