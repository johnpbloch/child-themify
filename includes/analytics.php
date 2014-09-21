<?php

class CTF_Analytics {

	protected $hash;

	protected $optIn = 0;

	public function __construct( $hash = false ) {
		$this->hash  = $hash ? $hash : $this->generateHash();
		$this->optIn = (int) get_option( 'ctf_analytics_opt_in', $this->optIn );
	}

	public function run() {
		$this->solicitOptIn();
		$this->hooks();
	}

	public function shutdown() {
		if (
			(int) $this->optIn <= 0 ||
			! is_admin() ||
			$this->hash === get_option( 'ctf_analytics_hash' )
		) {
			return;
		}
		$url         = 'https://api.keen.io/3.0/projects/5419a6cfbcb79c14cc525900/events';
		$requestArgs = array(
			'headers'  => array(
				'Authorization' => 'e8b4545fae070535bc350edede5b7745cdc640f9c6aaa845d9e84353a9b0ca0a4aa4fd2e86575d152' .
				                   '9e5aa3398edfc629c34cac35385a6bf5e802d458354cae61165c48f6d63f48f70320c388cf4258211' .
				                   '8513160439a90552f824abd16f4a48da0318a5dffcf124ed6c16420d4b0633',
				'Content-Type'  => 'application/json',
			),
			'body'     => $this->getBodyJSON(),
			'timeout'  => 0.01,
			'blocking' => false,
		);
		wp_remote_post( $url, $requestArgs );
		update_option( 'ctf_analytics_hash', $this->hash );
	}

	protected function solicitOptIn() {
		if ( ! is_admin() || $this->optIn !== 0 ) {
			return;
		}
		add_settings_error(
			'ctf_analytics_opt_in',
			'ctf_analytics_opt_in',
			sprintf(
				'<strong>%s</strong> %s <a href="#" id="ctf_analytics_more_info">%s</a> | <a href="#" id="ctf_analytics_opt_in">%s</a> | <a href="#" id="ctf_analytics_opt_out">%s</a>',
				esc_html__( 'Hey there!', 'child-themify' ),
				esc_html__( 'Thanks for using Child Themify! To help future development of the plugin, we want to collect some anonymous data, but only if you don\'t mind.', 'child-themify' ),
				esc_html__( 'Learn More', 'child-themify' ),
				esc_html__( 'Opt In', 'child-themify' ),
				esc_html__( 'Opt Out', 'child-themify' )
			),
			'updated'
		);
	}

	protected function hooks() {
		add_action( 'shutdown', array( $this, 'shutdown' ) );
	}

	protected function generateHash() {
		return md5( $GLOBALS['wp_version'] . '|' . PHP_VERSION );
	}

	protected function getBodyJSON() {
		return json_encode( array(
			'stats' => array(
				array(
					'requestID'  => md5( home_url() ),
					'WPVersion'  => $GLOBALS['wp_version'],
					'PHPVersion' => PHP_VERSION,
				)
			)
		) );
	}

}
