<?php
	$is_app_exists = $ios_url !== '' || $android_url !== '' ? true : false;
?>

<div class="gridContainer">
  <div class="footerTop">
  	<?php if($is_app_exists): ?>
    	<div class="appDownload">For more galleries and latest updates <br /><button class="btn btn-primary">Download APP</button></div>
    <?php else : ?>
    	<div class="appDownload">Publish your Catalog, Share, Get Orders online <br /><button class="btn btn-primary" onclick="window.open('https://www.qwikbills.com')">Know More</button></div>
    <?php endif; ?>
    <div class="poweredBy">Powered by <br /><a href="https://www.qwikbills.com/"><img src="/images/logo.png" alt="" /></a></div>
  </div>
</div>