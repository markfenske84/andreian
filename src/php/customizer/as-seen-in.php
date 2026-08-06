<?php
// As Seen In section — global logo strip used by the masthead.

require_once get_template_directory() . '/src/php/customizer/controls/class-chw-multi-image-control.php';

/**
 * Sanitize the JSON-encoded list of attachment IDs.
 */
function chw_sanitize_attachment_id_json( $value ) {
    $decoded = json_decode( (string) $value, true );

    if ( ! is_array( $decoded ) ) {
        return '';
    }

    $ids = array_values( array_filter( array_map( 'absint', $decoded ) ) );

    return wp_json_encode( $ids );
}

$wp_customize->add_section('as_seen_in_section', array(
    'title'    => __('As Seen In', 'chw'),
    'priority' => 32,
));

// Section heading label.
$wp_customize->add_setting('as_seen_in_label', array(
    'default'           => 'As Seen In',
    'sanitize_callback' => 'sanitize_text_field',
));

$wp_customize->add_control('as_seen_in_label', array(
    'label'       => __('Heading Label', 'chw'),
    'section'     => 'as_seen_in_section',
    'settings'    => 'as_seen_in_label',
    'description' => __('Small label shown before the logos (e.g. "As Seen In").', 'chw'),
    'type'        => 'text',
));

// Logos repeater (ordered attachment IDs stored as JSON).
$wp_customize->add_setting('as_seen_in_logos', array(
    'default'           => '',
    'sanitize_callback' => 'chw_sanitize_attachment_id_json',
));

$wp_customize->add_control(new CHW_Multi_Image_Control($wp_customize, 'as_seen_in_logos', array(
    'label'       => __('Logos', 'chw'),
    'section'     => 'as_seen_in_section',
    'settings'    => 'as_seen_in_logos',
    'description' => __('Add the publication / award logos. Drag to reorder. Shown in the masthead when "Show As Seen In logos" is enabled on a page.', 'chw'),
)));
