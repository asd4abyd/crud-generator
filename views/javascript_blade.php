<script>

    (function($) {
        $(document).on('click', 'form button.delete', function() {
            if(confirm('You going to delete this record, Are you sure?')) {
                $(this).parent().submit();
            }
        });


        $(document).on('keypress', 'input.numeric', function(e) {

            var key = e.which || e.charCode;
            if(key < 20){
                return false;
            }
            if(!/\d/.test(String.fromCharCode(key))){
                return false;
            }
        });

        $(document).on('keyup', 'input.numeric', function() {
            var $this = $(this);

            $this.val(String($this.val()).replace(/[^\d]/g,''))
        });

        $(document).on('keypress', 'input.decimal', function(e) {

            var key = e.which || e.charCode;
            if(key < 20){
                return false;
            }
            if(!/[\d.]/.test(String.fromCharCode(key))){
                return false;
            }

            if(String($(this).val()).indexOf('.')>-1 && String.fromCharCode(key)=='.') {
                return false
            }

        });

        $(document).on('keyup', 'input.decimal', function() {
            var $this = $(this);
            var $value = String($this.val());
            var posDot = $value.indexOf('.')+1;

            $this.val($value.substring(0, posDot).replace(/[^\d\.]/g,'')+$value.substring(posDot).replace(/[^\d]/g,''))
        });
    })(jQuery);

</script>
