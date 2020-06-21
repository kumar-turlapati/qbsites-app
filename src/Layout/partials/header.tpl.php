<?php
  $items_in_cart = isset($_SESSION['cart']) && count($_SESSION['cart']) > 0 ? count($_SESSION['cart']) : 0;
?>
<div class="gridContainer">
  <div class="headerWrap">
    <div class="logo">
      <a href="/catalog/view/<?php echo $catalog_hash ?>" title="QwikBills">
        <img src="/images/logo.png" alt="" />
      </a>
    </div>
    <div class="catalogName"><?php echo $org_name.' - '. $catalog_name ?></div>
    <div class="rightGroup">
        <ul>
          <li>
            <?php if(!isset($is_cart)): ?>
              <a href="/order">
                <span><img src="/images/ic_cart.svg" alt="Cart" /></span>
                <span class="itemsAdded">
                  <span class="cartCount"><?php echo $items_in_cart ?></span> items
                </span>
              </a>
            <?php else: ?>
              <a href="/catalog/view/<?php echo $_SESSION['catalog_hash'] ?>">
                <span><img src="/images/ic_cart.svg" alt="Cart" /></span>
                <span class="itemsAdded">
                  <span class="cartCount">CONTINUE TO SHOP
                </span>
              </a>
            <?php endif; ?>
          </li>
        </ul>
    </div>
  </div>
</div>

<?php /*
<li>
  <a href="share.html">
    <span><img src="/images/ic_share.svg" alt="Share" /></span>
    <span>Share</span>
  </a>
</li> */ ?>
