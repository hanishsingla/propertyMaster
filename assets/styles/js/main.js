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
//  property type
$(document).ready(function () {
    // Define the options for each property type
    const propertyTypeOptions = {
        'residential': {
            'Villa': 'villa',
            'Apartment': 'apartment',
            'Floor': 'floor',
            'Plot': 'plot',
        },
        'commercial': {
            'Office': 'office',
            'Shop': 'Shop',
            'Hotel': 'hotel',
            'Warehouse': 'warehouse',
            'Plot': 'plot',
            'Agricultural/ Farm Land': 'agricultural_farm_land',
        },
    };

    var $propertyTypeSelect = $('[data-property="type"]');

    const $categorySelect = $('[data-property="category"]');

    function updateCategorySelectBox() {
        const propertyTypeValue = $propertyTypeSelect.val();
        const categoryOptions = propertyTypeOptions[propertyTypeValue];

        if(categoryOptions){
            $.ajax({
                type: 'post',
                url: '/createProperty',
                data: {data: categoryOptions},
                error: function (data) {
                    alert('error')
                }
            })
        }
    }

    updateCategorySelectBox();

    $propertyTypeSelect.change(function () {
        updateCategorySelectBox();
    });

});


// Create  property checkbox
const checkedBox = '[type="checkbox"]';

$(checkedBox).on('change', function () {
    $(this).is(':checked') ? $(this).closest('.form-group').find('.property-garage').removeClass('d-none') : $(this).closest('.form-group').find('.property-garage').addClass('d-none')
});

$(checkedBox).trigger('change');

/*==========  Property create  js end ===========*/

/*==========  Property details  js start ===========*/

$('.property-carousel').slick({
    dots: true,
    infinite: true,
    speed: 500,
    fade: true,
    cssEase: 'linear',
});

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

