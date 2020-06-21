<?php
  use Framework\Utilities;
  use Framework\Config\Config;

  $s3_config = Config::get_s3_details();

  $business_details = $catalog_details['businessDetails'];
  $catalog_items = $catalog_details['catalogItems'];
  $catalog_name = $catalog_details['catalogName'];
  $catalog_desc = $catalog_details['catalogDesc'];

	// arrange locations in array
	foreach($catalog_details['businessLocations'] as $location_details) {
		$business_locations[$location_details['locationID']] = $location_details['locationCode'];
	}
	
	unset($catalog_details['catalogItems']);  
	unset($catalog_details['businessDetails']);  
	unset($catalog_details['businessLocations']);

 	$image_end_point = 'https://'.$s3_config['BUCKET_NAME'].'.'.$s3_config['END_POINT_FULL'].'/'.$org_code;

 	// dump($catalog_items);
 	// exit;
?>

<div class="productImages">
	<?php
		// main loop for items
		$cntr = 0;
		foreach($catalog_items as $catalog_item_details) {
			$item_name = $catalog_item_details['itemName'];
			$item_code = $catalog_item_details['itemCode'];
			$item_rate = $catalog_item_details['itemRate'];
			$location_code = $business_locations[$catalog_item_details['locationID']];

			// each item has images.
			foreach($catalog_item_details['images'] as $images) {
				$image_url = $image_end_point.'/'.$location_code.'/'.$images['imageName'];
				$cntr = $cntr + 1;
				$_SESSION['catalog_images'][$cntr] = $image_url;
	?>
				<div class="item">
				  <div data-aos="fade-up">
				    <img src="<?php echo $image_url ?>" alt="Image" class="img-fluid">
				    <div class="imageOverlay"></div>
				    <div class="hoverIcons">
				      <div class="imageView">
				        <a href="<?php echo $image_url ?>" data-fancybox="gallery">
				          <img src="/images/ic_view.png" alt="View" />
				        </a>
				      </div>
				      <div class="qty">
				      	<input type="text" value="1" id="qty_<?php echo $item_code.'_'.$cntr ?>" />
				      </div>
				      <button class="addToCart" id="btn_<?php echo $item_code.'_'.$cntr ?>" onclick="cartOp('<?php echo $item_code?>', 'add', <?php echo $cntr ?>)">
				      	<img src="/images/ic_buy.png" alt="Add to cart" />
				      </button>
				    </div>
				    <div class="productInfo">
				      <div class="infoWrap">
				        <div class="infoText">
				          <h3 class="productName"><?php echo $item_name ?></h3>
				          <p class="productText"><?php //echo $item_text ?></p>
				        </div>
				        <?php if((float)$item_rate>0): ?>
				        	<div class="price">₹<span><?php echo number_format($item_rate,2,'.','') ?></span></div>
				        <?php endif; ?>
				      </div>
				    </div>
				  </div>
				</div>
	<?php
			}
	} 
	?>
</div>