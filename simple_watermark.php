<?php
/**
 * @package Simple_Watermark
 * @version 1.6
 */
/*
Plugin Name: Simple Watermark
Plugin URI: http://justinestrada.com
Description: This is simple plugin for image watermarks to overlay content images.
Author: Justin Estrada
Version: 1.6
Author URI: http://justinestrada.com
*/

function simple_watermark_admin_notice() {
	echo "<p class='simple-watermark-admin-notice'>Plugin: Simple Watermark Enabled.</p>";
}

// Now we set that function up to execute when the admin_notices action is called
add_action( 'admin_notices', 'simple_watermark_admin_notice' );


/******************************
Creates Photography Setting Page
*******************************/
function photography_settings_page_init(){
  register_setting('photography_settings-group', 'photo_input_enable_watermark');
  register_setting('photography_settings-group', 'photo_input_watermark_text');
  register_setting('photography_settings-group', 'photo_input_watermark_image_url');
  register_setting('photography_settings-group', 'photo_input_watermark_selection');
  add_settings_section(
    'photo_settings_section',
    'Photography Settings',
    'photo_settings_section_callback',
    'photography_settings'
  );
  add_settings_field(
    'photo_settings_enable_watermark',
    'Enable Watermark',
    'photo_input_enable_watermark_callback',
    'photography_settings',
    'photo_settings_section'
  );
  add_settings_field(
    'photo_settings_watermark_text',
    'Watermark Text',
    'photo_input_watermark_text_callback',
    'photography_settings',
    'photo_settings_section'
  );
  add_settings_field(
    'photo_settings_watermark_image_url',
    'Watermark Image URL',
    'photo_input_watermark_image_url_callback',
    'photography_settings',
    'photo_settings_section'
  );
  add_settings_field(
    'photo_settings_watermark_selection',
    'Watermark Image Selection',
    'photo_input_watermark_selection_callback',
    'photography_settings',
    'photo_settings_section'
  );
}
add_action('admin_init', 'photography_settings_page_init');

function photo_settings_section_callback(){
  $html = '<p>Check to enable digital watermarks. A digital watermark is a kind of marker covertly embedded an image.</p><p>If no Image URL is given the text inputted will be used as the watermark.</p>';
  echo $html;
}
function photo_input_enable_watermark_callback(){
  $option = get_option('photo_input_enable_watermark');
  if ($option){
    $html = '<label for="photo_input_enable_watermark"><span class="dashicons dashicons-format-image"></span></label>&nbsp;<input type="checkbox" id="photo_input_enable_watermark" name="photo_input_enable_watermark" value="1" checked/>';
  } else {
    $html = '<label for="photo_input_enable_watermark"><span class="dashicons dashicons-format-image"></span></label>&nbsp;<input type="checkbox" id="photo_input_enable_watermark" name="photo_input_enable_watermark" value="1" />';
  }
  echo $html;
}
// Create Theme Options Page
function photography_add_theme_page(){
  add_theme_page(
    __('Photography', 'wpsettings'),
    __('Photography', 'wpsettings'),
    'edit_theme_options',
    'photography_settings',
    'photography_options_page'
  );
}

function photography_options_page(){
?>
<div class="wrap">
  <h2>Photography Settings - <?php echo get_current_theme(); ?></h2>
  <form method="post" action="options.php">
    <?php 
    settings_fields('photography_settings-group'); 
    do_settings_sections('photography_settings');
    submit_button();
    ?>
    <script type="text/javascript">
    </script>
  </form>
</div>
<?php
}

add_action('admin_menu', 'photography_add_theme_page');

/* ============================================================
 * Including Content WaterMark
============================================================= */
$enableWatermark = get_option('photo_input_enable_watermark');
function contentWaterMark(){
/* Usable Variables */
$watermarkText = get_option('photo_input_watermark_text');
$watermarkImageURL = get_option('photo_input_watermark_image_url');
?>
	<style type="text/css">
	.watermark {
		position: relative;
	}
	.watermark:before {
	    content: '<?php echo (($watermarkText)?$watermarkText:"watermark"); ?>';
	    position: absolute;
	    display: table;
	    width: 100%;
	    height: 100%;
	    top: 0;
	    right: 0;
	    bottom: 0;
	    left: 0;
	    margin: auto;
	    <?php if ($watermarkText && !$watermarkImageURL):
	    echo 'color: rgba(255,255,255,.15);';
	    echo 'text-shadow: 2px 2px 2px #000;';
	    else: 
		echo 'color: rgba(255,255,255,0);';
	    endif; ?>
	    text-align: center;
	    font-size: 25px;
	    vertical-align: middle;
	    z-index: 2;
	}
	.watermark:after {
	    content: '';
	    position: absolute;
	    display: block;
	    width: 100%;
	    height: 100%;
	    top: 0;
	    right: 0;
	    bottom: 0;
	    left: 0;
	    margin: auto;
		background: url('<?php echo $watermarkImageURL; ?>');
	    background-color: rgba(255,255,255,0);
		background-repeat: no-repeat;
		background-size: contain;
		background-position: 100%;
		z-index: 1;
		opacity: .15;
	}
	</style>
	<script type="text/javascript">
	if("undefined"==typeof jQuery)throw new Error("Simple Watermark JavaScript requires jQuery");
	function watermarkThis(){
		jQuery('.post .watermark-this').wrap('<div class="watermark"></div>');
		jQuery('.watermark-this').wrap('<div class="watermark"></div>');
	}
	jQuery(document).ready(function(){
		watermarkThis();
	});
	</script>
<?php
}
if ($enableWatermark){
	add_action('wp_head', 'contentWaterMark');	
}

?>
