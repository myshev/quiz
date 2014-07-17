function initControls(container) {
    $(container).find('.select2').each(function () {
        var $this = $(this);
        if ($this.data('select2')) {
            return;
        }
        var opts = {
            allowClear: attrDefault($this, 'allowClear', !$(this).prop('required'))
        };

        $this.select2(opts);
        $this.addClass('visible');
    });
}

$(document).ready(function () {
    initControls(document.body);
});

$(document).ready(function() {
    $('.remove-element').on('click', function(e) {
        if(!confirm('Подтвердите удаление')) {
            return false;
        }
        return true;
    });
});
