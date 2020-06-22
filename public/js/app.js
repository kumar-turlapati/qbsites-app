$(document).ready(function(){

	AOS.init({
	  duration: 800,
	  easing: 'slide',
	  once: false
	});

	$('.cartPlus').on('click', function(){
		var thisId = $(this).attr('id').split('_')[1];
		var qty = returnNumber(parseInt($('#iqty_'+thisId).val()));
		var itemRate = returnNumber(parseInt($('#rate_'+thisId).text()));
		var newQty = qty+1;
		var totalValue = returnNumber(parseFloat($('#totalAmount').text()));
		$('#iqty_'+thisId).val(newQty);
		updateCartTotal();
	});

	$('.cartMinus').on('click', function(){
		var thisId = $(this).attr('id').split('_')[1];
		var qty = returnNumber(parseInt($('#iqty_'+thisId).val()));
		var itemRate = returnNumber(parseInt($('#rate_'+thisId).text()));
		if(qty > 1) {
			var newQty = qty-1;
			var totalValue = returnNumber(parseFloat($('#totalAmount').text()));
			$('#iqty_'+thisId).val(newQty);
			updateCartTotal();		
		} else {
			bootbox.alert({
		    message: "Minimum one unit is required to place the order.",
			});			
		}
	});

	$('#cb_checkall').on('click', function(){
		var isChecked = $(this).prop('checked');
		$('.itemCheckboxes').prop("checked", isChecked);
	});

	$('.itemCheckboxes').on('click', function(){
		var checked;
	  $('.itemCheckboxes').each(function(i, obj) {
	  	checked = $(this).prop('checked');
	  });
	  if(checked) {
			$('#cb_checkall').prop('checked', true);
	  } else {
			$('#cb_checkall').prop('checked', false);
	  }
	});

	$('.removeItemFromCart').on('click', function(){
		var itemCodes = new Array;
	  $('.itemCheckboxes').each(function(i, obj) {
	  	var checked = $(this).prop('checked');
	  	if(checked) {
		  	var itemCode = $(this).attr('id').split('_')[1];
		  	itemCodes.push(itemCode);
	  	}
	  });
	  if(itemCodes.length === 0) {
			bootbox.alert({
		    message: "Please select an item to remove from the Cart",
			});
			return false;
	  }

    $.ajax("/cart/remove"+'?itemCode='+itemCodes.join()+'&qty=1&cntr=1', {
    	type: "POST",
      success: function(itemDetails) {
      	window.location.reload(true);
      },
      error: function(e) {
				bootbox.alert({
			    message: 'An error occurred :(',
				});
      }
    });
	});

	$('#otpBtn').on('click', function(e){
		e.preventDefault();
		$(this).attr('disabled', true);

		var mobileNo = $('#mobileNumber').val();
		var businessName = $('#businessName').val();
		if(businessName.length === 0) {
			bootbox.alert({
		    message: 'Please enter your personal name (or) business name first',
			});
			$(this).attr('disabled', false);
			return;
		}

		if(mobileNo.length !== 10 ) {
			bootbox.alert({
		    message: 'Invalid mobile no.',
			});
			$('#mobileNumber').val('');
			$(this).attr('disabled', false);
			return;			
		}
		mobileNo = parseInt(mobileNo);
		if(Number.isInteger(mobileNo)) {
	    $.ajax("/send-otp/"+mobileNo, {
	    	type: "POST",
	      success: function(otpResponse) {
	      	// console.log(otpResponse);
	      	if(otpResponse.status) {
	      		$('#otpBtn').hide();
	      		$('#otp').attr('disabled', false);
	      		$('#orderBtn').show();
						setTimeout(function() {
						  $("#resendOtpBtn").show();
						}, 5000);
	      	} else {
						bootbox.alert({
					    message: 'An error occurred while sending Otp :(',
						});
						$(this).attr('disabled', false);
						return false;
	      	}
	      },
	      error: function(e) {
					bootbox.alert({
				    message: 'An error occurred :(',
					});
					$(this).attr('disabled', false);
					return false;
	      }
	    });
		} else {
			bootbox.alert({
		    message: 'Invalid mobile no.',
			});
			$('#mobileNumber').val('');
			$(this).attr('disabled', false);
			return false;			
		}
	});

	$('#resendOtpBtn').on('click', function(e){
		e.preventDefault();
		
		$(this).attr('disabled', true);
		
		var mobileNo = $('#mobileNumber').val();
		var businessName = $('#businessName').val();
		if(businessName.length === 0) {
			bootbox.alert({
		    message: 'Please enter your personal name (or) business name first',
			});
			$(this).attr('disabled', false);
			return;			
		}
		if(mobileNo.length !== 10 ) {
			bootbox.alert({
		    message: 'Invalid mobile no.',
			});
			$('#mobileNumber').val('');
			$(this).attr('disabled', false);
			return;			
		}
		mobileNo = parseInt(mobileNo);
		if(Number.isInteger(mobileNo)) {
	    $.ajax("/send-otp/"+mobileNo, {
	    	type: "POST",
	      success: function(otpResponse) {
	      	if(otpResponse.status) {
						setTimeout(function() {
						  $("#resendOtpBtn").attr('disabled', false);
						}, 8000);
	      	} else {
						bootbox.alert({
					    message: 'An error occurred while sending Otp :(',
						});
						$(this).attr('disabled', false);
						return false;
	      	}
	      },
	      error: function(e) {
					bootbox.alert({
				    message: 'An error occurred :(',
					});
					$(this).attr('disabled', false);
					return false;
	      }
	    });
		} else {
			bootbox.alert({
		    message: 'Invalid mobile no.',
			});
			$('#mobileNumber').val('');
			$(this).attr('disabled', false);
			return false;			
		}
	});	

	$('#orderSubmit').submit(function(e){
		e.preventDefault();
		$(this).attr('disabled', true);
		$('#resendOtpBtn').hide();
		var form = $(this);
		var mobileNo = $('#mobileNumber').val();
		var businessName = $('#businessName').val();
		var otp = $('#otp').val();
		if(businessName.length === 0) {
			bootbox.alert({
		    message: 'Please enter your personal name (or) business name',
			});
			$(this).attr('disabled', false);
			return;			
		}
		if(mobileNo.length !== 10 ) {
			bootbox.alert({
		    message: 'Invalid mobile no.',
			});
			$('#mobileNumber').val('');
			$(this).attr('disabled', false);
			return;			
		}
		if(otp.length !== 4 ) {
			bootbox.alert({
		    message: 'Invalid otp',
			});
			$('#otp').val('');
			$(this).attr('disabled', false);
			return;			
		}

		mobileNo = parseInt(mobileNo);
		otp = parseInt(otp);
		if(Number.isInteger(mobileNo) && Number.isInteger(otp)) {
 			$.ajax({
        type: "POST",
        url: '/order/submit',
        data: form.serialize(),
        success: function(orderResponse) {
        	// console.log(orderResponse);
        	if(!orderResponse.status) {
						bootbox.alert({
					    message: orderResponse.reason,
						});
						return false;
        	} else {
						bootbox.alert({
					    message: 'Your Order has been submitted successfully with Order No. '+orderResponse.orderNo,
					    callback: function() {
					    	var ch = $('#ch').val();
					    	window.location.href = '/catalog/view/'+ch;
					    }
						});
        	}
        },
	      error: function(e) {
					bootbox.alert({
				    message: 'An error occurred while submitting your order :(',
					});
					$(this).attr('disabled', false);
					return false;
	      }
      });			
		} else {
			bootbox.alert({
		    message: 'Invalid mobile no. (or) otp',
			});
			$('#otp, #mobileNumber').val('');
			$(this).attr('disabled', false);
			return;	
		}

	});
});

