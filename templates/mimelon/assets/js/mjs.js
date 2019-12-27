if($(".countdown").length > 0) {
   expiredDate  = $("input#countdown1").val();
  if(expiredDate) {
      // Set the date we're counting down to
      var countDownDate = new Date(expiredDate).getTime();
      // Update the count down every 1 second
      var x = setInterval(function() {

          // Get todays date and time
          var now = new Date().getTime();
          
          // Find the distance between now and the count down date
          var distance = countDownDate - now;
          
          // Time calculations for days, hours, minutes and seconds
          var days = Math.floor((distance % (1000 * 60 * 60 * 24 * 12)) / (1000 * 60 * 60 * 24));
          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);

          var days = $('.countdown1days');
          var hrs = $('.countdown1hrs');
          var min = $('.countdown1min');
          var sec = $('.countdown1sec');
          
          days.html(days);
          hrs.html(hours);
          min.html(minutes);
          sec.html(seconds);

          // If the count down is over, write some text 
          if (distance < 0) {
              clearInterval(x);
              // document.querySelector('.countdown').innerHTML = "EXPIRED";
              $('.countdown').text("EXPIRED");
          }
      }, 1000);
  }
}

if($(".countdown").length > 0) {
   expiredDate  = $("input#countdown2").val();
  if(expiredDate) {
      // Set the date we're counting down to
      var countDownDate = new Date(expiredDate).getTime();
      // Update the count down every 1 second
      var x = setInterval(function() {

          // Get todays date and time
          var now = new Date().getTime();
          
          // Find the distance between now and the count down date
          var distance = countDownDate - now;
          
          // Time calculations for days, hours, minutes and seconds
          var days = Math.floor((distance % (1000 * 60 * 60 * 24 * 12)) / (1000 * 60 * 60 * 24));
          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);

          var days = $('.countdown2days');
          var hrs = $('.countdown2hrs');
          var min = $('.countdown2min');
          var sec = $('.countdown2sec');
          
          days.html(days);
          hrs.html(hours);
          min.html(minutes);
          sec.html(seconds);

          // If the count down is over, write some text 
          if (distance < 0) {
              clearInterval(x);
              // document.querySelector('.countdown').innerHTML = "EXPIRED";
              $('.countdown').text("EXPIRED");
          }
      }, 1000);
  }
}

if($(".countdown").length > 0) {
   expiredDate  = $("input#countdown3").val();
  if(expiredDate) {
      // Set the date we're counting down to
      var countDownDate = new Date(expiredDate).getTime();
      // Update the count down every 1 second
      var x = setInterval(function() {

          // Get todays date and time
          var now = new Date().getTime();
          
          // Find the distance between now and the count down date
          var distance = countDownDate - now;
          
          // Time calculations for days, hours, minutes and seconds
          var days = Math.floor((distance % (1000 * 60 * 60 * 24 * 12)) / (1000 * 60 * 60 * 24));
          var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
          var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
          var seconds = Math.floor((distance % (1000 * 60)) / 1000);

          var days = $('.countdown3days');
          var hrs = $('.countdown3hrs');
          var min = $('.countdown3min');
          var sec = $('.countdown3sec');
          
          days.html(days);
          hrs.html(hours);
          min.html(minutes);
          sec.html(seconds);

          // If the count down is over, write some text 
          if (distance < 0) {
              clearInterval(x);
              // document.querySelector('.countdown').innerHTML = "EXPIRED";
              $('.countdown').text("EXPIRED");
          }
      }, 1000);
  }
}

