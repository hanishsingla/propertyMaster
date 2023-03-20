import bootbox from 'bootbox';

const body = 'body';
$(body).on('click', '[data-popup="model"]', function (e) {
    e.preventDefault();
    const $panelbtn = $(this);
    const url = $panelbtn.attr('href');
    const title = $panelbtn.data('title');
    const size = $panelbtn.data('size');
    const id = $panelbtn.data('id');

    $.ajax({
        type: 'GET',
        url: url,
        success: function (response) {
            bootbox.dialog({
                title: title,
                size: size || 'extra-large',
                centerVertical: true,
                message: response,
            })
            $(document).ready(function () {
            $('#' + id).on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    type: 'post',
                    url: url,
                    async: true,
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    dataType: 'JSON',
                })
                location.reload()
            })
        })

        }

    })
})