$(function(){

    $(document).on('click', '#translate-window #settle-translate-translate-btn', function() {
        var selectedOption = $('#translate-window select').val();
        var link = decodeURIComponent( selectedOption );
        $('#translate-window form').prop('action', link);
        $('#translate-window form').submit();
    });

});