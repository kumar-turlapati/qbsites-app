<?php
  use FrameWork\Utilities;
 	$cart_url = '/catalog/view/'.$_SESSION['catalog_hash'];
?>

<?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>

	<section>
	  <div class="cartSection">
	    <div class="leftGroup">
	      <div class="cartForm">
	        <div class="formHeader">
	          <div class="checkBox">
	            <input type="checkbox" id="cb_checkall" /><label for="cb_checkall">Select All</label>
	          </div>
	          <div class="deleteItem">
	            <button class="removeItemFromCart"><img src="/images/close.svg" alt="Remove" /></button>
	          </div>
	        </div>
	        <div class="formBody">
	        	<?php 
	        		$total_amount = 0;
	        		foreach($_SESSION['cart'] as $item_key => $cart_details):
	        			$image_key 			= 	$cart_details['imageCntr'];
	        			$cart_qty 			= 	$cart_details['qty'];

	        			$product_name 	= 	isset($_SESSION['catalog'][$item_key]['itemName']) ? $_SESSION['catalog'][$item_key]['itemName'] : '';
	        			$product_desc 	= 	isset($_SESSION['catalog'][$item_key]['itemDescription']) ? $_SESSION['catalog'][$item_key]['itemDescription'] : '';
	        			$product_rate 	= 	isset($_SESSION['catalog'][$item_key]['itemRate']) ? $_SESSION['catalog'][$item_key]['itemRate'] : '';
	        			$product_image 	= 	isset($_SESSION['catalog_images'][$image_key]) ? $_SESSION['catalog_images'][$image_key] : '';

	        			$total_amount  += 	round($cart_qty*$product_rate, 2);
	        	?>

	        		<?php if($total_amount > 0): ?>
			          <div class="itemRow">
			            <div class="cartLeftGroup">
			              <div class="checkBox">
			                <input type="checkbox" id="cb_<?php echo $item_key ?>" name="cbCartItems" class="itemCheckboxes" />
			                <label for="cb_<?php echo $item_key ?>">&nbsp;</label>
			              </div>
			              <div class="productImg">
			                <div class="imgWrap"><img src="<?php echo $product_image ?>" alt="" /></div>
			              </div>
			              <div class="productInfo">
			                <h3><a href="#"><?php echo $product_name ?></a></h3>
			                <?php if($product_desc !== ''): ?>
			                	<p><?php echo $product_desc ?></p>
			                <?php endif; ?>
			              </div>
			            </div>
			            <div class="cartRightGroup">
			              <div class="qtyGroup">
			                <button id="cminus_<?php echo $image_key ?>" class="cartMinus">
			                	<img src="/images/ic_minus.png" alt="Remove from Cart" />
			                </button>
			                <input
			                	id="iqty_<?php echo $image_key ?>" 
			                	type="text" 
			                	value="<?php echo $cart_qty ?>"
			                	class="iqtys"
			                />
			                <button id="cplus_<?php echo $image_key ?>" class="cartPlus">
			                	<img src="/images/ic_plus.png" alt="Add to Cart" />
			                </button>
			              </div>
			              <div class="price">₹<span id="rate_<?php echo $image_key ?>"><?php echo number_format($product_rate, 2, '.', '') ?></span></div>
			            </div>
			          </div>
		        	<?php endif; ?>
	        	

	        	<?php 
	        		endforeach; 
	        		// redirect user to shopping page, if there are no qtys added.
	        		if($total_amount <= 0) {
	        			Utilities::redirect($cart_url);
	        		}
	        	?>
	          <div class="priceBox">
	            <div class="priceRow totalPrice">
	              <span>Total: </span>
	              <div class="price">₹<span id="totalAmount"><?php echo number_format($total_amount, 2 , '.', '') ?></span></div>
	            </div>
	            <div class="noteText">*All items are subject to the availability. Price excludes applicable taxes and duties</div>
	          </div>
	          <!-- bottom actions -->
	          <div class="bottomActions">
	          	<form id="orderSubmit" method="POST" autocomplete="off">
		            <div class="userForm">
		              <div class="formCol">
		              	<input type="text" placeholder="Business name / Personal name" id="businessName" name="businessName" maxlength="100" />
		              </div>
		              <div class="formCol">
		              	<input type="text" placeholder="Mobile number" id="mobileNumber" name="mobileNumber" maxlength="10" />
		              </div>
		              <div class="formCol">
		              	<input type="text" placeholder="Enter OTP" id="otp" name="otp" disabled="disabled" maxlength="4" />
		              </div>
		            </div>
		            <button class="btn btn-primary" id="orderBtn" name="orderBtn" style="display:none;">Place Order</button>&nbsp;
		            <button class="btn btn-primary" id="otpBtn" name="otpBtn">Send OTP</button>
		            <button class="btn btn-normal" id="resendOtpBtn" name="resendOtpBtn" style="display:none;">Resend OTP</button>
	          	</form>
	          </div>
	        </div>
	      </div>
	    </div>
	  </div>
	</section>

<?php else: ?>

	<section>
		<div class="cartSection">
			Your Cart is empty. <a href="<?php echo $cart_url ?>">Shop Now</a>
		</div>
	</section>

<?php endif; ?>


<?php /*
<div class="errorMsg">Your order has failed</div>
<div class="successMsg">Your order has been placed successfully</div> */ ?>
