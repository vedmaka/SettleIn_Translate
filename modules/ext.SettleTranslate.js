$(function(){

    $(document).on('click', '#translate-window #settle-translate-translate-btn', function() {
        var selectedOption = $('#translate-window select').val();
        if( !selectedOption || !selectedOption.length ) {
            return false;
        }
        var link = decodeURIComponent( selectedOption );
        $('#translate-window form').prop('action', link);
        $('#translate-window form').submit();
        $('#translate-window').modal('hide');
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

    $(document).on('click', '.translate-sublink', function(e){
        e.preventDefault();
        $('#translate-link').click();
    });

});
