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

$(body).on('click', '[data-popup="submit"]', function (e) {
    e.preventDefault();
    const $panelbtn = $(this);
    const url = $panelbtn.attr('href');
    const title = $panelbtn.data('title');
    const size = $panelbtn.data('size');
    $.ajax({
        type: 'GET',
        url: url,
        success: function (response) {
            bootbox.dialog({
                title: title,
                size: size || "extra-large",
                centerVertical: true,
                message: response,
                buttons: {
                    cancel: {
                        label: 'close',
                        className: "btn-secondary",
                    },
                    submit: {
                        label: 'submit',
                        className: "btn-success",
                        callback: function () {
                            const $form = $(this).find('form'); // find the form inside the bootbox dialog
                            const formData = new FormData(document.getElementById($form.attr('id')));
                            $.ajax({
                                type: 'POST',
                                processData: false,
                                contentType: false,
                                cache: false,
                                url: url,
                                data: formData,
                                enctype: 'multipart/form-data',
                                success: function (response) {
                                    window.location.reload()
                                }
                            });

                        }
                    },

                }
            })
        }

    })
})

