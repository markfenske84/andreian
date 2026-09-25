<?php
/**
 * Google reCAPTCHA v3 for post comments (no plugin).
 *
 * Define ANDREIAN_RECAPTCHA_SITE_KEY and ANDREIAN_RECAPTCHA_SECRET_KEY in wp-config.php.
 * Optional: ANDREIAN_RECAPTCHA_SCORE_THRESHOLD (float, default 0.5).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** reCAPTCHA v3 action name sent to Google and checked on verify. */
define( 'ANDREIAN_RECAPTCHA_V3_ACTION', 'comment' );

/**
 * Whether both reCAPTCHA keys are configured.
 *
 * @return bool
 */
function andreian_recaptcha_is_configured() {
	return defined( 'ANDREIAN_RECAPTCHA_SITE_KEY' )
		&& defined( 'ANDREIAN_RECAPTCHA_SECRET_KEY' )
		&& ANDREIAN_RECAPTCHA_SITE_KEY
		&& ANDREIAN_RECAPTCHA_SECRET_KEY;
}

/**
 * Minimum score required for v3 (0.0–1.0).
 *
 * @return float
 */
function andreian_recaptcha_score_threshold() {
	if ( defined( 'ANDREIAN_RECAPTCHA_SCORE_THRESHOLD' ) ) {
		return (float) ANDREIAN_RECAPTCHA_SCORE_THRESHOLD;
	}

	return 0.5;
}

/**
 * Whether reCAPTCHA should run on the current comment form.
 *
 * @return bool
 */
function andreian_recaptcha_show_on_comment_form() {
	if ( ! andreian_recaptcha_is_configured() || is_user_logged_in() ) {
		return false;
	}

	if ( ! is_singular( 'post' ) ) {
		return false;
	}

	$post_id = get_queried_object_id();

	return $post_id && comments_open( $post_id );
}

/**
 * Whether a submitted comment should be verified with Google.
 *
 * @param array $commentdata Comment data.
 * @return bool
 */
function andreian_recaptcha_should_verify_comment( $commentdata ) {
	if ( ! andreian_recaptcha_is_configured() || is_user_logged_in() ) {
		return false;
	}

	$post_id = isset( $commentdata['comment_post_ID'] ) ? (int) $commentdata['comment_post_ID'] : 0;

	if ( ! $post_id || 'post' !== get_post_type( $post_id ) ) {
		return false;
	}

	return true;
}

/**
 * Enqueue Google reCAPTCHA v3 and theme handler on post comment forms.
 */
function andreian_recaptcha_enqueue_scripts() {
	if ( ! andreian_recaptcha_show_on_comment_form() ) {
		return;
	}

	$site_key = ANDREIAN_RECAPTCHA_SITE_KEY;

	wp_enqueue_script(
		'google-recaptcha',
		add_query_arg( 'render', rawurlencode( $site_key ), 'https://www.google.com/recaptcha/api.js' ),
		array(),
		null,
		array(
			'in_footer' => true,
			'strategy'  => 'async',
		)
	);

	if ( wp_script_is( 'theme', 'registered' ) ) {
		$wp_scripts = wp_scripts();
		$wp_scripts->registered['theme']->deps[] = 'google-recaptcha';
	}

	wp_localize_script(
		'theme',
		'andreianRecaptcha',
		array(
			'siteKey' => $site_key,
			'action'  => ANDREIAN_RECAPTCHA_V3_ACTION,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'andreian_recaptcha_enqueue_scripts', 1000 );

/**
 * Hidden token field and required v3 disclosure above the submit button.
 *
 * @param string $submit_field Submit field HTML.
 * @param array  $args         Comment form arguments.
 * @return string
 */
function andreian_recaptcha_comment_form_submit_field( $submit_field, $args ) {
	if ( ! andreian_recaptcha_show_on_comment_form() ) {
		return $submit_field;
	}

	$disclosure = sprintf(
		/* translators: 1: Privacy Policy URL, 2: Terms of Service URL. */
		__( 'This site is protected by reCAPTCHA and the Google <a href="%1$s" rel="noopener noreferrer" target="_blank">Privacy Policy</a> and <a href="%2$s" rel="noopener noreferrer" target="_blank">Terms of Service</a> apply.', 'andreian' ),
		'https://policies.google.com/privacy',
		'https://policies.google.com/terms'
	);

	$markup  = '<input type="hidden" name="g-recaptcha-response" value="" class="andreian-recaptcha-token">';
	$markup .= '<p class="comment-form-recaptcha-disclosure">' . wp_kses_post( $disclosure ) . '</p>';

	return $markup . $submit_field;
}
add_filter( 'comment_form_submit_field', 'andreian_recaptcha_comment_form_submit_field', 10, 2 );

/**
 * Verify reCAPTCHA v3 response before WordPress saves the comment.
 *
 * @param array $commentdata Comment data.
 * @return array
 */
function andreian_recaptcha_preprocess_comment( $commentdata ) {
	if ( ! andreian_recaptcha_should_verify_comment( $commentdata ) ) {
		return $commentdata;
	}

	$token = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( $_POST['g-recaptcha-response'] ) ) : '';

	if ( '' === $token ) {
		andreian_recaptcha_comment_failed(
			__( 'Security verification did not complete. Please try submitting your comment again.', 'andreian' )
		);
	}

	$response = wp_remote_post(
		'https://www.google.com/recaptcha/api/siteverify',
		array(
			'timeout' => 10,
			'body'    => array(
				'secret'   => ANDREIAN_RECAPTCHA_SECRET_KEY,
				'response' => $token,
				'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		andreian_recaptcha_comment_failed(
			__( 'Could not verify reCAPTCHA. Please try again in a moment.', 'andreian' )
		);
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( empty( $body['success'] ) ) {
		andreian_recaptcha_comment_failed(
			__( 'Security verification failed. Please try again.', 'andreian' )
		);
	}

	$action = isset( $body['action'] ) ? (string) $body['action'] : '';

	if ( ANDREIAN_RECAPTCHA_V3_ACTION !== $action ) {
		andreian_recaptcha_comment_failed(
			__( 'Security verification failed. Please try again.', 'andreian' )
		);
	}

	$score = isset( $body['score'] ) ? (float) $body['score'] : 0.0;

	if ( $score < andreian_recaptcha_score_threshold() ) {
		andreian_recaptcha_comment_failed(
			__( 'Your comment could not be submitted because it did not pass automated spam checks. If you believe this is an error, try again or contact the site owner.', 'andreian' )
		);
	}

	return $commentdata;
}
add_filter( 'preprocess_comment', 'andreian_recaptcha_preprocess_comment', 10, 1 );

/**
 * Stop comment submission with an error message.
 *
 * @param string $message User-facing message.
 */
function andreian_recaptcha_comment_failed( $message ) {
	wp_die(
		esc_html( $message ),
		esc_html__( 'Comment not submitted', 'andreian' ),
		array(
			'response'  => 403,
			'back_link' => true,
		)
	);
}
