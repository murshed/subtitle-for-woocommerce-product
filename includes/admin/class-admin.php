<?php

namespace WC_Product_Subtitle\Admin;

use function Sodium\add;
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
		public function class_init() {
			if ( version_compare( $GLOBALS['wp_version'], '4.1-alpha', '<' ) ) {
				add_action( 'edit_form_after_title', array( &$this, 'add_subtitle_field' ) );
			} else {
				add_action( 'edit_form_before_permalink', array( &$this, 'add_subtitle_field' ) );
			}
			add_action( 'save_post', array( $this, 'save_product_subtitle' ), 10, 3 );
		}

		/**
		 * On WPOnion Loaded.
		 */
		public function wponion_loaded() {
			if ( false !== wc_ps_option( 'admin_column' ) ) {
				wponion_admin_columns( 'product', __( 'Subtitle' ), array( $this, 'render_subtitle' ) );
			}

			if ( false !== wc_ps_option( 'admin_below_product_title' ) ) {
				wponion_admin_columns( 'product', array(
					'title'     => __( 'Name' ),
					'render'    => array( $this, 'render_subtitle' ),
					'force_add' => true,
				) );
			}

			var_dump( wponion_settings_page_url( '_wc_product_subtitle' ) );
			wponion_plugin_links( wc_product_subtitle()->file() )
				->row_link( __( 'FAQ' ), 'https://wordpress.org/plugins/wc-product-subtitle/faq/' )
				->row_link( __( 'View On Github' ), 'https://github.com/varunsridharan/wc-product-subtitle' )
				->row_link( __( 'Report An Issue' ), 'https://github.com/varunsridharan/wc-product-subtitle/issues' )
				->action_link_before( 'settings', __( 'Settings' ), wponion_settings_page_url( '_wc_product_subtitle' ) );
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
		 * @param $post_id
		 * @param $post
		 */
		public function save_product_subtitle( $post_id, $post ) {
			if ( 'product' !== $post->post_type ) {
				return;
			}
			if ( isset( $_POST['product_subtitle'] ) ) {
				update_product_subtitle( $post_id, wp_kses( $_POST['product_subtitle'], array() ) );
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
				$placeholder = __( 'Subtitle : ' );
				$field       = wpo_field( 'text', 'product_subtitle', '' )
					->placeholder( $placeholder )
					->name( 'product_subtitle' )
					->only_field( true )
					->attribute( 'spellcheck', 'true' )
					->attribute( 'size', '50' )
					->attribute( 'autocomplete', 'false' )
					->attribute( 'id', 'wcps_subtitle' )
					->render( $value, null );

				echo <<<HTML
<div id="subtitlediv"> <div id="subtitlewrap"> $field </div> </div>
<style>
#subtitlediv{ margin-top: 10px; display: inline-block; width: 100%; } 
#wcps_subtitle{
	padding: 3px 8px;
	font-size: 1.5em;
	line-height: 100%;
	height: 1.6em;
	width: 100%;
	display: inline-block;
	outline: none;
	margin: 0 0 3px;
	background-color: #fff;
}
</style>
HTML;
			}
		}
	}
}