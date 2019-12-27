$(function() {
    
	if($('.datetimepicker').length){
		$('.datetimepicker').datetimepicker({
			format: 'Y-m-d H:i:s'
		});
	}
	
	$('.is-editable').each(function(){
		$(this).parent().append('<i class="fa fa-pencil make-edit"></i>');
	});
	
	$('body').on('click', '.make-edit', function(){
		_this = this;
		_sib = $(_this).siblings('.is-editable');
		if(!_sib.hasClass('active')){
			_sib.addClass('active');
			$(_this).prev().trigger('focusin');
		}else{
			_field = $(_this).prev().attr('name');
			_val = $(_this).prev().val();
			if(!_val) _val = $(_this).prev().text();
			_val = _val.trim();
			if(_val){
				_obj = {field: _field, value: _val};
				
				if($(_this).parents('.table').hasClass('address')){
					_obj['address_id'] = $('[name="address_id"]').val();
					_obj['name'] = $(_this).prev().find(':selected').text();
				}else if(_field == 'product_seria'){
					_obj['order_product_id'] = $(_this).prev().data('id');
				}else
					_obj['order_id'] = $('[name="order_id"]').val();
				
				$.post(refundUrl.replace('refund_product', 'ajax'), _obj, function(response){
					if(response.success){
						$(_this).val(_val);
						_sib.text(response.data);
						swal({
							title: "Successfully changed!",
							type: "success",
							showCancelButton: false,
							closeOnConfirm: false,
							confirmButtonColor: "green",
							showLoaderOnConfirm: true
						});
					}
					
				});
				_sib.removeClass('active');
			}
		}
	});
	
	
/* 	$('.elem-control').blur(function(){
		_pr = $(this).prev();
		if(_pr.hasClass('active')) _pr.removeClass('active');
	}); */

	$('body').click(function(e){
		if(!$(e.target).is('.make-edit') && !$(e.target).is('.elem-control'))
			$('.is-editable.active').removeClass('active');
	});
	
    $(".styled").uniform({
        radioClass: 'choice'
    });
    
    // Initialize multiple switches
    if (Array.prototype.forEach) {
        var elems = Array.prototype.slice.call(document.querySelectorAll('.switchery'));
        elems.forEach(function(html) {
            var switchery = new Switchery(html);
        });
    }
    else {
        var elems = document.querySelectorAll('.switchery');
        for (var i = 0; i < elems.length; i++) {
            var switchery = new Switchery(elems[i]);
        }
    }

    $(".switch").bootstrapSwitch();

    $('.bootstrap-select').selectpicker();
    $('.select-search').select2();

    // On demand picker
    $('#ButtonCreationDemoButton').click(function (e) {
        $('#ButtonCreationDemoInput').AnyTime_noPicker().AnyTime_picker().focus();
        e.preventDefault();
    });

    $('#ButtonCreationDemoButton1').click(function (e) {
        $('#ButtonCreationDemoInput1').AnyTime_noPicker().AnyTime_picker().focus();
        e.preventDefault();
    });

    $('.remove').on('click', function(e){
        var row = $(this).parent().parent().parent().parent();
        e.preventDefault();
        var href = $(this).attr('href');
        swal({
            title: "Do you realy want to delete",
            type: "error",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonColor: "#F44336",
            showLoaderOnConfirm: true
        },
        function() {
            $.ajax({
                type: 'get',
                url: href,
                dataType: 'json',
                success: function (data) {
                    if(data['success']){
                        swal({
                            title: "Data successfully deleted",
                            type: "success",
                            confirmButtonColor: "#4CAF50"
                        });
                        row.remove();
                    }else{
                        swal({
                            title: "Data can't be deleted",
                            text: data['message'],
                            type: "error",
                            confirmButtonColor: "#F44336"
                        });
                    }
                }
            });
        });
    });

    $('.delete').on('click', function(e){
        var row = $(this).parent().parent().parent().parent();
        e.preventDefault();
        var href = $(this).attr('href');
        swal({
            title: "Do you realy want to delete",
            type: "error",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonColor: "#F44336",
            showLoaderOnConfirm: true
        },
        function() {
            $.ajax({
                type: 'get',
                url: href,
                dataType: 'json',
                success: function (data) {
                    if(data['success']){
                        swal({
                            title: "Data successfully deleted",
                            type: "success",
                            confirmButtonColor: "#4CAF50"
                        });
                        row.remove();
                    }else{
                        swal({
                            title: "Data can't be deleted",
                            text: data['message'],
                            type: "error",
                            confirmButtonColor: "#F44336"
                        });
                    }
                }
            });
        });
    });

    $('.clean').on('click', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        swal({
            title: "Do you realy want to clean all trashed data",
            type: "error",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonColor: "#F44336",
            showLoaderOnConfirm: true
        },
        function() {
            $.ajax({
                type: 'get',
                url: href,
                dataType: 'json',
                success: function (data) {
                    if(data['success']){
                        swal({
                            title: "All trashed data successfully cleaned",
                            type: "success",
                            confirmButtonColor: "#4CAF50"
                        });
                        row.remove();
                    }else{
                        swal({
                            title: "Trashed data can not clean",
                            text: data['message'],
                            type: "error",
                            confirmButtonColor: "#F44336"
                        });
                    }
                }
            });
        });
    });

    $('.delete_permanently').on('click', function(e){
        e.preventDefault();
        var href = $(this).attr('href');
        swal({
            title: "Do you realy want to delete permanently selected trashed data",
            type: "error",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonColor: "#F44336",
            showLoaderOnConfirm: true
        },
        function() {
            $.ajax({
                type: 'get',
                url: href,
                dataType: 'json',
                success: function (data) {
                    if(data['success']){
                        swal({
                            title: "All selected trashed data successfully deleted permanently",
                            type: "success",
                            confirmButtonColor: "#4CAF50"
                        });
                        row.remove();
                    }else{
                        swal({
                            title: "Trashed data can not remove",
                            text: data['message'],
                            type: "error",
                            confirmButtonColor: "#F44336"
                        });
                    }
                }
            });
        });
    });

    $('#language a:first').tab('show');

    $('[data-popup="lightbox"]').fancybox({
        padding: 3
    });

    var id = 1;
    $( 'textarea.editor').each( function() {
        $(this).attr("id","editor"+id);
        CKEDITOR.replace('editor'+id, {
            height: '300px',
            filebrowserBrowseUrl: '/en/administrator/filemanager/index/ckeditor'
        });
        id = id + 1;
    });
    
    // Image Manager
    $(document).on('click', 'a[data-toggle=\'image\']', function(e) {
        var $element = $(this);
        var $popover = $element.data('bs.popover'); // element has bs popover?

        e.preventDefault();

        // destroy all image popovers
        $('a[data-toggle="image"]').popover('destroy');

        // remove flickering (do not re-add popover when clicking for removal)
        if ($popover) {
            return;
        }

        $element.popover({
            html: true,
            placement: 'right',
            trigger: 'manual',
            content: function() {
                return '<button type="button" id="button-image" class="btn btn-primary"><i class="icon-pen6"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="icon-eraser2"></i></button>';
            }
        });

        $element.popover('show');

        $('#button-image').on('click', function() {
            var $button = $(this);
            var $icon   = $button.find('> i');
            $('#modal-image').remove();

            $.ajax({
                url: '/administrator/filemanager/index/popup?target=' + $element.parent().find('input').attr('id') + '&thumb=' + $element.attr('id'),
                dataType: 'html',
                beforeSend: function() {
                    $button.prop('disabled', true);
                    if ($icon.length) {
                        $icon.attr('class', 'icon-spinner4');
                    }
                },
                complete: function() {
                    $button.prop('disabled', false);
                     $(".styled").uniform({
				        radioClass: 'choice'
				    });
                    if ($icon.length) {
                        $icon.attr('class', 'icon-pen6');
                    }
                },
                success: function(html) {
                    $('body').append('<div id="modal-image" class="modal">' + html + '</div>');

                    $('#modal-image').modal('show');
                }
            });

            $element.popover('destroy');
        });

        

        $('#button-clear').on('click', function() {
            $element.find('img').attr('src', $element.find('img').attr('data-placeholder'));

            $element.parent().find('input').val('');

            $element.popover('destroy');
        });
    });

    $(document).ready(function(){
        $('.button-cropper').on('click', function() {
            var $button = $(this);
            var $icon   = $button.find('> i');
    
            $('#modal-image').remove();
    
            $.ajax({
                url: '/administrator/filemanager/cropper',
                dataType: 'html',
                beforeSend: function() {
                    $button.prop('disabled', true);
                    if ($icon.length) {
                        $icon.attr('class', 'icon-spinner4');
                    }
                },
                complete: function() {
                    $button.prop('disabled', false);
                     $(".styled").uniform({
                        radioClass: 'choice'
                    });

                    // Basic setup
    // ------------------------------

    // Default initialization
    $('.crop-basic').cropper();


    // Hidden overlay
    $('.crop-modal').cropper({
        modal: false
    });


    // Fixed position
    $('.crop-not-movable').cropper({
        cropBoxMovable: false,
        data: {
            x: 75,
            y: 50,
            width: 350,
            height: 250
        }
    });


    // Fixed size
    $('.crop-not-resizable').cropper({
        cropBoxResizable: false,
        data: {
            x: 10,
            y: 10,
            width: 300,
            height: 300
        }
    });


    // Disabled autocrop
    $('.crop-auto').cropper({
        autoCrop: false 
    });


    // Disabled drag
    $('.crop-drag').cropper({
        movable: false 
    });


    // 16:9 ratio
    $('.crop-16-9').cropper({
        aspectRatio: 16/9
    });


    // 4:3 ratio
    $('.crop-4-3').cropper({
        aspectRatio: 4/3
    });


    // Minimum zone size
    $('.crop-min').cropper({
        minCropBoxWidth: 150,
        minCropBoxHeight: 150
    });


    // Disabled zoom
    $('.crop-zoomable').cropper({
        zoomable: false
    });


    
    // Demo cropper
    // ------------------------------

    // Define variables
    var $cropper = $(".cropper"),
        $image = $('#demo-cropper-image'),
        $download = $('#download'),
        $dataX = $('#dataX'),
        $dataY = $('#dataY'),
        $dataHeight = $('#dataHeight'),
        $dataWidth = $('#dataWidth'),
        $dataScaleX = $('#dataScaleX'),
        $dataScaleY = $('#dataScaleY'),
        options = {
            aspectRatio: 1,
            preview: '.preview',
            crop: function (e) {
                $dataX.val(Math.round(e.x));
                $dataY.val(Math.round(e.y));
                $dataHeight.val(Math.round(e.height));
                $dataWidth.val(Math.round(e.width));
                $dataScaleX.val(e.scaleX);
                $dataScaleY.val(e.scaleY);
            }
        };

    // Initialize cropper with options
    $cropper.cropper(options);
    $('body').delegate($cropper, cropper(options));


    //
    // Toolbar
    //

    $('.demo-cropper-toolbar').on('click', '[data-method]', function () {
        var $this = $(this),
            data = $this.data(),
            $target,
            result;

        if ($image.data('cropper') && data.method) {
            data = $.extend({}, data);

            if (typeof data.target !== 'undefined') {
                $target = $(data.target);

                if (typeof data.option === 'undefined') {
                    data.option = JSON.parse($target.val());
                }
            }

            result = $image.cropper(data.method, data.option, data.secondOption);

            switch (data.method) {
                case 'scaleX':
                case 'scaleY':
                    $(this).data('option', -data.option);
                break;

                case 'getCroppedCanvas':
                    if (result) {

                        // Init modal
                        $('#getCroppedCanvasModal').modal().find('.modal-body').html(result);

                        // Download image
                        $download.attr('href', result.toDataURL('image/jpeg'));
                    }
                break;
            }
        }
    });


    //
    // Aspect ratio
    //

    $('.demo-cropper-ratio').on('change', 'input[type=radio]', function () {
        options[$(this).attr('name')] = $(this).val();
        $image.cropper('destroy').cropper(options);
    });


    //
    // Switching modes
    //

    // Crop and clear
    var cropClear = document.querySelector('.clear-crop-switch');
    var cropClearInit = new Switchery(cropClear);
    cropClear.onchange = function() {
        if(cropClear.checked) {

            // Crop mode
            $cropper.cropper('crop');

            // Enable other options
            enableDisableInit.enable();
            destroyCreateInit.enable();
        }
        else {

            // Clear move
            $cropper.cropper('clear');

            // Disable other options
            enableDisableInit.disable();
            destroyCreateInit.disable();
        }
    };

    // Enable and disable
    var enableDisable = document.querySelector('.enable-disable-switch');
    var enableDisableInit = new Switchery(enableDisable);
    enableDisable.onchange = function() {
        if(enableDisable.checked) {

            // Enable cropper
            $cropper.cropper('enable');

            // Enable other options
            cropClearInit.enable();
            destroyCreateInit.enable();
        }
        else {

            // Disable cropper
            $cropper.cropper('disable');

            // Disable other options
            cropClearInit.disable();
            destroyCreateInit.disable();
        }
    };

    // Destroy and create
    var destroyCreate = document.querySelector('.destroy-create-switch');
    var destroyCreateInit = new Switchery(destroyCreate);
    destroyCreate.onchange = function() {
        if(destroyCreate.checked) {

            // Initialize again
            $cropper.cropper({
                aspectRatio: 1,
                preview: ".preview",
                data: {
                    x: 208,
                    y: 22
                }
            });

            // Enable other options
            cropClearInit.enable();
            enableDisableInit.enable();
        }
        else {

            // Destroy cropper
            $cropper.cropper('destroy');
            
            // Disable other options
            cropClearInit.disable();
            enableDisableInit.disable();
        }
    };


    //
    // Methods
    //

    // Get data
    $("#getData").on('click', function() {
        $("#showData1").val(JSON.stringify($cropper.cropper("getData")));
    });

    // Set data
    $("#setData").on('click', function() {
        $cropper.cropper("setData", {
            x: 291,
            y: 86,
            width: 158,
            height: 158
        });

        $("#showData1").val('Image data has been changed');
    });


    // Get container data
    $("#getContainerData").on('click', function() {
        $("#showData2").val(JSON.stringify($cropper.cropper("getContainerData")));
    });

    // Get image data
    $("#getImageData").on('click', function() {
        $("#showData2").val(JSON.stringify($cropper.cropper("getImageData")));
    });


    // Get canvas data
    $("#getCanvasData").on('click', function() {
        $("#showData3").val(JSON.stringify($cropper.cropper("getCanvasData")));
    });

    // Set canvas data
    $("#setCanvasData").on('click', function() {
        $cropper.cropper("setCanvasData", {
            left: -50,
            top: 0,
            width: 750,
            height: 750
        });

        $("#showData3").val('Canvas data has been changed');
    });


    // Get crop box data
    $("#getCropBoxData").on('click', function() {
        $("#showData4").val(JSON.stringify($cropper.cropper("getCropBoxData")));
    });

    // Set crop box data
    $("#setCropBoxData").on('click', function() {
        $cropper.cropper("setCropBoxData", {
            left: 395,
            top: 68,
            width: 183,
            height: 183
        });

        $("#showData4").val('Crop box data has been changed');
    });

                    if ($icon.length) {
                        $icon.attr('class', 'icon-pen6');
                    }
                },
                success: function(html) {
                    $('body').append('<div id="modal-image" class="modal">' + html + '</div>');
    
                    $('#modal-image').modal('show');
                }
            });
    
        });
    });

    // Single picker
    $('.daterange-single').daterangepicker({
        singleDatePicker: true,
        locale: {
            format: 'YYYY'
        }
    });

    // Single picker
    $('.daterange-single1').daterangepicker({ 
        singleDatePicker: true,
        locale: {
            format: 'YYYY-MM-DD'
        }
    });

    $('.restore').on('click', function(e){
        var row = $(this).parent().parent().parent().parent();
        e.preventDefault();
        var href = $(this).attr('href');
        swal({
            title: "Do you realy want to delete",
            type: "error",
            showCancelButton: true,
            closeOnConfirm: false,
            confirmButtonColor: "#F44336",
            showLoaderOnConfirm: true
        },
        function() {
            $.ajax({
                type: 'get',
                url: href,
                dataType: 'json',
                success: function (data) {
                    if(data['success']){
                        swal({
                            title: "Data successfully restored",
                            type: "success",
                            confirmButtonColor: "#4CAF50"
                        });
                        row.remove();
                    }else{
                        swal({
                            title: "Data can't be restored",
                            text: data['message'],
                            type: "error",
                            confirmButtonColor: "#F44336"
                        });
                    }
                }
            });
        });
    });

    // Status change
    $('.switch').on('switchChange.bootstrapSwitch', function (event, state) {
        let url = $(this).data('url');
        let id  = $(this).data('id');
        $.post(url+'/changeStatus', { 'id': id });
    });

    // Delete selected elements
    $(document).on('click', 'button#deleteSelectedItems', function(e) {
		
		if(!$('td .checker .checked').length){
			swal({
                title: "Please select Order",
                type: "error",
                showCancelButton: false,
                closeOnConfirm: false,
                confirmButtonColor: "#F44336",
                showLoaderOnConfirm: true
            });
			return false;
		}
		
        let url = $(this).data('href');
        let selected_items = $('form#form-list').serializeArray();
		
        if(selected_items){
            swal({
                title: "Do you realy want to delete",
                type: "error",
                showCancelButton: true,
                closeOnConfirm: false,
                confirmButtonColor: "#F44336",
                showLoaderOnConfirm: true
            },
            function() {
                $.ajax({
                    type: 'post',
                    url: url,
                    dataType: 'json',
                    data : $('form#form-list').serialize(),
                    success: function (data) {
                        
                        if(data['success']){
                            swal({
                                title: data['message'],
                                type: "success",
                                confirmButtonColor: "#4CAF50"
                            });
                            /* selected_items.forEach((element) => {
                                $("input[name='"+element.name+"'][value='"+element.value+"']").parent().parent().parent().parent().remove();
                            }); */
							location.reload();
                        }else{
                            swal({
                                title: data['message'],
                                type: "error",
                                confirmButtonColor: "#F44336"
                            });
                        }
                    }
                });
            });
        }
        
        

    });

    // Remove selected elements
    $(document).on('click', 'button#removeSelectedItems', function(e) {
        let url = $(this).data('href');
        let selected_items = $('form#form-list').serializeArray();
        if(selected_items){
            swal({
                title: "Do you realy want to delete",
                type: "error",
                showCancelButton: true,
                closeOnConfirm: false,
                confirmButtonColor: "#F44336",
                showLoaderOnConfirm: true
            },
            function() {
                $.ajax({
                    type: 'post',
                    url: url,
                    dataType: 'json',
                    data : $('form#form-list').serialize(),
                    success: function (data) {
                        if(data['success']){
                            swal({
                                title: data['message'],
                                type: "success",
                                confirmButtonColor: "#4CAF50"
                            });
                            selected_items.forEach((element) => {
                                $("input[name='"+element.name+"'][value='"+element.value+"']").parent().parent().parent().parent().remove();
                            });
                        }else{
                            swal({
                                title: data['message'],
                                type: "error",
                                confirmButtonColor: "#F44336"
                            });
                        }
                    }
                });
            });
        }
    });

    // Restore selected elements
    $(document).on('click', 'button#restoreSelectedItems', function(e) {
        let url = $(this).data('href');
        let selected_items = $('form#form-list').serializeArray();
        if(selected_items){
            swal({
                title: "Do you realy want to delete",
                type: "error",
                showCancelButton: true,
                closeOnConfirm: false,
                confirmButtonColor: "#F44336",
                showLoaderOnConfirm: true
            },
            function() {
                $.ajax({
                    type: 'post',
                    url: url,
                    dataType: 'json',
                    data : $('form#form-list').serialize(),
                    success: function (data) {
                        if(data['success']){
                            swal({
                                title: data['message'],
                                type: "success",
                                confirmButtonColor: "#4CAF50"
                            });
                            selected_items.forEach((element) => {
                                $("input[name='"+element.name+"'][value='"+element.value+"']").parent().parent().parent().parent().remove();
                            });
                        }else{
                            swal({
                                title: data['message'],
                                type: "error",
                                confirmButtonColor: "#F44336"
                            });
                        }
                    }
                });
            });
        }
    });


});

//  # Bootstrap multiselect
$(function(){
    $('.multiselect').multiselect({
        onChange: function() {
            $.uniform.update();
        }
    });

    // Select All and Filtering features
    $('.multiselect-select-all-filtering').multiselect({
        includeSelectAllOption: true,
        enableFiltering: true,
        enableCaseInsensitiveFiltering: true,
        templates: {
            filter: '<li class="multiselect-item multiselect-filter"><i class="icon-search4"></i> <input class="form-control" type="text"></li>'
        },
        onSelectAll: function() {
            $.uniform.update();
        }
    });

    // Styled checkboxes and radios
    $(".styled, .multiselect-container input").uniform({ radioClass: 'choice'});
    
    $('select[name="per_page"]').change(function(){
        var value = $(this).val();
        window.location =  window.location.pathname+"?per_page="+value;
    });
});




