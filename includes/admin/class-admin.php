<?php

namespace WC_Product_Subtitle\Admin;

use VSP\Base;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}


if ( ! class_exists( '\WC_Product_Subtitle\Admin\Admin' ) ) {
	/**
	 * Class Admin
	 *
	 * @package WC_Product_Subtitle\Admin
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Admin extends Base {
		/**
		 * On Class Init.
		 */
		public function __construct() {
			$hook = ( version_compare( $GLOBALS['wp_version'], '4.1-alpha', '<' ) ) ? 'edit_form_after_title' : 'edit_form_before_permalink';
			/* @uses add_subtitle_field */
			add_action( $hook, array( &$this, 'add_subtitle_field' ) );
			/* @uses save_product_subtitle */
			add_action( 'save_post', array( $this, 'save_product_subtitle' ), 10, 3 );

			if ( false !== wc_ps_option( 'admin_column' ) ) {
				wponion_admin_columns( 'product', __( 'Subtitle', 'wc-product-subtitle' ), array(
					$this,
					'render_subtitle',
				) );
			}

			if ( false !== wc_ps_option( 'admin_below_product_title' ) ) {
				wponion_admin_columns( 'product', array(
					'title'     => __( 'Name', 'wc-product-subtitle' ),
					'render'    => array( $this, 'render_subtitle' ),
					'force_add' => true,
				) );
			}

			wponion_plugin_links( wc_product_subtitle()->file() )
				->row_link( __( 'FAQ', 'wc-product-subtitle' ), 'https://wordpress.org/plugins/wc-product-subtitle/faq/' )
				->row_link( __( 'View On Github', 'wc-product-subtitle' ), 'https://github.com/varunsridharan/wc-product-subtitle' )
				->row_link( wpo_icon( 'dashicons dashicons-welcome-add-page' ) . __( 'Report An Issue', 'wc-product-subtitle' ), 'https://github.com/varunsridharan/wc-product-subtitle/issues' )
				->row_link( wpo_icon( 'dashicons dashicons-heart' ) . __( 'Donate', 'wc-product-subtitle' ), 'https://paypal.me/varunsridharan' )
				->action_link_before( 'settings', __( 'Settings', 'wc-product-subtitle' ), admin_url( 'admin.php?page=product-subtitle' ) )
				->action_link_after( 'sysinfo', __( 'System Info', 'wc-product-subtitle' ), admin_url( 'admin.php?page=product-subtitle&container-id=system-info' ) );
		}

		/**
		 * Renders Product Subtitle In Admin Column.
		 *
		 * @param $post_id
		 * @param $column
		 */
		public function render_subtitle( $post_id, $column ) {
			switch ( $column ) {
				case 'subtitle':
					echo the_product_subtitle( $post_id );
					break;
				case 'name':
					echo '<br/><span style="margin-top:4px;display: inline-block;"><i>' . get_product_subtitle( $post_id ) . '</i></span>';
					break;
			}
		}

		/**
		 * Stores Subtitle In Product EDIT Page.
		 *
		 * @param $post_id
		 * @param $post
		 */
		public function save_product_subtitle( $post_id, $post ) {
			if ( 'product' !== $post->post_type ) {
				return;
			}
			if ( isset( $_POST['product_subtitle'] ) ) {
				update_product_subtitle( $post_id, wp_kses_post( $_POST['product_subtitle'] ) );
			}
		}

		/**
		 * Add Product Subtitle Field In Post Edit View.
		 *
		 * @param \WP_Post $post
		 */
		public function add_subtitle_field( $post ) {
			if ( 'product' === $post->post_type ) {
				global $post;
				$post_id     = $post->ID;
				$value       = get_product_subtitle( $post_id );
				$placeholder = __( 'Subtitle : ', 'wc-product-subtitle' );

				if ( wc_ps_option( 'admin_wp_editor' ) ) {
					/* @var \WPO\Fields\WP_Editor $field */
					$field = wpo_field( 'wp_editor', 'product_subtitle' );
					$field = $field->horizontal( true )
						->title( __( 'Product Subtitle', 'wc-product-subtitle' ) )
						->settings( array(
							'media_buttons'    => false,
							'wpautop'          => false,
							'quicktags'        => false,
							'teeny'            => true,
							'drag_drop_upload' => false,
							'textarea_rows'    => 2,
						) )
						->debug( false )
						->wrap_id( 'wc_product_subtitle' );
					$field = $field->render( $value, null );
					wponion_load_core_assets();
					wp_add_inline_script( 'wponion-core', "window.wponion.hooks.addAction( 'wponion_init', 'wcps', function() { window.wponion_init_field( 'wp_editor', jQuery( 'div#wc_product_subtitle' ) ); } );" );
				} else {
					$field = wpo_field( 'text', 'product_subtitle', '' )
						->placeholder( $placeholder )
						->name( 'product_subtitle' )
						->only_field( true )
						->attribute( 'spellcheck', 'true' )
						->attribute( 'size', '50' )
						->attribute( 'autocomplete', 'false' )
						->attribute( 'id', 'wcps_subtitle' )
						->render( $value, null );
					$field = '<div id = "subtitlediv"> <div id="subtitlewrap"> ' . $field . ' </div> </div> ';
				}
				echo <<<HTML
$field
<style>
div#wc_product_subtitle.wponion-element {display: inline-block;width: 100%;margin: 10px 0;}
div#wc_product_subtitle.wponion-element .wponion-field-title > h4{font-size: 1.3em;margin-top: 0;margin-bottom: 15px;}
#subtitlediv{ margin-top: 10px; display: inline-block; width: 100%; } 
#wcps_subtitle{padding: 3px 8px;font-size: 1.5em;line-height: 100%;height: 1.6em;width: 100%;display: inline-block;outline: none;margin: 0 0 3px;background-color: #fff;}
</style>
<script>
	
</script>
HTML;
			}
		}
	}
}
