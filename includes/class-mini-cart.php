<?php

namespace WC_Product_Subtitle;

if ( ! class_exists( '\WC_Product_Subtitle\Mini_Cart' ) ) {
	/**
	 * Class Mini_Cart
	 *
	 * @package WC_Product_Subtitle
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Mini_Cart extends Display_Handler {
		/**
		 * Mini_Cart constructor.
		 */
		public function __construct() {
			parent::__construct( 'mini_cart' );

			if ( ! empty( $this->get_position() ) ) {
				add_action( 'woocommerce_before_mini_cart', array( &$this, 'init_mini_cart' ) );
			}
		}

		/**
		 * Inits Mini Cart.
		 */
		public function init_mini_cart() {
			if ( ! empty( $this->get_position() ) ) {
				add_filter( 'woocommerce_cart_item_name', array( &$this, 'mini_cart_subtitle' ), 10, 2 );
			}
		}

		/**
		 * @param string     $title
		 * @param bool|array $item
		 *
		 * @return string
		 */
		public function mini_cart_subtitle( $title, $item = false ) {
			$subtitle = $this->render_subtitle( $item['product_id'] );
			return ( $this->is_before() ) ? $subtitle . ' ' . $title : $title . ' ' . $subtitle;
		}
	}
}
