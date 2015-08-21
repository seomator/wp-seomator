<?php
	/*
		Plugin Name: Seomator Lead Generation and White-label
		Plugin URI: https://seomator.com
		Description: Embed our audit tool into your website and provide free white-labeled SEO reports with our awesome charts and conclusion. Your potential customers will be overwhelmed by founded issues represented in detailed conclusion. Sales have never been so easy. Before first contact, you already know customer needs and have professional SEO audit in your hand.
		Version: 0.1
		Author: Seomator.com
		Author URI: https://seomator.com
	*/
	
	class SeomatorLeadgen {

        private $plugin_file_url;

        public function __construct() {
			add_action('admin_menu', array($this, 'add_shortcode_generator_page'));
			add_shortcode('seomator_shortcode', array($this, 'seomator_shortcode'));

            $this->plugin_file_url  = str_replace('\\', '/', trailingslashit(plugins_url('', __FILE__)));
		}


        public function seomator_shortcode($atts) {
            $saved_seomator_options = get_option('seomator_options');
            if ($saved_seomator_options != false && is_array($saved_seomator_options) && !empty($saved_seomator_options)) {

                /* External script without duplicates and only at pages where shortcode is used */
                wp_register_script('seomator-lead-script', 'https://seomator.com/assets/js/lead.js');
                wp_enqueue_script('seomator-lead-script', false, array(), false, true);

                extract($saved_seomator_options);
                $partnerCode = esc_attr(html_entity_decode($partnerCode));
                extract(shortcode_atts(array(
                    'width' => 900,
                    'height' => 400,
                ), $atts ));

                ob_start();
                ?>
                <script>
                    window.leadPartnerCode = '<?php _e($partnerCode)?>';
                    window.leadWidth = <?php _e($width)?>;
                    window.leadHeight = <?php _e($height)?>;
                </script>
                <div class="widgetContainer"></div>

                <?php
                $content = ob_get_clean();
                return $content;
            }
        }

		public function add_shortcode_generator_page() {
			add_options_page('Seomator Shortcode Generator', 'Seomator Shortcode Generator', 'manage_options', 'seomator-shortcode-generator', array($this, 'seomator_shortcode_admin_page'));
		}
		
		public function seomator_shortcode_admin_page() {

            ?>
            <div class="wrap">
                <h2><img src="<?php _e($this->plugin_file_url) ?>img/32_logo_medium.png" alt="" style="vertical-align: middle; padding-right: 10px; ">Seomator Shortcode Generator</h2>
            <?php
            if(isset($_POST['seomator_options']) && is_array($_POST['seomator_options'])) {
				$post_data = array();
                /* We only have one text option at the moment */
				foreach ($_POST['seomator_options'] as $key => $val) {
                    if (is_string($val)) {
                        $post_data[$key] = sanitize_text_field($val);
                    }
                }
				update_option('seomator_options', $post_data);
				?><div class="updated"><p><strong>Partner code saved.</strong></p></div><?php
			}
			
			$saved_seomator_options = get_option('seomator_options');
			$saved_seomator_options = $saved_seomator_options === false ? array('partnerCode' => '') : $saved_seomator_options;
			?>
				<form action="" method="POST">

                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="partnerCode">Partner Code</label>
                                </th>
                                <td>
                                    <input type="text" id="partnerCode" name="seomator_options[partnerCode]" class="regular-text code" value="<?php _e($saved_seomator_options['partnerCode'])?>" required>
                                    <p id="home-description" class="description">You can find your Partner Code at <a href="https://seomator.com/private/prosettings" target="_blank">Embedded Audit and PDF Export Settings page</a></p>
                                    <p class="submit">
                                        <input type="submit" class="button button-primary" value="Save">
                                    </p>
                                </td>
                            </tr>
                            <?php if ($saved_seomator_options['partnerCode']): ?>
                            <tr>
                                <th>
                                    Shortcode
                                </th>
                                <td>
                                    <p class="">
                                        Use this shortcode to embed audit form into the page you want. You can pass <i>width</i> and <i>height</i> parameters for each form.
                                    </p>
                                    <p class="submit">
                                        <input class="regular-text code" id="seomatorGeneratedCode" readonly value="[seomator_shortcode width=900 height=400]">
                                    </p>
                                    <p>
                                        If you want to customize input forms, audits look and branding, visit our <a href="https://seomator.com/private/prosettings" target="_blank">settings page</a>.
                                    </p>
                                </td>
                            </tr>
                            <?php endif;?>
                        </table>
				</form>
				</div>
				<script type="text/javascript">
					jQuery(function ($)
					{
						$("#seomatorGeneratedCode").click(function ()
						{
							$(this).select();
						});
					});
				</script>
			<?php
		}
	}
	
	new SeomatorLeadgen();
?>