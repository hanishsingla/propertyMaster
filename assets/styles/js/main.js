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
    var propertyTypeOptions = {
        'residential': {
            'Single-family home': 'single_family_home',
            'Apartment': 'apartment',
            'Condominium': 'condominium',
            'Townhouse': 'townhouse',
            'Mobile home': 'mobile_home',
        },
        'commercial': {
            'Office': 'office',
            'Retail store': 'retail_store',
            'Hotel': 'hotel',
            'Warehouse': 'warehouse',
        },
        'agricultural': {
            'Crop field': 'crop_field',
            'Orchard': 'orchard',
            'Cattle ranch': 'cattle_ranch',
        },
    };

    var $propertyTypeSelect = $('[data-property="type"]');

    var $categorySelect = $('[data-property="category"]');

    function updateCategorySelectBox() {
        const propertyTypeValue = $propertyTypeSelect.val();
        const categoryOptions = propertyTypeOptions[propertyTypeValue];
        $categorySelect.empty();

        $.each(categoryOptions, function (key, value) {
            $categorySelect.append($('<option></option>').attr('value', value).text(key));
        });
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

$('.heart-icon').on('click', function (e) {
    e.preventDefault();
    const $heart = $(this);
    const $heartIcon = $heart.find('i.fa-heart');
    const url = $heart.attr('href');

    $.ajax({
        type: 'POST',
        url: url,
        success: function (data) {

        }
    })
    $heartIcon.toggleClass('fa-regular fa-heart fa-solid fa-heart');
});

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
                console.log(response);
            },
            error: function (error) {
                console.log(error);
            }
        });
    });
});

/*==========  Property details  js end ===========*/

