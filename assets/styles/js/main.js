import bootbox from 'bootbox';
import 'slick-carousel'

const body = 'body';

$(body).on('click', '[data-popup="submit"]', function (e) {
    e.preventDefault();
    const $panelBtn = $(this);
    const url = $panelBtn.attr('href');
    const title = $panelBtn.data('title');
    const size = $panelBtn.data('size');
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

    const $panelBtn = $(this);
    const url = $panelBtn.attr('href');
    $.ajax({
        type: 'GET',
        url: url,
        success: function (data) {
            // Replace the content of the main section with the response data
            $('#homeListing').html(data);
        }
    })
})
/*========== home page Nav tab js end ===========*/

/*==========  Property create  js start ===========*/


// Create  property checkbox
const checkedBox = '[type="checkbox"]';

$(checkedBox).on('change', function () {
    $(this).is(':checked') ? $(this).closest('.form-group').find('.property-garage').removeClass('d-none') : $(this).closest('.form-group').find('.property-garage').addClass('d-none')
});

$(checkedBox).trigger('change');

/*==========  Property create  js end ===========*/

/*==========  Property details  js start ===========*/

/*==========  carousel js start ===========*/

let carousel = '.carousel';
let show = $(carousel).data('show');
let scroll = $(carousel).data('scroll');
let dots = $(carousel).data('dots');
let arrows = $(carousel).data('arrows');
show = show ? parseInt(show) : 3;
scroll = scroll ? parseInt(scroll) : 3;

$(carousel).slick(
    {

        dots: dots || false,
        arrows: arrows || false,
        slidesToShow: show,
        slidesToScroll: scroll,
        autoplay: true,
        cssEase: 'linear',
        infinite: true,
        adaptiveHeight:true,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow: show,
                    slidesToScroll: scroll,
                    infinite: true,
                    adaptiveHeight:true,
                    dots: dots || false,
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: show,
                    slidesToScroll: scroll,
                    adaptiveHeight:true,
                    dots: dots || false,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: show,
                    slidesToScroll: scroll,
                    adaptiveHeight:true,
                    dots: dots || false,
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });

/*==========  carousel js end ===========*/

/*==========  Favourite property  js start ===========*/
$(document).ready(function () {
    $(body).on('click', '[data-input="checked"]', function () {
        const $input = $(this)
        const id = $input.data('id');
        const url = $input.data('href');
        const isChecked = $input.is(':checked');
        $.ajax({
            url: '/' + url + '/' + id,
            method: 'POST',
            data: {data: isChecked},
            success: function (response) {
                $input.parent().find('.fa-heart').toggleClass('fa-heart fa-heart fa-beat');
            },
            error: function (error) {
                console.log(error);
            }
        });
    });
});
/*==========   Favourite property   js start ===========*/
/*==========  Property details  js end ===========*/


/*==========  Password hide show js start ===========*/

$(body).on('click', '[data-click="eye"]', function (e) {
    const id = $(this).prev().attr('id');
    const passwordField = $('#' + id);
    const fieldType = passwordField.prop('type');

    if (fieldType === 'password') {
        passwordField.prop('type', 'text');
    } else {
        passwordField.prop('type', 'password');
    }
    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
});
/*========== Password hide show js end ===========*/

