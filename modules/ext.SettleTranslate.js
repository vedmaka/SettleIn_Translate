$(function(){

    $(document).on('click', '#translate-window #settle-translate-translate-btn', function() {
        var selectedOption = $('#translate-window select').val();
        var link = decodeURIComponent( selectedOption );
        $('#translate-window form').prop('action', link);
        $('#translate-window form').submit();
    });

    $(document).on('show.bs.modal', '#translate-window', function(e){

        var translations = window.wgPageAvailableTranslations || [];
        $('#translate-window select option').each(function(i,v){
            var lang = $(v).data('lang');
            if( lang && ($.inArray( lang, translations ) != -1) ) {
                $(v).prop('disabled', true);
            }
        });

    });

});