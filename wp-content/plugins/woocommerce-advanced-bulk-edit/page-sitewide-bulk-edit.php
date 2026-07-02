
<div class="wcabe-top-bar-container">
    <div class="wcabe-title"><?php esc_html_e( "WooCommerce Advanced Bulk Edit", "woocommerce-advbulkedit" ); ?></div>
</div>

<div class="wrap boxed-layout-wcabe">

  <p>&nbsp;</p>

  <h3><?php esc_html_e( "Site-wide Bulk Edit", "woocommerce-advbulkedit" ); ?></h3>

  <a href="<?php echo admin_url( 'edit.php?post_type=product&page=advanced_bulk_edit' ); ?>"><?php esc_html_e( "< back", "woocommerce-advbulkedit" ); ?></a>

<?php
if (!wcabe_is_current_user_admin()) {
?>
	<p><?php esc_html_e( "Only admins can access this page.", "woocommerce-advbulkedit" ); ?></p>
<?php
	return;
}
?>

  <div class="wcabe-general-settings-section">
    <h3><?php esc_html_e( "Site-wide Bulk Edit Tools", "woocommerce-advbulkedit" ); ?></h3>
    <p><?php esc_html_e( "Welcome to the Site-wide Bulk Edit add-on. This tool allows you to perform bulk operations across your entire WooCommerce store.", "woocommerce-advbulkedit" ); ?></p>
    
    <!-- Add your Site-wide Bulk Edit content here -->
    <div class="wcabe-sitewide-tools">
      <div class="wcabe-tool-card">
        <h4><?php esc_html_e( "Global Product Update", "woocommerce-advbulkedit" ); ?></h4>
        <p><?php esc_html_e( "Update product attributes across your entire store with a single operation.", "woocommerce-advbulkedit" ); ?></p>
        <button class="button-primary-wcabe" disabled><?php esc_html_e( "Coming Soon", "woocommerce-advbulkedit" ); ?></button>
      </div>
      
      <div class="wcabe-tool-card">
        <h4><?php esc_html_e( "Bulk Category Management", "woocommerce-advbulkedit" ); ?></h4>
        <p><?php esc_html_e( "Reorganize your product categories and assign products in bulk.", "woocommerce-advbulkedit" ); ?></p>
        <button class="button-primary-wcabe" disabled><?php esc_html_e( "Coming Soon", "woocommerce-advbulkedit" ); ?></button>
      </div>
      
      <div class="wcabe-tool-card">
        <h4><?php esc_html_e( "Price Manager", "woocommerce-advbulkedit" ); ?></h4>
        <p><?php esc_html_e( "Apply percentage or fixed amount changes to prices across multiple products.", "woocommerce-advbulkedit" ); ?></p>
        <button class="button-primary-wcabe" disabled><?php esc_html_e( "Coming Soon", "woocommerce-advbulkedit" ); ?></button>
      </div>
    </div>
  </div>

</div>

<style>
  .wcabe-sitewide-tools {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
  }
  
  .wcabe-tool-card {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    width: calc(33.333% - 14px);
    min-width: 250px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
  }
  
  .wcabe-tool-card h4 {
    margin-top: 0;
    color: #23282d;
  }
  
  .wcabe-tool-card p {
    min-height: 60px;
  }
  
  @media (max-width: 782px) {
    .wcabe-tool-card {
      width: 100%;
    }
  }
</style>
