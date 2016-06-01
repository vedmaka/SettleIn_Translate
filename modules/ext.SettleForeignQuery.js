(function(mw){

    /**
     * @constructor
     */
    var foreignQuery = function( element ) {
        this.currentTitle = $(element).data('currenttitle');
        this.selfForeignSource = $(element).data('selfsource') || false;
        this.element = $(element);
        this.init();
        this.fetchPages();
    };

    foreignQuery.prototype.fetchPages = function() {
        var self = this;
        var domains = mw.config.get('wgSettleTranslateDomains');
        if( !domains ) {
            return false;
        }

        var itemsFound = 0;

        if( this.selfForeignSource && this.selfForeignSource.length ) {
            var langCode = this.selfForeignSource.split(':');
            var link = '#';
            if( langCode.length && domains[langCode[0]] ) {
                link = '//' + domains[langCode[0]] + '/index.php?title=' + langCode[1];
            }
            this.renderItem( langCode[1] + ' (' +  mw.config.get('translateLangCodes')[langCode[0]] + ')', link );
            itemsFound += 1;
        }
        $.each( domains, function( langCode, domain ) {
            var link = '//' + domain + '/api.php?origin='+ mw.config.get('wgServer') +'&format=json&action=foreign&page=' + self.currentTitle;
            $.get( link, function(data) {
               var r = data.foreign;
                if( r && r.length ) {
                    $.each( r, function( i, item ){
                       self.renderItem( item['title'] + ' (' + mw.config.get('translateLangCodes')[langCode] + ')', item['link'] );
                        itemsFound += 1;
                    });
                }
            });
        });
    };

    foreignQuery.prototype.renderItem = function( text, link )
    {
        this.element.find('.foreign-list-result-text').remove();
        var li = $('<li />');
        $(li).append('<a href="'+ link +'" target="_blank">' + text + '</a>');
        this.element.find('ul').append( li );
    };

    foreignQuery.prototype.init = function() {
        this.element.append( $('<ul />') );
    };

    window.foreignQuery = foreignQuery;

})(mediaWiki);