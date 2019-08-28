<?php
/**
 * Setup Freshjet
 *
 * @package Freshjet
 */

namespace Freshjet;

use Mailjet\Resources;
use Mailjet\Client;

/**
 * Setup Freshjet
 */
class Setup {
	/**
	 * Freshjet option's meta key
	 *
	 * @var string $options_key
	 */
	private $options_key = 'freshjet_options';

	/**
	 * Setup actions & filters
	 */
	public function __construct() {
		$this->setup_mailjet();

		add_action( 'admin_head', [ $this, 'inline_styles' ] );
		add_action( 'admin_menu', [ $this, 'add_custom_pages' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'settings_page_styles' ] );
	}

	/**
	 * Setup Mailjet
	 */
	public function setup_mailjet() {
		$opts = get_option( $this->options_key );

		$public_key = isset( $opts['public_key'] ) ? $opts['public_key'] : false;
		$secret_key = isset( $opts['secret_key'] ) ? $opts['secret_key'] : false;

		$sender_name  = isset( $opts['sender_name'] ) ? $opts['sender_name'] : false;
		$sender_email = isset( $opts['sender_email'] ) ? $opts['sender_email'] : false;

		$sender_name  = apply_filters( 'freshjet/sender_name', $sender_name );
		$sender_email = apply_filters( 'freshjet/sender_email', $sender_email );

		define( 'FRESHJET_PUBLIC_KEY', $public_key );
		define( 'FRESHJET_SECRET_KEY', $secret_key );
		define( 'FRESHJET_SENDER_NAME', $sender_name );
		define( 'FRESHJET_SENDER_EMAIL', $sender_email );
	}

