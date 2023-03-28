import bootbox from 'bootbox';
const body = 'body';

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

/*========== home page Nav tab js start ===========*/
$(body).on('click', '[data-popup="navTab"]', function (e) {
    e.preventDefault();

    $('.nav-link').removeClass('active');
    $(this).addClass('active');

    const $panelbtn = $(this);
    const url = $panelbtn.attr('href');
    $.ajax({
        type: 'GET',
        url: url,
        success: function(data) {
            // Replace the content of the main section with the response data
            $('#content').html(data);
        }
    })
})
/*========== home page Nav tab js end ===========*/