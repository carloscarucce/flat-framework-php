;(function($){
    
    flat.script.load(function(context){
    
        /**
         * types: date/datetime
         */
        context.find(':input[f-type="datetime"], :input[f-type="date"]')
        .not('[readonly], [disabled]')
        .each(function(){

            var $field = $(this);
            $field.datetimepicker({
                format: $field.attr('dtpicker-format') || ''
            });

        });
        
        /**
         * type: float
         */
        context.find(':input[f-type="float"]')
        .not('[readonly], [disabled]')
        .each(function(){
           
            var $field = $(this);
            var ts = $field.attr('f-ts');
            var ds = $field.attr('f-ds');
            var prec = new Array( parseInt($field.attr('f-precision') || 0) + 1 ).join( '0' );
            $field.mask("#"+ts+"##0"+ds+prec, {reverse: true});
                    
        });
        
        context.find(':input[disabled],:input[readonly]').unbind();

    }, true);
    
})(jQuery);