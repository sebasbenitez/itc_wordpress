
<div class="wcabe-top-bar-container">
    <div class="wcabe-title"><?php esc_html_e( "WooCommerce Advanced Bulk Edit", "woocommerce-advbulkedit" ); ?></div>
</div>

<div class="wrap boxed-layout-wcabe">

  <p>&nbsp;</p>

  <h3><?php esc_html_e( "General Settings", "woocommerce-advbulkedit" ); ?></h3>

  <a href="<?php echo admin_url( 'edit.php?post_type=product&page=advanced_bulk_edit' ); ?>"><?php esc_html_e( "< back", "woocommerce-advbulkedit" ); ?></a>

<?php
if (!wcabe_is_current_user_admin()) {
?>
	<p><?php esc_html_e( "Only admins can access this page.", "woocommerce-advbulkedit" ); ?></p>
<?php
	return;
}

$settings = get_option('w3exabe_settings');
$wcabe_license_key = $settings['license_key'] ?? '';

$connection_log = wcabe_connection_log_read();
?>


  <div class="wcabe-general-settings-section">
    <form method="post" action="<?php echo admin_url( 'edit.php' ); ?>">
      <h3><?php esc_html_e( "License Key (Purchase Code)", "woocommerce-advbulkedit" ); ?></h3>
      <p>
        <input type="text" id="wcabe_license_key" name="wcabe_license_key" value="<?php echo $wcabe_license_key; ?>" class="regular-text wcabe-license-key-input" />
        <input type="submit" name="wcabe-submit-settings" id="wcabe-submit-settings" class="button-primary-wcabe" value="Save">
      </p>
      <!--<p>Status: </p>-->
      <p>
          <?php esc_html_e( "The license key will allow you to use plugin auto-updates. If you don't have one, please purchase it ", "woocommerce-advbulkedit" ); ?>
          <a href="https://codecanyon.net/item/woocommerce-advanced-bulk-edit/8011417" target="_blank"><?php esc_html_e( "here", "woocommerce-advbulkedit" ); ?></a>.
      </p>
      <p>
          <?php esc_html_e( "You can find the purchase code in your CodeCanyon account in Downloads section. Click on the Download button next to WooCommerce Advanced Bulk Edit and from the drop-down menu select the text version of the purchase code doc. More detailed info ", "woocommerce-advbulkedit" ); ?>
          <a href="https://wpmelon.com/r/wcabe-purchase-code-info" target="_blank"><?php esc_html_e( "here.", "woocommerce-advbulkedit" ); ?></a>.
      </p>
    </form>
  </div>

  <div class="wcabe-general-settings-section">
    <form method="post" action="<?php echo admin_url( 'edit.php' ); ?>">
      <h3><?php esc_html_e( "Check Connection With Updates Server", "woocommerce-advbulkedit" ); ?></h3>
      <p>
        <?php esc_html_e( "If you have issues getting the plugin updates, click the button below to check the connection with the updates server and get some error info, which might help the support team to ", "woocommerce-advbulkedit" ); ?>
        <a href="https://wpmelon.com/r/support" target="_blank"><?php esc_html_e( "resolve the issue.", "woocommerce-advbulkedit" ); ?></a>
      </p>
      <p>
        <input type="submit" name="wcabe-submit-settings-connection-test" id="wcabe-submit-settings-connection-test" class="button-primary-wcabe" value="Check Connection">
      </p>
      <p>
        <textarea readonly class="check-connection-terminal"><?php echo $connection_log; ?></textarea>
      </p>
      <p>
        <input type="submit" name="wcabe-submit-settings-connection-clear-log" id="wcabe-submit-settings-connection-test" class="button-wcabe" value="Clear Log">
      </p>
    </form>
  </div>

  <div class="wcabe-general-settings-section">
      <h3><?php esc_html_e( "Recommended Plugins", "woocommerce-advbulkedit" ); ?></h3>
      <p><?php esc_html_e( "If you love using WooCommerce Advanced Bulk Edit, you might also find our other plugins incredibly useful!", "woocommerce-advbulkedit" ); ?></p>
      <div class="box-container">
          <div class="flex-container">
              <div class="image-column">
                  <img src="<?php echo esc_attr(WCABE_PLUGIN_URL) ?>images/fomopop-thumb-80x80.png" alt="FomoPop Marketing Thumbnail">
              </div>
              <div class="content-column">
                  <h4 class="content-title"><?php esc_html_e( "FomoPop Marketing", "woocommerce-advbulkedit" ); ?></h4>
                  <p class="content-description"><?php esc_html_e( "FomoPop Marketing is a social proof plugin to display nicely designed pop-up notifications on your WooCommerce store. Showing that other people are buying gives site visitors more confidence. This will motivate visitors to make a purchase and result in increasing sales instantly.", "woocommerce-advbulkedit" ); ?></p>
              </div>
              <div class="button-column">
                  <a href="https://wpmelon.com/r/fomopop-marketing" target="_blank">
                      <button class="button-wcabe button-highlight-wcabe"><?php esc_html_e( "Get FomoPop Marketing Now", "woocommerce-advbulkedit" ); ?></button>
                  </a>
              </div>
          </div>
      </div>
  </div>

</div>
