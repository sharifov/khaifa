$(document).ready(function(){

"use strict";

$('.pro-owl-carousel').owlCarousel({
    nav:true,
    loop:false,
    responsiveClass:true,
    responsive : {
        1300:{
            items:6,
        },
        1280:{
            items:6,
        },
        992:{
            items:4
        },
        500:{
            items:3
        },
        0: {
            items:2
        }
    }
})
$('.pc-slider-item .owl-carousel').owlCarousel({
    nav:true,
    loop:false,
    responsiveClass:true,
    responsive : {
        1300:{
            items:5
        },
        1280:{
            items:5
        },
        992:{
            items:4
        },
        500:{
            items:3
        },
        0: {
            items:2
        }
    }
})
$( ".owl-prev").html('');
$( ".owl-next").html('');

//  i Check plugin
$('.i-check, .i-radio').iCheck({
    checkboxClass: 'i-check',
    radioClass: 'i-radio'
});

// price slider
$("#price-slider").ionRangeSlider({
    min: 130,
    max: 575,
    type: 'double',
    prefix: "$",
    prettify: false,
    hasGrid: false
});

$('#jqzoom').jqzoom({
    zoomType: 'standard',
    lens: true,
    preloadImages: false,
    alwaysOn: false,
    zoomWidth: 460,
    zoomHeight: 460,
    // xOffset:390,
    yOffset: 0,
    position: 'left'
});

/* become a seller toggle text */
$('.new_account_terms_more').on('click',function(){
    $('.new_account_terms_info').slideToggle();
})

$('.form-group-cc-number input').payment('formatCardNumber');
$('.form-group-cc-date input').payment('formatCardExpiry');
$('.form-group-cc-cvc input').payment('formatCardCVC');

// Register account on payment
$('#create-account-checkbox').on('ifChecked', function() {
    $('#create-account').removeClass('hide');
});

$('#create-account-checkbox').on('ifUnchecked', function() {
    $('#create-account').addClass('hide');
});

$('#shipping-address-checkbox').on('ifChecked', function() {
    $('#shipping-address').removeClass('hide');
});

$('#shipping-address-checkbox').on('ifUnchecked', function() {
    $('#shipping-address').addClass('hide');
});


// $('.owl-carousel').each(function(){
//   $(this).owlCarousel();
// });


// Lighbox gallery
$('#popup-gallery').each(function() {
    $(this).magnificPopup({
        delegate: 'a.popup-gallery-image',
        type: 'image',
        gallery: {
            enabled: true
        }
    });
});

// Lighbox image
$('.popup-image').magnificPopup({
    type: 'image'
});

// Lighbox text
$('.popup-text').magnificPopup({
    removalDelay: 500,
    closeBtnInside: true,
    callbacks: {
        beforeOpen: function() {
            this.st.mainClass = this.st.el.attr('data-effect');
        }
    },
    midClick: true
});

$(".product-page-qty-plus").on('click', function() {
    var currentVal = parseInt($(this).prev(".product-page-qty-input").val(), 10);

    if (!currentVal || currentVal == "" || currentVal == "NaN") currentVal = 0;

    $(this).prev(".product-page-qty-input").val(currentVal + 1);
});

$(".product-page-qty-minus").on('click', function() {
    var currentVal = parseInt($(this).next(".product-page-qty-input").val(), 10);
    if (currentVal == "NaN") currentVal = 1;
    if (currentVal > 1) {
        $(this).next(".product-page-qty-input").val(currentVal - 1);
    }
});

/* starts product single slider */
var owlSmallRolls= $('.small-rolls');
    owlSmallRolls.owlCarousel({
        nav: true,
        margin:8,
        loop: false,
        responsive : {
            500:{
                items:4
            },
            0: {
                items:3
            }
        }
    })

    var smallRollItemCount = 0 ;
    $('#small-img-roll .small-roll-item').each(function(){
        smallRollItemCount ++;
    })
    if(smallRollItemCount < 4){
        $('.small-rolls .owl-nav').addClass('none');
    }else{
        $('.small-rolls .owl-nav').removeClass('none');
    }
    /* ends product single slider */
    

})