if($(".countdown").length > 0) {
     expiredDate  = $("input#countdown4").val();
    if(expiredDate) {
        // Set the date we're counting down to
        var countDownDate = new Date(expiredDate).getTime();
        // Update the count down every 1 second
        var x = setInterval(function() {

            // Get todays date and time
            var now = new Date().getTime();

            // Find the distance between now and the count down date
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor((distance % (1000 * 60 * 60 * 24 * 12)) / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            var days = $('.countdown4days');
            var hrs = $('.countdown4hrs');
            var min = $('.countdown4min');
            var sec = $('.countdown4sec');

            days.html(days);
            hrs.html(hours);
            min.html(minutes);
            sec.html(seconds);

            // If the count down is over, write some text
            if (distance < 0) {
                clearInterval(x);
                // document.querySelector('.countdown').innerHTML = "EXPIRED";
                $('.countdown').text("EXPIRED");
            }
        }, 1000);
    }
}

if($(".countdown").length > 0) {
     expiredDate  = $("input#countdown5").val();
    if(expiredDate) {
        // Set the date we're counting down to
        var countDownDate = new Date(expiredDate).getTime();
        // Update the count down every 1 second
        var x = setInterval(function() {

            // Get todays date and time
            var now = new Date().getTime();

            // Find the distance between now and the count down date
            var distance = countDownDate - now;

            // Time calculations for days, hours, minutes and seconds
            var days = Math.floor((distance % (1000 * 60 * 60 * 24 * 12)) / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            var days = $('.countdown5days');
            var hrs = $('.countdown5hrs');
            var min = $('.countdown5min');
            var sec = $('.countdown5sec');

            days.html(days);
            hrs.html(hours);
            min.html(minutes);
            sec.html(seconds);

            // If the count down is over, write some text
            if (distance < 0) {
                clearInterval(x);
                // document.querySelector('.countdown').innerHTML = "EXPIRED";
                $('.countdown').text("EXPIRED");
            }
        }, 1000);
    }
}

$('.lang-currency ul.nav li.dropdown').hover(function() {
    $(this).find('.dropdown-menu').stop(true, true).delay(1).fadeIn(20);
  }, function() {
    $(this).find('.dropdown-menu').stop(true, true).delay(1).fadeOut(20);
});

//add heart
$('.add__heart').click(function(){
   product_id = $(this).data('id');
   element = $(this);
  $.ajax({
    type: "POST",
    url: '/favorite/add',
    data: {'product_id': product_id},
    dataType: 'json',
    success: function (response) {
      if(response.success)
      {
        if(response.type == 'inserted') {
            $(element).toggleClass('active');
        } else if(response.type == 'deleted') {
          $(element).toggleClass('active');
        } else {
          alert(response.message);
        }
        $('sup[id=favorite_count]').text(response.count);
        //element.parent().parent().fadeOut();
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      console.log(textStatus, errorThrown);
    }
  });
  
})

//dropdown js
$('.m_dropdown_btn').click(function(){
  $('.m_dropdown_menu').slideToggle(0);
})
$('.size-dropdown-btn').on('click',function(){
  $('.size-dropdown').slideToggle(0);
})

// function slideToggleFunction(param1, param2) {
//   $(param1).hover(function(){
//     $(param2).slideToggle(0);
//   })
// }
// slideToggleFunction($('.sign_in_drop_li'), $('.sign_in_dropdown'));

$('.size-guide-js').on('click',function(e){
  e.stopPropagation();
  $('.size-guide-field-js').toggleClass('display');
});
$(".size-guide-field-js").on("click", function(e){
  e.stopPropagation();
})

$("body").on("click", function(){
  $('.size-guide-field-js').removeClass('display');
});

/* starts remove item function */
function removeItemFunction(removeBtn,removedItem){
  $(removeBtn).click(function(){
     element = $(this);
     product_id = $(this).data('id');
		$.ajax({
			type: "POST",
			url: '/favorite/remove',
			data: {'product_id': product_id},
			dataType: 'json',
			success: function (response) {
				if(response.success)
				{
          element.closest(removedItem).remove();
           current_favorite_count = parseInt($('#favorite_count').text());
          $('sup.heart-count').text(current_favorite_count-1);
				}
				else
				{
					alert(response.message);
				}

			},
			error: function(jqXHR, textStatus, errorThrown) {
				console.log(textStatus, errorThrown);
			}
		});
    
  })
}
removeItemFunction($('.saved-close-js'),$('.saved-item-js'));
/* ends remove item function */

//color
$('.m_dropdown_menu li span').click(function(){
  $('.m_dropdown_btn').text($(this).text());
  $('.m_dropdown_menu').hide();
})

//manual size dropdown
$('.size-dropdown li span').click(function(){
  $('.m_dropdown_btn').text($(this).text());
  $('.size-dropdown').hide();
})

//+ - quantity
$(function() {
      $("div.quantity").append('<a class="inc qty-button"></a><a class="dec qty-button"></a>');
      $(".qty-button").on("click", function() {
       pro = $(this).attr('class').split(' ');
      var $button = $(this);
      var oldValue = $button.parent().find("input").val();

      if (pro[0]=='inc') {
        var newVal = parseInt(oldValue) + 1;
      } else {
       // Don't allow decrementing below zero
        if (oldValue > 0) {
          var newVal = parseInt(oldValue) - 1;
        } else {
          newVal = 0;
        }
      }

      $button.parent().find("input").val(newVal);

    });

  });

  //datepicker
  $(function () {
    $('.datetimepicker1 input').datetimepicker(
      {
        format: 'DD/MM/YYYY'
      }
    );

    //accordion
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
      acc[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.maxHeight){
          panel.style.maxHeight = null;
        } else {
          panel.style.maxHeight = panel.scrollHeight + "px";
        } 
      });
    }


    //filter height according to the all items height
    if (window.matchMedia('(min-width: 992px)').matches) {
      var asideHeight=$('.product-categories-section').height();
      $('.product-categories-aside').css('min-height',asideHeight);
    } else {
      
    }

    // starts category menu/submenu
    var acc_submenu = document.getElementsByClassName("accordion-submenu");
    var i;

    for (i = 0; i < acc_submenu.length; i++) {
      acc_submenu[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var panel = this.nextElementSibling;
        if (panel.style.display === "block") {
          panel.style.display = "none";
        } else {
          panel.style.display = "block";
        }
      });
    }
    // ends category menu/submenu
  
    if($("input[type='range']").length > 0) {
         //starts range slider
      $('input[type="range"]').rangeslider({
        polyfill: false,
      });
    }
    
    $(document).ready(function(){
      var output = $('.range-slider .output');
      var range = $('.range-slider input[type="range"]');
    
      output.text(parseFloat(range.val()));
    
      // function adjusStep ()
    
      if (+range.val() > 5 && +range.attr("step") === 3) {
        range.attr("step", "10");
        range.attr("min", "500");
        range.rangeslider('update', true);
      } else if (+range.val() === 5 && +range.attr("step") === 5) {
        range.attr("step", "3");
        range.attr("min", "500");
        range.rangeslider('update', true);
      }
    
      range.on('input', function() {
        output.text(parseFloat(range.val()));
    
        if (+range.val() > 5 && +range.attr("step") === 3) {
          range.attr("step", "5");
          range.attr("min", "500");
          range.rangeslider('update', true);
        } else if (+range.val() <= 5 && +range.attr("step") === 5) {
          range.attr("step", "3");
          range.attr("min", "500");
          range.rangeslider('update', true);
        }
      });
    });
    //ends range slider

    //starts file reader

    //starts upload front back image
    function readUrls(fileUpload,imgInp){
      function readURL1(input) {

        if (input.files && input.files[0]) {
        var reader = new FileReader();
  
        reader.onload = function(e) {
            $(fileUpload).attr('src', e.target.result);
        }
  
        reader.readAsDataURL(input.files[0]);
        }
        }
  
       $(imgInp).change(function() {
            readURL1(this);
        });
    }

    function readUrlz(fileUpload,imgInp){
      function readURLz1(input) {

        if (input.files && input.files[0]) {
        var reader = new FileReader();
  
        reader.onload = function(e) {
            $(fileUpload).attr('value', e.target.result);
        }
  
        reader.readAsDataURL(input.files[0]);
        }
        }
  
       $(imgInp).change(function() {
            readURLz1(this);
        });
    }

    readUrls($('.file-upload-img1'),$(".imgInp1"));
    readUrlz($('.file-upload-img11'),$(".imgInp1"));
    readUrls($('.file-upload-img2'),$(".imgInp2"));
    readUrlz($('.file-upload-img21'),$(".imgInp2"));
    //ends upload front back image


    //mobile toggle menu
    $('.nav-mobile-icon').on('click',function(){
      $('.mobile-menu-content').addClass('left0');
    })
    $('.mobile-menu-close').on('click',function(){
      $('.mobile-menu-content').removeClass('left0');
      setTimeout(function(){
        $('.transform000').removeClass('transform000');
        $('.sub-sub-content').removeClass('transform-sub-sub');
      }, 10);
    })


    /* mobile registration and profile details for mobile */
    if($( window ).width() < 992){
      var mobileReg = $('.mobile-registration');
      $('.mobile-top-profile').on('click',function(){
        $(mobileReg).addClass('left0');
      })
      $('.mobile-reg-close').on('click',function(){
        $(mobileReg).removeClass('left0');
      })

      $('.faq_sub_mob').on('click',function(){
        $('.faq-sub-category').addClass('transform0');
      })
      $('.faq-sub-close ').on('click',function(){
        $('.faq-sub-category').removeClass('transform0');
      })

     

     
      
    }

    if($(window).width() < 665){
      $('.size-guide-close').on('click',function(){
        $('.size-guide-field-js').removeClass('display');
      })
    }
    
    //categor filter mobile
    var fixedCategory = $('.mobile-fixed-aside');
    $('.filter-mobile').on('click',function(){
      $(fixedCategory).addClass('transform-360');
    })
    $('.mobile-fixed-aside-btn').on('click',function(){
      $(fixedCategory).removeClass('transform-360');
    })

     /*****************[-------------- starts productslider --------------]******************/
    $('.pro-single-zoom-list li').on('click',function(){
      $('.pro-single-zoom-list li').removeClass('active');
      $(this).addClass('active');
    })

      liLength =  $("#pagerUl > li").length;
     $("#prevSlider").on("click", function (){
        if($("#pagerUl li.active").index() !== 0) {
           prevImage = $("#pagerUl li.active").prev();
          prevImage.find("a").trigger("click");
          $("#pagerUl li").removeClass("active");
          prevImage.addClass("active");
        }
     });
     
     $("#nextSlider").on("click", function (){
      if($("#pagerUl li.active").index() !== liLength - 1 ) {
         nextImage = $("#pagerUl li.active").next();
        nextImage.find("a").trigger("click");
        $("#pagerUl li").removeClass("active");
        nextImage.addClass("active");
      }
    });

    $("#pagerUl > li:eq(0)").addClass("active");
    /*****************[-------------- ends productslider --------------]******************/

    /* starts main search */
    $("#all-search").on("keyup", function() {
      if($(this).val().length > 2) {
        $('#mainSearchResult').addClass('searchResultBlock');
      }else{
        $('#mainSearchResult').removeClass('searchResultBlock');
      }
      var value = $(this).val().toLowerCase();
      $("#mainSearchResult li").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
      });
    });

    $('#all-search').on('blur',function(){
      $('#mainSearchResult').removeClass('searchResultBlock');
    })

    $('#mainSearchResult').on('click',function(e){
      e.stopPropagation();
      $('#mainSearchResult').addClass('searchResultBlock');
    })
    $('body').on('click',function(e){
      e.stopPropagation();
      $('#mainSearchResult').hide();
    })


    /* ends main search */

    /* starts my account change */
    $('.change-account-btn').on('click',function(){
      var formElement = $('.main-form-element');
      $(formElement).removeClass('changeble-input');
      $(formElement).removeAttr('disabled');
      $(this).hide();

    })
    /* ends my account change */
  
    /* starts form validation */
    // Wait for the DOM to be ready
  $(function() {
    // Initialize form validation on the registration form.
    
    // It has the name attribute "registration"
    $("#myAccountValidation").validate({
      // Specify validation rules
      rules: {
        firstname: {
          required: true
        },
        lastname: {
          required: true
        },
        email: {
          required: true,
          email: true
        },
        password: {
          required: true,
          password: true,
          minlength: 6
        },
        repeatpassword:{
          required: true,
          repeatpassword: true,
          minlength: 6
        },
        city: {
          required: true
        },
        country: {
          required: true
        },
        postcode:{
          required:true
        },
        mobilenumber:{
          required:true
        }
      },
      // Specify validation error messages
      messages: {
        email: $('#email').next('div.error-message').find('span').eq(0).text(),
        firstname: $('#firstname').next('div.error-message').find('span').eq(0).text(),
        lastname: $('#lastname').next('div.error-message').find('span').eq(0).text(),
        city: $('#city').next('div.error-message').find('span').eq(0).text(),
        country: $('#country').next('div.error-message').find('span').eq(0).text(),
        postcode: $('#postcode').next('div.error-message').find('span').eq(0).text(),
        password:{
          required: $('#password').next('div.error-message').find('span').eq(0).text(),
          minlength: $('#password').next('div.error-message').find('span').eq(0).text()
        },
        repeatpassword:{
          required: $('#repeatpassword').next('div.error-message').find('span').eq(0).text(),
          minlength: $('#repeatpassword').next('div.error-message').find('span').eq(0).text()
        },
        mobilenumber:$('#mobilenumber').next('div.error-message').find('span').eq(0).text()
      },
      // Make sure the form is submitted to the destination defined
      // in the "action" attribute of the form when valid
      submitHandler: function(form) {
        form.submit();
      }
    });
  });
    /* end form validation */
    if(document.querySelectorAll(".mobilenumber-flag").length> 0){
      $(".mobilenumber-flag").intlTelInput();
  }

   $("#myAccountValidation").on("submit", function(e){
    var checkBlank =  $(".number-validation").val().split(" ").length;
    if(checkBlank > 1 ||  $(".number-validation").val().length == 0) {
        e.preventDefault();
        $(".number-validation").parent(".intl-tel-input").next(".error-message").fadeIn();
    }else {
      $(".number-validation").parent(".intl-tel-input").next(".error-message").fadeOut();
    }
  });

  $(".number-validation").on("focus", function(){
    var inputVal = $(this).val().split(" ")[0];
    $(this).val(inputVal);
  })

    /* starts input mask */
    if(document.querySelectorAll("#mobilenumber").length> 0){
      var element = document.getElementById("mobilenumber");           
      element.addEventListener("keyup", function(){
          var val= this.value;
          var reg = /^([\+\d]+)$/;
          if(val.match(reg)) {
          return this.value;
          }else {
          return this.value = this.value.slice(0,this.value.length-1)
          }
      })
    }
    /* ends input mask */


    //mobile menu
    $('.mobile-search2-icon').on('click',function(){
      $('.mobile-search-input').toggleClass('d-block');
    })


    /* review: star rating */
    if($( '#star-rating-2').length>0){
      $( '#star-rating-2').starrating();
    }
    if($( '#star-rating-3').length>0){
      $( '#star-rating-3').starrating();
    }

    //single product slider
    $('.js-zoom-img-nav li a').on('click',function(){
      var src, _this, image;
      _this = $(this);
      image = _this.find("img");
      src = image.attr("src");
      $(".zoomPad img").attr("src", src);
    })

    // wow animation
    new WOW().init();
    //banner-full-slider

    var owlMain = $('.banner-full-slider');
    owlMain.owlCarousel({
      loop:true,
      items:1,
      nav:true,
      autoplay:true,
      autoplayTimeout:5000
    })
    if (owlMain.children().length < 2) {
      $('.banner-full-slider .owl-nav').hide();
    }
    $( ".owl-prev").html(' ');
    $( ".owl-next").html(' ');
    //file multi upload
    function multiFile(fileNumber,fileResult,appendToFile){
      $(fileNumber).on('change',function(){
        $(appendToFile).appendTo($(fileResult));
      })
    }
    multiFile($('.multi1'),$("#multi1"),$( ".MultiFile-list"));


    //file upload
    $('.seller-type-select').change(function(){
       selected = $(".seller-type-select option:selected").val();
      $('.seller-file-upload-cover').show();
      if(selected == 1)
      {
        $('.label_personal').show();
        $('.label_business').hide();
      }
      else if( selected == 2)
      {
        $('.label_personal').hide();
        $('.label_business').show();
      }
    })

    $('#MultiFile1 .multi1').removeAttr('multiple');

    $(document).on('change', '.multi1[name="file"]',(function(e) {
      e.preventDefault();
      
      var file_data = $(this).prop('files')[0];  
      var form_data = new FormData(),
      _this=$(this);                  
      form_data.append('file', file_data);
      $.ajax({
          type:'POST',
          url:'/become_seller/upload',
          data:form_data,
          processData: false,
          contentType: false,
          success:function(data){
              _this.attr('value',data.image);
              _this.attr('name','files[]');
              _this.attr('type','text');
          },
          error: function(data){

          }
      });
      // $(this).remove();
  }));
  
    //description see more
    var descriptionLiCount = $('.des-list-js li').length;
    if(descriptionLiCount < 5){
      $('.des-see-less22').hide();
      $('.des-see-more22').hide();
    }

    $('.des-see-more22').click(function(){
      $('.des-list-js li:hidden').show(300);
          if ($('.des-list-js li').length == $('.des-list-js li:visible').length) {
              $(this).hide();
              $('.des-see-less22').show();
          }
    })

    $('.des-see-less22').click(function(){
      $('.des-list-js li:visible').slice(4).hide(300);
      if ($('.des-list-js li').length == $('.des-list-js li:visible').length) {
        $(this).hide();
        $('.des-see-more22').show();
      }
    })

    //product single zoomPad img
    var ulImgLengthZoomPad = $('.pro-single-zoom-list li').length;
    if( !$('.pro-single-zoom-list').length || !ulImgLengthZoomPad > 0){
      $('.chevron_left').hide();
      $('.chevron_right').hide();
    }

    if($('.product-page-product-wrap .zoomPad img').width() < 400){
      $('.zoomWindow').addClass('hidden');
      $('.zoomPup').addClass('hidden');
    }

    $('.vat-popup22').click(function(){
      $("#VAT").modal();
    })
    $('.shipping-popup22').click(function(){
     $("#Shipping").modal();
    })
    
    
    //price range slider
    $(".js-range-slider").ionRangeSlider({
      onFinish: function (data) {
          $('#data-from').val(data.from_pretty);
          $('#data-to').val(data.to_pretty);
      }
  });


  $('.mobile-menu-list li').not(':first').click(function(){
    $(this).find('.mobile_sub_categories').addClass('transform000');
  });

  $('.sub-sub-mobile li').click(function(){
    $(this).find('.sub-sub-content').addClass('transform-sub-sub');
  })

  $('.faq-sub-breadcramp li:nth-child(1)').on('click',function(e){
    e.preventDefault();
    setTimeout(function() {
      $('.mobile_sub_categories').removeClass('transform000');
      $('.sub-sub-content').removeClass('transform-sub-sub');
    }, 10);
  })

  $('.faq-sub-breadcramp li:nth-child(2)').on('click',function(e){
    e.preventDefault();
    setTimeout(function(){
      $('.sub-sub-content').removeClass('transform-sub-sub');
    }, 10);
  })

  //card info ischecked
  function cardPayment(){
    if($('.free_paid input[value="credit"]').is(':checked')){
      $('.frames-container').show();
    }else{
      $('.frames-container').hide();
    }
  }

  cardPayment();
  
  $('.free_paid input[name="payment_method"]').on("click",function(){
    cardPayment();

    var payment_method = $( 'input[name="payment_method"]:checked').val();
    $.ajax({
      type: "POST",
      url: '/checkout/payment_method',
      data: {'payment_method': payment_method},
      dataType: 'json',
      success: function (response) {
          if(response == true) {
            window.location.href = "/checkout";
          }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
      }
    });
    
  })

  $('.notifyMeBtn').on('click',function(e){
    e.preventDefault();
    var product_id = $('input[name="product_id"]').val();
    var email = $('input[name="subs_email"]').val();
    $.ajax({
      type: "POST",
      url: '/product/stock_notifier',
      data: {'product_id': product_id, 'email': email},
      dataType: 'json',
      success: function (response) {
        
          if(response.success) {
            $('.notifyMeBtn').addClass('active');
            email.val("");
          }
      },
      error: function(jqXHR, textStatus, errorThrown) {
        console.log(textStatus, errorThrown);
      }
    });
    
  })


  
    var paymentForm = document.getElementById('payment-form');
    var payNowButton = document.getElementById('pay-now-button');
    var validationStatus = true;
    if(paymentForm != null)
    {
      if(validationStatus === true) {
        Frames.init({
        publicKey: 'pk_bcd284c3-ae5b-4f7b-b442-fc9286969481',
        containerSelector: '.frames-container',
        cardValidationChanged: function() {
          // if all fields contain valid information, the Pay now
          // button will be enabled and the form can be submitted
          payNowButton.disabled = !Frames.isCardValid();
        },
        cardSubmitted: function() {
          payNowButton.disabled = true;
          // display loader
          }
        });
        
        paymentForm.addEventListener('submit', function(event) {
          event.preventDefault();
          if($('[name="agreement"]').is(':checked')){
            Frames.submitCard()
            .then(function(data) {
            Frames.addCardToken(paymentForm, data.cardToken);
            paymentForm.submit();
            })
            .catch(function(err) {
              // catch the error
              });
          }
        });
    
      }
    }
   

  $('#pay-now-button').on('click',function(){
    if($('.payment-type-choose input').not('[value="credit"]').is(':checked') && $('[name="agreement"]').is(':checked')){
      // $('#payment-form').submit();
        $('#payment-form').submit();
    }
  })


  /* starts custom zoom plugin */
  $('.show').zoomImage();
  $('.show-small-img:first-of-type').css({'border': 'solid 1px #951b25', 'padding': '2px'})
  $('.show-small-img:first-of-type').attr('alt', 'now').siblings().removeAttr('alt')
  $('.show-small-img').click(function () {
    $('#show-img').attr('src', $(this).attr('src'))
    $('#big-img').attr('src', $(this).attr('src'))
    $(this).attr('alt', 'now').siblings().removeAttr('alt')
    $(this).css({'border': 'solid 1px #951b25', 'padding': '2px'}).siblings().css({'border': 'none', 'padding': '0'})
    if ($('#small-img-roll').children().length > 100) {
      if ($(this).index() >= 3 && $(this).index() < $('#small-img-roll').children().length - 1){
        $('#small-img-roll').css('left', -($(this).index() - 2) * 76 + 'px')
      } else if ($(this).index() == $('#small-img-roll').children().length - 1) {
        $('#small-img-roll').css('left', -($('#small-img-roll').children().length - 4) * 76 + 'px')
      } else {
        $('#small-img-roll').css('left', '0')
      }
    }
  })

