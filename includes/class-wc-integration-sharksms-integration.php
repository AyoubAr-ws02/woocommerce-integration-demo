<?php

/**
 * Integration Demo Integration.
 *
 * @package  WC_Integration_Demo_Integration
 * @category Integration
 * @author   WooThemes
 */

if (!class_exists('WC_Integration_Demo_Integration')) :

	class WC_Integration_Demo_Integration extends WC_Integration
	{

		/**
		 * Init and hook in the integration.
		 */
		public function __construct()
		{
			global $woocommerce;

			$this->id                 = 'integration-sharksms';
			$this->method_title       = __('Integration Demo', 'woocommerce-integration-sharksms');
			$this->method_description = __('An integratino demo to show you how easy it is to extend WooCommerce.', 'woocommerce-integration-sharksms');

			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();

			// Define user set variables.
			$this->api_key          = $this->get_option('api_key');
			$this->group_id          = $this->get_option('group_id');
			$this->code_country          = $this->get_option('code_country');
			$this->debug            = $this->get_option('debug');

			// Actions.
			add_action('woocommerce_update_options_integration_' .  $this->id, array($this, 'process_admin_options'));

			// Filters.
			add_filter('woocommerce_settings_api_sanitized_fields_' . $this->id, array($this, 'sanitize_settings'));
		}


		/**
		 * Initialize integration settings form fields.
		 *
		 * @return void
		 */
		public function init_form_fields()
		{
			$this->form_fields = array(
				'api_key' => array(
					'title'             => __('API Key', 'woocommerce-integration-sharksms'),
					'type'              => 'text',
					'description'       => __('Message example: to get api key from admin of sharksms'),
					'desc_tip'          => true,
					'default'           => ''
				),
				'group_id' => array(
					'title'             => __('Group ID', 'woocommerce-integration-sharksms'),
					'type'              => 'text',
					'description'       => __('Message example: please set the group you want to save the contatcs info into'),
					'desc_tip'          => true,
					'default'           => ''
				),
				'code_country' => array(
					'title'             => __('Code Country', 'woocommerce-integration-sharksms'),
					'type'              => 'text',
					'description'       => __('Message example: please set the country alias phone code'),
					'desc_tip'          => true,
					'default'           => '+212'
				),
				'debug' => array(
					'title'             => __('Debug Log', 'woocommerce-integration-sharksms'),
					'type'              => 'checkbox',
					'label'             => __('Enable logging', 'woocommerce-integration-sharksms'),
					'default'           => 'no',
					'description'       => __('Log events such as API requests', 'woocommerce-integration-sharksms'),
				),
				'visit_sharksmsm' => array(
					'title'             => __('Visit Sharksmsm.com', 'woocommerce-integration-sharksms'),
					'type'              => 'button',
					'custom_attributes' => array(
						'onclick' => "location.href='sharksms.com'",
					),
					'description'       => __('Find your API key from your shark smsm account.', 'woocommerce-integration-sharksms'),
					'desc_tip'          => true,
				)
			);
		}

		/**
		 * Generate Button HTML.
		 */
		public function generate_button_html($key, $data){
			$field    = $this->plugin_id . $this->id . '_' . $key;
			$defaults = array(
				'class'             => 'button-primary',
				'css'               => '',
				'custom_attributes' => array(),
				'desc_tip'          => false,
				'description'       => '',
				'title'             => '',
			);

			$data = wp_parse_args($data, $defaults);

			ob_start();
?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr($field); ?>"><?php echo wp_kses_post($data['title']); ?></label>
					<?php echo $this->get_tooltip_html($data); ?>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
						<button class="<?php echo esc_attr($data['class']); ?>" type="button" name="<?php echo esc_attr($field); ?>" id="<?php echo esc_attr($field); ?>" style="<?php echo esc_attr($data['css']); ?>" <?php echo $this->get_custom_attribute_html($data); ?>><?php echo wp_kses_post($data['title']); ?></button>
						<?php echo $this->get_description_html($data); ?>
					</fieldset>
				</td>
			</tr>
<?php
			return ob_get_clean();
		}


		/**
		 * Santize our settings
		 * @see process_admin_options()
		 */
		public function sanitize_settings($settings)
		{
			// We're just going to make the api key all upper case characters since that's how our imaginary API works
			if (
				isset($settings) &&
				isset($settings['api_key'])
			) {
				$settings['api_key'] = strtoupper($settings['api_key']);
			}
			return $settings;
		}


		/**
		 * Validate the API key
		 * @see validate_settings_fields()
		 */
		public function validate_api_key_field($key)
		{
			// get the posted value
			$value = $_POST[$this->plugin_id . $this->id . '_' . $key];

			// check if the API key is longer than 20 characters. Our imaginary API doesn't create keys that large so something must be wrong. Throw an error which will prevent the user from saving.
			if (
				isset($value) &&
				20 < strlen($value)
			) {
				$this->errors[] = $key;
			}
			return $value;
		}


		/**
		 * Display errors by overriding the display_errors() method
		 * @see display_errors()
		 */
		public function display_errors()
		{

			// loop through each error and display it
			foreach ($this->errors as $key => $value) {
			?>
				<div class="error">
					<p><?php _e('Looks like you made a mistake with the ' . $value . ' field. Make sure it isn&apos;t longer than 20 characters', 'woocommerce-integration-sharksms'); ?></p>
				</div>
<?php
			}
		}
	}

endif;
