;(function($){
    
    var ns = flat.namespace('components.DataGrid');
    
    var datagridInstances = {};
    
    var DataGrid = function(el){
        
        var _self = this;
        
        //Internal
        var page = 1;
        var rowsPerPage = 5;
        var fetchUrl = getGridAttr('data-url-src');
        var params = getGridAttr('data-params');
        var check = getGridAttr('data-check');
        var id = getGridAttr('data-grid-id');
        var columns = JSON.parse(getGridAttr('data-columns'));
        var searchValue = '';
        
        //Html
        var $tbody = el.find('tbody');
        var $searchField = el.find('.-dg-search-input');
        var $searchButton = el.find('.-dg-search-button');
        var $clearSearchButton = el.find('.-dg-clear-search-button');
        var $paginationBox = el.find('.-dg-pagination');
        var $loadingMask = el.find('.-dg-loading-mask');
        var $rowsPerPageOption = el.find('.-dg-option-rpp');
        
        /**
         * Fetch Data
         * @returns {undefined}
         */
        this.fetch = function(){
            
            var timeout = setTimeout(function(){
                $loadingMask.show();
            }, 100);
            
            $.post(
                fetchUrl,
                {
                    page: page,
                    rowsPerPage: rowsPerPage,
                    params: params,
                    check: check,
                    search: searchValue
                }, null, 'json'
            ).done(function(data){
                
                //Draw rows
                var rowCount = data['rows'].length;
                var resultsHtml = '';
                for(var i = 0; i < rowCount; i++){
                    
                    var rs = data['rows'][i];
                    resultsHtml += '<tr>';
                    
                    for(var k in rs){
                        var align = columns[k]['align'] || 'left';
                        resultsHtml += '<td style="text-align: '+align+';">'+rs[k]+'</td>';
                    }
                    
                    resultsHtml += '</tr>';
                    
                }
                
                if(!rowCount){
                    resultsHtml = '<tr><td colspan="'+columns.length+'">\
                        No results found</td></tr>';
                }
                
                $tbody.html(resultsHtml);
                
                //Draw pagination
                $paginationBox.html(data['paginationHtml']);
                
            }).fail(function(){
                $tbody.html('');
            }).always(function(){
                clearTimeout(timeout);
                $loadingMask.hide();
            });
            
        };
        
        /**
         * 
         * @returns {undefined}
         */
        this.reset = function(){
            $searchField.val('');
            setSearch('');
        };
        
        /**
         * Get datagrid element attr value then removes it
         * 
         * @param {string} attrName
         * @returns {string}
         */
        function getGridAttr(attrName){
            var val = el.attr(attrName);
            el.removeAttr(attrName);
            return val;
        }
        
        /**
         * 
         * @param {string} _search
         * @returns {undefined}
         */
        function setSearch(_search){
            page = 1;
            searchValue = _search;
            $clearSearchButton.toggle(!!searchValue.length);
            _self.fetch();
        }
        
        /**
         * 
         * @param {int} _page
         * @returns {undefined}
         */
        function setPage(_page){
            if(!isNaN(_page)){
                page = _page;
            }else{
                page = 1;
            }
            _self.fetch();
        }
        
        /**
         * 
         * @param {int} _rpp
         * @returns {undefined}
         */
        function setRowsPerPage(_rpp){
            if(!isNaN(_rpp)){
                rowsPerPage = parseInt(_rpp);
            }else{
                rowsPerPage = 10;
            }
            
            if(rowsPerPage < 5 || rowsPerPage > 50){
                rowsPerPage = 5;
            }
            
            page = 1;
            _self.fetch();
        }
        
        //DOM listeners//
        
        //Search button click
        $searchButton.click(function(e){
            e.preventDefault();
            setSearch($.trim($searchField.val()));
        });
        
        //"Return" detected on search text input
        $searchField.keydown(function(e){
           if(e.which == 13) {
               e.preventDefault();
               $searchButton.trigger('click');
           }
        });
        
        //Clear search
        $clearSearchButton.click(function(e){
            e.preventDefault();
            $searchField.val('');
            setSearch('');
        });
        
        //Pagination link clicked
        $paginationBox.on('click', '[href]', function(e){
            e.preventDefault();
            var link = $(this).attr('href');
            setPage(link);
        });
        
        //Select number of rows per page
        $rowsPerPageOption.change(function(){
            setRowsPerPage($(this).val());
        });
        
        datagridInstances[id] = _self;
        
    };
    
    /**
     * 
     * @param {string} id
     * @returns {DataGrid}
     */
    ns.get = function(id){
        return datagridInstances[id];
    };
    
    //DomListener
    flat.script.load(function(context){
        context.find('.-data-grid-component').each(function(){
           var dg = new DataGrid($(this));
           dg.fetch();
        });
    });
    
})(jQuery);