	/**
	 * Add inline styles
	 */
	public function inline_styles() {?>
		<style>
		#toplevel_page_freshjet .wp-menu-image img {
			max-width: 25px;
			padding-top: 5px;
		}
		</style>
		<?php
	}

	/**
	 * Add custom pages
	 */
	public function add_custom_pages() {
		add_menu_page(
			'Freshjet',
			'Freshjet',
			'manage_options',
			'freshjet',
			[ $this, 'render_freshjet_page' ],
			FRESHJET_PLUGIN_URL . '/assets/images/mailjet-logo-small.svg'
		);
	}

	/**
	 * Render freshjet page output
	 */
	public function render_freshjet_page() {
		?>
		<div class="wrap settingstuff freshjet-keys">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<div class="neatbox has-subboxes has-bigger-heading">
					<?php
					settings_fields( 'freshjet-keys-group' );
					do_settings_sections( 'freshjet-general-page' );
					submit_button();
					?>
					<div class="response"></div>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Register Freshjet settings fields.
	 */
	public function register_settings() {
		$passport_note = '<p style="margin-top: 3px;"><small style="font-weight: 400;">You will need <strong>Transactional</strong> template in <a href="https://app.mailjet.com/templates/transactional">Mailjet\'s Passport</a></small>.</p>';

		// Setup setting groups.
		register_setting( 'freshjet-keys-group', $this->options_key );

		// Setup setting sections.
		add_settings_section( 'freshjet-keys-section', __( 'Mailjet Keys', 'freshjet' ), '', 'freshjet-general-page' );
		add_settings_section( 'freshjet-sender-section', __( 'Sender Identities', 'freshjet' ), '', 'freshjet-general-page' );
		add_settings_section( 'freshjet-template-section', __( "Mailjet's Passport Template", 'freshjet' ), '', 'freshjet-general-page' );

		// Setup setting fields.
		add_settings_field( 'freshjet-public-key-field', __( 'API Public Key (SMTP username)', 'freshjet' ), [ $this, 'render_public_key_field' ], 'freshjet-general-page', 'freshjet-keys-section' );
		add_settings_field( 'freshjet-secret-key-field', __( 'API Secret Key (SMTP password)', 'freshjet' ), [ $this, 'render_secret_key_field' ], 'freshjet-general-page', 'freshjet-keys-section' );
		add_settings_field( 'freshjet-sender-name-field', __( 'Sender Name', 'freshjet' ), [ $this, 'render_sender_name_field' ], 'freshjet-general-page', 'freshjet-sender-section' );
		add_settings_field( 'freshjet-sender-email-field', __( 'Sender Email', 'freshjet' ), [ $this, 'render_sender_email_field' ], 'freshjet-general-page', 'freshjet-sender-section' );
		add_settings_field( 'freshjet-template-selector-field', __( 'Default Transactional Template', 'freshjet' ) . $passport_note, [ $this, 'render_template_selector_field' ], 'freshjet-general-page', 'freshjet-template-section' );
		add_settings_field( 'freshjet-enable-individual-template-field', __( 'Individual Transactional Template', 'freshjet' ), [ $this, 'render_enable_individual_template_field' ], 'freshjet-general-page', 'freshjet-template-section' );
	}

	/**
	 * Render public key field
	 */
	public function render_public_key_field() {
		$opts       = get_option( $this->options_key );
		$public_key = isset( $opts['public_key'] ) ? $opts['public_key'] : '';

		echo '
			<p>
				<label>
					<input type="text" name="' . esc_attr( $this->options_key ) . '[public_key]" value="' . esc_attr( $public_key ) . '" class="regular-text" />
				</label>
			</p>
		';
	}

	/**
	 * Render secret key field
	 */
	public function render_secret_key_field() {
		$opts       = get_option( $this->options_key );
		$secret_key = isset( $opts['secret_key'] ) ? $opts['secret_key'] : '';

		echo '
			<p>
				<label>
					<input type="password" name="' . esc_attr( $this->options_key ) . '[secret_key]" value="' . esc_attr( $secret_key ) . '" class="regular-text" />
				</label>
			</p>
		';
	}

	/**
	 * Render sender name field
	 */
	public function render_sender_name_field() {
		$opts        = get_option( $this->options_key );
		$sender_name = isset( $opts['sender_name'] ) ? $opts['sender_name'] : '';

		echo '
			<p>
				<label>
					<input type="text" name="' . esc_attr( $this->options_key ) . '[sender_name]" value="' . esc_attr( $sender_name ) . '" class="regular-text" />
				</label>
			</p>
		';
	}

	/**
	 * Render sender email field
	 */
	public function render_sender_email_field() {
		$opts         = get_option( $this->options_key );
		$sender_email = isset( $opts['sender_email'] ) ? $opts['sender_email'] : '';

		echo '
			<p>
				<label>
					<input type="email" name="' . esc_attr( $this->options_key ) . '[sender_email]" value="' . esc_attr( $sender_email ) . '" class="regular-text" />
				</label>
			</p>
		';
	}

	/**
	 * Render template selector field
	 */
	public function render_template_selector_field() {
		$opts        = get_option( $this->options_key );
		$template_id = isset( $opts['template_id'] ) ? absint( $opts['template_id'] ) : 0;
		$templates   = $this->get_mailjet_templates();
		?>

		<select name="<?php echo esc_attr( $this->options_key ); ?>[template_id]" class="regular-text">
			<?php if ( ! empty( $templates ) ) : ?>
				<option value=""><?php esc_html_e( 'Blank', 'freshjet' ); ?></option>
				<?php foreach ( $templates as $template ) : ?>
					<option value="<?php echo esc_attr( $template['id'] ); ?>" <?php selected( $template_id, $template['id'] ); ?>>
						<?php echo esc_html( $template['name'] ); ?>
						<?php if ( ! empty( $template['description'] ) ) : ?>
							&mdash;
							<?php echo esc_html( $template['description'] ); ?>
						<?php endif; ?>
					</option>
				<?php endforeach; ?>
			<?php else : ?>
				<option value=""><?php esc_html_e( 'Not available', 'freshjet' ); ?></option>
			<?php endif; ?>
		</select>

		<p>
			<small>
				<?php if ( ! empty( $templates ) ) : ?>
					This is default template for general <code>wp_mail()</code> usage where only <code>subject</code> & <code>body</code> will be used as variables.
					<br>However, this template can be overriden by individual template when sending an email.
				<?php else : ?>
					You don't have any transactional template in Mailjet's Passport.
				<?php endif; ?>
			</small>
		</p>

		<?php
	}

	/**
	 * Render individual template field
	 */
	public function render_enable_individual_template_field() {
		$opts       = get_option( $this->options_key );
		$is_enabled = isset( $opts['enable_individual_template'] ) ? absint( $opts['enable_individual_template'] ) : 0;
		?>

		<label>
			<input 
				type="checkbox" value="1"
				name="<?php echo esc_attr( $this->options_key ); ?>[enable_individual_template]" 
				<?php checked( $is_enabled, 1 ); ?>
			>
			Enable
		</label>

		<p>
			<small>
				By enabling this option, you will be able to use custom template id and it's variables for each email. <br>
				You can use it by providing these optional global variables:
			</small>
			<ul>
				<li>
					<small><code>$GLOBALS['freshjet_template_id'];</code> with the value is integer</small>
				</li>
				<li>
					<small><code>$GLOBALS['freshjet_template_vars'];</code> with the value is array</small>
				</li>
			</ul>
		</p>

		<?php
	}

	/**
	 * Setting page styles
	 */
	public function settings_page_styles() {
		$current_screen = get_current_screen();

		if ( 'toplevel_page_freshjet' !== $current_screen->id ) {
			return;
		}

		wp_enqueue_style( 'settings-page', FRESHJET_PLUGIN_URL . '/assets/css/settings-page.css', [], '0.1.0' );
	}

	/**
	 * Get mailjet templates
	 *
	 * @see https://dev.mailjet.com/reference/email/templates/
	 *
	 * @return array
	 */
	public function get_mailjet_templates() {
		$is_master_key  = $this->check_mailjet_key();
		$tpl_owner_type = $is_master_key ? 'user' : 'apikey';
		$api_url        = 'https://api.mailjet.com/v3/REST/template?EditMode=tool\&Limit=100\&OwnerType=' . $tpl_owner_type;

		/**
		 * Fetching Mailjet's Passport Template
		 *
		 * @link https://dev.mailjet.com/guides/#use-the-template-in-send-api
		 * @link https://dev.mailjet.com/reference/email/templates/#v3_get_template
		 *
		 * ! This doesn't work.
		 */

		/**
		$mailjet = new Client( FRESHJET_PUBLIC_KEY, FRESHJET_SECRET_KEY, true, [ 'version' => 'v3.1' ] );

		$filters   = [
			'EditMode'  => 'tool',
			'Limit'     => '100', // Max value 1000.
			'OwnerType' => $tpl_owner_type,
			'Purposes'  => 'transactional', // This even doesn't work in the curl version.
		];
		$templates = $mailjet->get( Resources::$Template, [ 'filters' => $filters ] );
		*/

		$response  = wp_remote_get(
			$api_url,
			[
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( FRESHJET_PUBLIC_KEY . ':' . FRESHJET_SECRET_KEY ),
				),
			]
		);
		$templates = [];

		if ( is_wp_error( $response ) ) {
			return $templates;
		}

		$json_list  = $response['body'];
		$array_list = json_decode( $json_list, true );

		if ( ! isset( $array_list['Count'] ) || $array_list['Count'] < 1 ) {
			return $templates;
		}

		foreach ( $array_list['Data'] as $template ) {
			if ( in_array( 'transactional', $template['Purposes'], true ) ) {
				array_push(
					$templates,
					[
						'id'          => $template['ID'],
						'name'        => $template['Name'],
						'description' => $template['Description'],
					]
				);
			}
		}

		return $templates;
	}

	/**
	 * Check mailjet key whether it's master key or not.
	 */
	public function check_mailjet_key() {
		$api_url   = 'https://api.mailjet.com/v3/REST/apikey/' . FRESHJET_PUBLIC_KEY;
		$is_master = false;

		$response = wp_remote_get(
			$api_url,
			[
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( FRESHJET_PUBLIC_KEY . ':' . FRESHJET_SECRET_KEY ),
				),
			]
		);

		if ( is_wp_error( $response ) ) {
			return $is_master;
		}

		$json_list  = $response['body'];
		$array_list = json_decode( $json_list, true );

		if ( isset( $array_list['Count'] ) && $array_list['Count'] > 0 ) {
			$data = $array_list['Data'][0];

			if ( isset( $data['IsMaster'] ) && $data['IsMaster'] ) {
				$is_master = true;
			}
		}

		return $is_master;
	}
}