function updateCartTotal() {
	var totalValue = 0;
  $('.iqtys').each(function(i, obj) {
    var itemCode = $(this).attr('id').split('_')[1];
    var itemQty = parseFloat($(this).val());
    var itemRate = returnNumber(parseFloat($('#rate_'+itemCode).text()));
    var itemValue = itemQty*itemRate;
    totalValue += itemValue;
  });	
  $('#totalAmount').text(totalValue.toFixed(2));
}

function cartOp(itemCode, op, index) {
	if(itemCode.length > 0) {
		if(op === 'add' || op === 'remove') {
			var itemQty = returnNumber(parseInt($('#qty_'+itemCode+'_'+index).val()));
			if(itemQty <= 0) {
				alert('Order qty. must be greater than zero :(');
				return false;
			}
      jQuery.ajax("/cart/"+op+'?itemCode='+itemCode+'&qty='+itemQty+'&cntr='+index, {
      	type: "POST",
        success: function(itemDetails) {
        	var opStatus = itemDetails.status;
        	if(opStatus) {
        		var newCartCount = itemDetails.ic;
        		$('.cartCount').text(newCartCount);
						bootbox.alert({
					    message: 'Item added to Cart successfully',
						});        		
        	} else {
						bootbox.alert({
					    message: itemDetails.reason,
						});
        	}
        },
        error: function(e) {
					bootbox.alert({
				    message: 'An error occurred while adding your item to Cart :(',
					});  
        }
      });
		} else {
			bootbox.alert({
		    message: 'Invalid op!',
			});
		}
	} else {
		bootbox.alert({
	    message: 'Invalid item!',
		});		
	}
}

function returnNumber(val) {
  if(isNaN(val) || val.length === 0) {
    return 0;
  }
  return val;
}

$(window).scroll(function () {
  var scroll = $(window).scrollTop();
  if (scroll >= 250) {
    $("header").addClass("navbarSticky");
  } else {
    $("header").removeClass("navbarSticky");
  }
});
