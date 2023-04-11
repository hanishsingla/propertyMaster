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
$(document).ready(function() {
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

    var $propertyTypeSelect = $('#property_propertyType');

    var $categorySelect = $('#property_propertyCategory');

    function updateCategorySelectBox() {
        var propertyTypeValue = $propertyTypeSelect.val();
        var categoryOptions = propertyTypeOptions[propertyTypeValue];
        $categorySelect.empty();

        $.each(categoryOptions, function(key, value) {
            $categorySelect.append($('<option></option>').attr('value', value).text(key));
        });
    }

    // Update the category select box on page load
    updateCategorySelectBox();

    // Update the category select box when the property type select box changes
    $propertyTypeSelect.change(function() {
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

/*==========  Property details  js end ===========*/

