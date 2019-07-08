<?php
/**
 * Setup Freshjet
 *
 * @package Freshjet
 */

namespace Freshjet;

/**
 * Setup Freshjet
 */
class Setup {
	/**
	 * Settings meta key
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
		// Setup setting groups.
		register_setting( 'freshjet-keys-group', $this->options_key );

		// Setup setting sections.
		add_settings_section( 'freshjet-keys-section', __( 'Mailjet Keys', 'freshjet' ), '', 'freshjet-general-page' );
		add_settings_section( 'freshjet-sender-section', __( 'Sender Identities', 'freshjet' ), '', 'freshjet-general-page' );

		// Setup setting fields.
		add_settings_field( 'freshjet-public-key-field', __( 'API Public Key (SMTP username)', 'freshjet' ), [ $this, 'render_public_key_field' ], 'freshjet-general-page', 'freshjet-keys-section' );
		add_settings_field( 'freshjet-secret-key-field', __( 'API Secret Key (SMTP password)', 'freshjet' ), [ $this, 'render_secret_key_field' ], 'freshjet-general-page', 'freshjet-keys-section' );
		add_settings_field( 'freshjet-sender-name-field', __( 'Sender Name', 'freshjet' ), [ $this, 'render_sender_name_field' ], 'freshjet-general-page', 'freshjet-sender-section' );
		add_settings_field( 'freshjet-sender-email-field', __( 'Sender Email', 'freshjet' ), [ $this, 'render_sender_email_field' ], 'freshjet-general-page', 'freshjet-sender-section' );
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
	 * Setting page styles
	 */
	public function settings_page_styles() {
		$current_screen = get_current_screen();

		if ( 'toplevel_page_freshjet' !== $current_screen->id ) {
			return;
		}

		wp_enqueue_style( 'settings-page', FRESHJET_PLUGIN_URL . '/assets/css/settings-page.css', [], '0.1.0' );
	}
}