$('#next-img').click(function (){
  $('#show-img').attr('src', $(".show-small-img[alt='now']").next().attr('src'))
  $('#big-img').attr('src', $(".show-small-img[alt='now']").next().attr('src'))
  $(".show-small-img[alt='now']").next().css({'border': 'solid 1px #951b25', 'padding': '2px'}).siblings().css({'border': 'none', 'padding': '0'})
  $(".show-small-img[alt='now']").next().attr('alt', 'now').siblings().removeAttr('alt')
  if ($('#small-img-roll').children().length > 100) {
    if ($(".show-small-img[alt='now']").index() >= 3 && $(".show-small-img[alt='now']").index() < $('#small-img-roll').children().length - 1){
      $('#small-img-roll').css('left', -($(".show-small-img[alt='now']").index() - 2) * 76 + 'px')
    } else if ($(".show-small-img[alt='now']").index() == $('#small-img-roll').children().length - 1) {
      $('#small-img-roll').css('left', -($('#small-img-roll').children().length - 4) * 76 + 'px')
    } else {
      $('#small-img-roll').css('left', '0')
    }
  }
})

$('#prev-img').click(function (){
  $('#show-img').attr('src', $(".show-small-img[alt='now']").prev().attr('src'))
  $('#big-img').attr('src', $(".show-small-img[alt='now']").prev().attr('src'))
  $(".show-small-img[alt='now']").prev().css({'border': 'solid 1px #951b25', 'padding': '2px'}).siblings().css({'border': 'none', 'padding': '0'})
  $(".show-small-img[alt='now']").prev().attr('alt', 'now').siblings().removeAttr('alt')
  if ($('#small-img-roll').children().length > 100) {
    if ($(".show-small-img[alt='now']").index() >= 3 && $(".show-small-img[alt='now']").index() < $('#small-img-roll').children().length - 1){
      $('#small-img-roll').css('left', -($(".show-small-img[alt='now']").index() - 2) * 76 + 'px')
    } else if ($(".show-small-img[alt='now']").index() == $('#small-img-roll').children().length - 1) {
      $('#small-img-roll').css('left', -($('#small-img-roll').children().length - 4) * 76 + 'px')
    } else {
      $('#small-img-roll').css('left', '0')
    }
  }
})
/* ends custom zoom plugin */

$('.details-qship').on('click', function(){
  var QElement = $(this).attr('data-id');
  $('#' + QElement).modal('show');
})




});

