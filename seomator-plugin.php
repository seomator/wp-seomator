<?php
	/*
		Plugin Name: Seomator White label SEO audit tool
		Plugin URI: https://seomator.com
		Description: Embed our audit tool into your website and provide free white-labeled SEO reports with our awesome charts and conclusion. Your potential customers will be overwhelmed by founded issues represented in detailed conclusion. Sales have never been so easy. Before first contact, you already know customer needs and have professional SEO audit in your hand.
		Version: 2.0
		Author: Seomator.com
		Author URI: https://seomator.com
	*/
	
	class SeomatorPlugin {

        private $plugin_file_url;

        public function __construct() {
			add_action('admin_menu', array($this, 'add_seomator_plugin_options_page'));
			add_shortcode('seomator_plugin_shortcode', array($this, 'seomator_plugin_shortcode'));

            $this->plugin_file_url  = plugin_dir_url(__FILE__);
		}

        static function seomator_plugin_install() {
            update_option('seomator_plugin_options', array(
                    'mode' => 'standard',
                    'reportMode' => 'overlay',
            ));
        }

        public function seomator_plugin_shortcode($atts) {
            $plugin_options = get_option('seomator_plugin_options');
            if ($plugin_options != false && is_array($plugin_options) && !empty($plugin_options)) {

                /* External script without duplicates and only at pages where shortcode is used */
                wp_register_script('seomator-plugin-script', 'https://seomator.com/assets/audit-tools/js/sdk.js');
                wp_enqueue_script('seomator-plugin-script', false, array(), false, true);

                extract($plugin_options);
                $partnerCode = esc_attr(html_entity_decode($partnerCode));

                ob_start();
                ?>
                <script>
                    window.seomtr = {
                        seomtrDomain: 'https://seomator.com',
                        partnerCode: '<?php _e($partnerCode)?>',
                        mode: '<?php _e($plugin_options['mode'])?>',
                        reportMode: '<?php _e($plugin_options['reportMode'])?>'
                    };
                </script>
                <div id="seomtr-container"></div>

                <?php
                $content = ob_get_clean();
                return $content;
            }
        }

		public function add_seomator_plugin_options_page() {
			add_options_page('Seomator Plugin options', 'Seomator', 'manage_options', 'seomator-plugin', array($this, 'seomator_plugin_admin_page'));
		}
		
		public function seomator_plugin_admin_page() {

            wp_register_style('seomator-plugin.css', $this->plugin_file_url . 'assets/css/seomator-plugin.css');
            wp_enqueue_style('seomator-plugin.css');

            ?>
            <div class="wrap">
                <h2><img src="<?php _e($this->plugin_file_url) ?>assets/img/32_logo_medium.png" alt="" style="vertical-align: middle; padding-right: 10px; ">Seomator Plugin</h2>
            <?php
            if(isset($_POST['seomator_plugin_options']) && is_array($_POST['seomator_plugin_options'])) {
				$post_data = array();
                /* We only have one text option at the moment */
				foreach ($_POST['seomator_plugin_options'] as $key => $val) {
                    if (is_string($val)) {
                        $post_data[$key] = sanitize_text_field($val);
                    }
                }
				update_option('seomator_plugin_options', $post_data);
				?>
                <div class="updated"><p><strong>Seomator Plugin settings have been saved.</strong></p></div>
                <?php
			}
			
			$plugin_options = get_option('seomator_plugin_options');
			$plugin_options = $plugin_options === false ? array('partnerCode' => '') : $plugin_options;
			?>
				<form action="" method="POST" class="seomator-plugin-form">

                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="partnerCode">Partner Code</label>
                                </th>
                                <td>
                                    <input type="text" id="partnerCode" name="seomator_plugin_options[partnerCode]" class="regular-text code" value="<?php _e($plugin_options['partnerCode'])?>" required>
                                    <p id="home-description" class="description">You can find your Partner Code at <a href="https://seomator.com/private/prosettings" target="_blank">Embedded Audit and PDF Export Settings page</a></p>
                                </td>
                            </tr>
                            <tr>
                                <th>Initial Form</th>
                                <td>
                                    <p class="option-description">An initial form is a first form your website's visitors can see when they land on Embedded Audit page. Select a form.</p>
                                    <div class="init-form-type">
                                        <div class="type-wrap">
                                            <a class="type-thumb" href="<?php _e($this->plugin_file_url) ?>assets/img/form-standard.png" target="_blank">
                                                <img src="<?php _e($this->plugin_file_url) ?>assets/img/form-standard.png">
                                            </a>
                                            <input id="standard" type="radio" name="seomator_plugin_options[mode]" value="standard" <?php _e($plugin_options['mode'] == 'standard' ? 'checked' : ''); ?>>
                                            <label for="standard" class="radio-inline">
                                                Standard
                                            </label>
                                        </div>
                                        <div class="type-wrap">
                                            <a class="type-thumb" href="<?php _e($this->plugin_file_url) ?>assets/img/form-slim.png" target="_blank">
                                                <img src="<?php _e($this->plugin_file_url) ?>assets/img/form-slim.png">
                                            </a>
                                            <input id="slim" type="radio" name="seomator_plugin_options[mode]" value="slim" <?php _e($plugin_options['mode'] == 'slim' ? 'checked="checked"' : ''); ?>>
                                            <label for="slim" class="radio-inline">
                                                Slim
                                            </label>
                                        </div>
                                        <div class="type-wrap">
                                            <a class="type-thumb" href="<?php _e($this->plugin_file_url) ?>assets/img/form-small.png" target="_blank">
                                                <img src="<?php _e($this->plugin_file_url) ?>assets/img/form-small.png">
                                            </a>
                                            <input id="small" type="radio" name="seomator_plugin_options[mode]" value="small" <?php _e($plugin_options['mode'] == 'small' ? 'checked="checked"' : ''); ?>>
                                            <label for="small" class="radio-inline">
                                                Small
                                            </label>
                                        </div>
                                        <div class="type-wrap">
                                            <a class="type-thumb" href="<?php _e($this->plugin_file_url) ?>assets/img/form-takeover.png" target="_blank">
                                                <img src="<?php _e($this->plugin_file_url) ?>assets/img/form-takeover.png">
                                            </a>
                                            <input id="takeover" type="radio" name="seomator_plugin_options[mode]" value="takeover" <?php _e($plugin_options['mode'] == 'takeover' ? 'checked="checked"' : ''); ?>>
                                            <label for="takeover" class="radio-inline">
                                                Takeover
                                            </label>
                                        </div>
                                        <div class="type-wrap">
                                            <a class="type-thumb" href="<?php _e($this->plugin_file_url) ?>assets/img/form-bar.png" target="_blank">
                                                <img src="<?php _e($this->plugin_file_url) ?>assets/img/form-bar.png">
                                            </a>
                                            <input id="bar" type="radio" name="seomator_plugin_options[mode]" value="bar" <?php _e($plugin_options['mode'] == 'bar' ? 'checked="checked"' : ''); ?>>
                                            <label for="bar" class="radio-inline">
                                                Bar
                                            </label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <th>Report Design</th>
                                <td>
                                    <p class="option-description">Select an option for a client report design.</p>
                                    <div class="report-design">
                                        <div class="type-wrap">
                                            <a class="type-thumb" href="<?php _e($this->plugin_file_url) ?>assets/img/rpt-overlay.png" target="_blank">
                                                <img src="<?php _e($this->plugin_file_url) ?>assets/img/rpt-overlay.png">
                                            </a>
                                            <input id="overlay" type="radio" name="seomator_plugin_options[reportMode]" value="overlay" <?php _e($plugin_options['reportMode'] == 'overlay' ? 'checked="checked"' : ''); ?>>
                                            <label for="overlay" class="radio-inline">
                                                Overlay
                                            </label>
                                        </div>
                                        <div class="type-wrap">
                                            <a class="type-thumb" href="<?php _e($this->plugin_file_url) ?>assets/img/rpt-blackout.png" target="_blank">
                                                <img src="<?php _e($this->plugin_file_url) ?>assets/img/rpt-blackout.png">
                                            </a>
                                            <input id="blackout" type="radio" name="seomator_plugin_options[reportMode]" value="blackout" <?php _e($plugin_options['reportMode'] == 'blackout' ? 'checked="checked"' : ''); ?>>
                                            <label for="blackout" class="radio-inline">
                                                Blackout
                                            </label>
                                        </div>
                                        <div class="type-wrap">
                                            <a class="type-thumb" href="<?php _e($this->plugin_file_url) ?>assets/img/rpt-embed.png" target="_blank">
                                                <img src="<?php _e($this->plugin_file_url) ?>assets/img/rpt-embed.png">
                                            </a>
                                            <input id="embed" type="radio" name="seomator_plugin_options[reportMode]" value="embed" <?php _e($plugin_options['reportMode'] == 'embed' ? 'checked="checked"' : ''); ?>>
                                            <label for="embed" class="radio-inline">
                                                Embed
                                            </label>
                                        </div>
                                    </div>

                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <p class="submit">
                                        <input type="submit" class="button button-primary" value="Save">
                                    </p>
                                </td>
                            </tr>
                            <?php if ($plugin_options['partnerCode']): ?>
                            <tr>
                                <th>
                                    Shortcode
                                </th>
                                <td>
                                    <p class="">
                                        Use this shortcode to embed audit form into the page you want. You can pass <i>width</i> and <i>height</i> parameters for each form.
                                    </p>
                                    <p class="submit">
                                        <input class="regular-text code" id="seomatorGeneratedCode" readonly value="[seomator_plugin_shortcode]">
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

    register_activation_hook(__FILE__, array('SeomatorPlugin', 'seomator_plugin_install'));

	new SeomatorPlugin();
?>