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

<?php
	// main loop for items
	foreach($catalog_items as $catalog_item_details) {
		$item_name = $catalog_item_details['itemName'];
		$item_rate = $catalog_item_details['itemRate'];
		$location_code = $business_locations[$catalog_item_details['locationID']];

		// each item has images.
		foreach($catalog_item_details['images'] as $images) {
			$image_url = $image_end_point.'/'.$location_code.'/'.$images['imageName'];
			echo "<div><img src='$image_url' /></div>";
		}
	}
	exit;
?>


