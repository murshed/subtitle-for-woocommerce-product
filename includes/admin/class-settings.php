<?php

namespace WC_Product_Subtitle\Admin;

use VSP\Core\Abstracts\Plugin_Settings;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( '\WC_Product_Subtitle\Admin\Settings' ) ) {
	/**
	 * Class Settings
	 *
	 * @package WC_Product_Subtitle\Admin
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Settings extends Plugin_Settings {
		/**
		 * @var array|\WPO\Field
		 * @access
		 */
		protected $template = array();

		/**
		 * Sets Basic Template.
		 */
		protected function set_template() {
			$this->template['placement'] = wpo_field( 'switcher', 'placement', __( 'Placement ', 'wc-product-subtitle' ), array(
				'switch_style' => 'style-14',
				'switch_width' => '5em',
				'on'           => __( 'Before', 'wc-product-subtitle' ),
				'off'          => __( 'After', 'wc-product-subtitle' ),
				// translators: Added Code Tag
				'desc_field'   => sprintf( __( 'If %1$sBefore%2$s Selected Then Title Will Be Displayed Before The Selected Position', 'wc-product-subtitle' ), '<code>', '</code>' ),
			) );
			$this->template['placement']->dependency( 'position', 'not-empty' );
			$this->template['position'] = wpo_field( 'select', 'position', __( 'Position', 'wc-product-subtitle' ), array(
				'style'      => 'width:15%',
				'desc_field' => __( 'Where to show the subtitle', 'wc-product-subtitle' ),
				'select2'    => true,
			) );
			$this->template['element']  = wpo_field( 'select', 'element', __( 'Element Tag', 'wc-product-subtitle' ), array(
				'style'      => 'width:10%',
				'options'    => wc_product_subtitle_tags(),
				'desc_field' => __( 'Which Type of html tag you need to have', 'wc-product-subtitle' ),
				'select2'    => true,
			) );
		}

		/**
		 * Generates Basic Settings Fields.
		 * Inits Settings.
		 */
		public function fields() {
			$this->set_template();
			$general = $this->builder->container( 'general', __( 'General', 'wc-product-subtitle' ) );

			$this->general( $general->container( 'admin', __( 'General Settings', 'wc-product-subtitle' ) ) );
			$this->cart_checkout_page( $general->container( 'cart_page', __( 'Cart Page', 'wc-product-subtitle' ) ) );
			$this->mini_cart( $general->container( 'mini-cart', __( 'Mini Cart', 'wc-product-subtitle' ) ) );
			$this->cart_checkout_page( $general->container( 'checkout_page', __( 'Checkout Page', 'wc-product-subtitle' ) ), true );
			$this->order_view_page( $general->container( 'order-view-page', __( 'Order View Page', 'wc-product-subtitle' ) ) );
			$this->shop_page( $general->container( 'shop-page', __( 'Shop Page', 'wc-product-subtitle' ) ) );
			$this->single_product( $general->container( 'single-product', __( 'Single Product', 'wc-product-subtitle' ) ) );
			$this->email( $general->container( 'order-email', __( 'Email', 'wc-product-subtitle' ) ) );
			$this->shortcode( $general->container( 'shortcode', __( 'Shortcode', 'wc-product-subtitle' ) ) );

			$this->builder->container( 'system-info', __( 'System Tool/Info', 'wc-product-subtitle' ), 'dashicons dashicons-info' )
				->callback( 'wponion_sysinfo' )
				->set_var( 'developer', 'varunsridharan23@gmail.com' );
		}

		/**
		 * General Settings Fields.
		 *
		 * @param \WPO\Container $container
		 */
		protected function general( $container ) {
			$img = wpo_image( wc_product_subtitle()->plugin_url( 'assets/img/wcps-example.jpg' ), wc_product_subtitle()->plugin_url( 'assets/img/wcps-example-big.jpg' ) )->tooltip( __( 'Click To View Full Image', 'wc-product-subtitle' ), array(
				'arrow'     => true,
				'placement' => 'bottom',
				'size'      => 'small',
			) );

			$container->subheading( __( 'Admin Settings', 'wc-product-subtitle' ) );
			$container->switcher( 'admin_column', __( 'Subtitle Column', 'wc-product-subtitle' ) )
				->switch_style( 'style-14' )
				->on( __( 'Enable', 'wc-product-subtitle' ) )
				->off( __( 'Disable', 'wc-product-subtitle' ) )
				->switch_width( '5em' )
				->desc_field( __( 'If enabled a custom product subtitle column will be added in product listing table', 'wc-product-subtitle' ) );
			$container->switcher( 'admin_below_product_title', __( 'Below Product Title', 'wc-product-subtitle' ) )
				->switch_style( 'style-14' )
				->on( __( 'Yes', 'wc-product-subtitle' ) )
				->off( __( 'No', 'wc-product-subtitle' ) )
				->switch_width( '3em' )
				->desc_field( __( 'If Enabled Subtitle Will Be Shown Below The Product Title In Product List Table', 'wc-product-subtitle' ) . ' <br/><br/>' . $img );

			$container->switcher( 'admin_wp_editor', __( 'Subtitle HTML Editor', 'wc-product-subtitle' ) )
				->switch_style( 'style-14' )
				->on( __( 'HTML Editor', 'wc-product-subtitle' ) )
				->off( __( 'Simple Input', 'wc-product-subtitle' ) )
				->switch_width( '8em' )
				->desc_field( __( 'If Enabled Then `HTML` Editor Will Be Shown Instead Of `Text Input`', 'wc-product-subtitle' ) );

			$iswcpdf_active    = wp_is_plugin_active( 'woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packingslips.php' );
			$iswcpdf_installed = wp_is_plugin_installed( 'woocommerce-pdf-invoices-packing-slips/woocommerce-pdf-invoices-packingslips.php' );
			$wrap_label        = false;
			if ( false === $iswcpdf_active ) {
				$title      = ( false === $iswcpdf_installed ) ? __( 'Plugin Not Installed', 'wc-product-subtitle' ) : __( 'Plugin Not Active', 'wc-product-subtitle' );
				$type       = ( false === $iswcpdf_installed ) ? 'danger' : 'warning';
				$wrap_label = array(
					'content'   => $title,
					'placement' => 'top-left',
					'type'      => $type,
				);
			}

			$container->subheading( __( 'Integrations', 'wc-product-subtitle' ) );
			$container->switcher( 'wcpdfinvoiceandpackingslip', __( 'WC PDF Invoices & Packing Slip', 'wc-product-subtitle' ) )
				->badge( $wrap_label )
				->desc_field( __( 'Enable this field to show subtitles in [WooCommerce PDF Invoice & Packing Slip](https://wordpress.org/plugins/woocommerce-pdf-invoices-packing-slips/) Plugin', 'wc-product-subtitle' ) );

			$container->subheading( __( 'F.A.Q', 'wc-product-subtitle' ) );
			$container->faq()
				->faq( __( 'How Do I Style Subtitles ?', 'wc-product-subtitle' ), wc_product_subtitle()->plugin_path( 'assets/markdown/how-do-i-style-subtitles.md' ) );
		}

		/**
		 * order_view_page fields.
		 *
		 * @param \WPO\Container $container
		 */
		protected function order_view_page( $container ) {
			$container->subheading( __( 'Order View Page Subtitle Configuration', 'wc-product-subtitle' ) );

			$container->content( '
' . __( 'This Configration Will Be Used In The Following Pages', 'wc-product-subtitle' ) . '
' . __( '1. Order Thank You Page', 'wc-product-subtitle' ) . '
' . __( '2. MyAccount Order View Page', 'wc-product-subtitle' ) . '
' )
				->markdown( true );

			$fieldset = $container->set_group( 'order_view_page' );
			$fieldset->field( clone( $this->template['position'] ) )
				->options( wp_product_subtitle_placements( 'order_view' ) );
			$fieldset->field( clone( $this->template['placement'] ) );
			$fieldset->field( clone( $this->template['element'] ) );

			$container->subheading( __( 'Frequently Asked Questions', 'wc-product-subtitle' ) );
			$container->faq()
				->faq( __( 'Subtitle not visible in Thank You Page & MyAccount Order View Page ?', 'wc-product-subtitle' ), wc_product_subtitle()->plugin_path( 'assets/markdown/subtitle-not-visible-in-thankyou-page-myaccount-order-view-page.md' ) );
		}

		/**
		 * shop page fields.
		 *
		 * @param \WPO\Container $container
		 */
		protected function shop_page( $container ) {
			$container->subheading( __( 'Shop Page Subtitle Configuration', 'wc-product-subtitle' ) );

			$fieldset = $container->set_group( 'shop_page' );
			$fieldset->field( clone( $this->template['position'] ) )
				->options( wp_product_subtitle_placements( 'shop' ) );
			$fieldset->field( clone( $this->template['placement'] ) );
			$fieldset->field( clone( $this->template['element'] ) );

			$container->subheading( __( 'Frequently Asked Questions', 'wc-product-subtitle' ) );
			$container->faq()
				->faq( __( 'Subtitle not visible in shop page', 'wc-product-subtitle' ), wc_product_subtitle()->plugin_path( 'assets/markdown/subtitle-not-visible-in-shop-page.md' ) );
		}

		/**
		 * single product fields.
		 *
		 * @param \WPO\Container $container
		 */
		protected function single_product( $container ) {
			$container->subheading( __( 'Single Product Page Subtitle Configuration', 'wc-product-subtitle' ) );

			$fieldset = $container->set_group( 'single_product' );
			$fieldset->field( clone( $this->template['position'] ) )
				->options( wp_product_subtitle_placements( 'single' ) );
			$fieldset->field( clone( $this->template['placement'] ) );
			$fieldset->field( clone( $this->template['element'] ) );

			$container->subheading( __( 'Frequently Asked Questions', 'wc-product-subtitle' ) );

			$container->faq()
				->faq( __( 'Subtitle not visible in single product page', 'wc-product-subtitle' ), wc_product_subtitle()->plugin_path( 'assets/markdown/subtitle-not-visible-in-single-product-page.md' ) );
		}

		/**
		 * cart / checkout page fields.
		 *
		 * @param \WPO\Container $container
		 * @param bool           $is_checkout
		 */
		protected function cart_checkout_page( $container, $is_checkout = false ) {
			$container = $container->set_group( true );
			$title     = ( false === $is_checkout ) ? __( 'Cart', 'wc-product-subtitle' ) : __( 'Checkout', 'wc-product-subtitle' );

			$container->subheading( $title . ' ' . __( 'Page Subtitle Configuration', 'wc-product-subtitle' ) );
			$container->field( clone( $this->template['position'] ) )
				->options( wp_product_subtitle_placements( 'cart' ) );
			$container->field( clone( $this->template['placement'] ) );
			$container->field( clone( $this->template['element'] ) );

			$container->subheading( __( 'Frequently Asked Questions', 'wc-product-subtitle' ) );

			$faq = $container->faq();
			// translators: Added Current Section Title
			$faq->faq( sprintf( __( 'Subtitle not visible in %s page ?', 'wc-product-subtitle' ), $title ), wc_product_subtitle()->plugin_path( 'assets/markdown/cart-page.md' ) );
		}

		/**
		 * mini cart fields.
		 *
		 * @param \WPO\Container $container
		 */
		protected function mini_cart( $container ) {
			$container->subheading( __( 'Mini Cart Configuration', 'wc-product-subtitle' ) );
			$fieldset = $container->set_group( 'mini_cart' );
			$fieldset->add( clone $this->template['position'] )
				->options( wp_product_subtitle_placements( 'mini_cart' ) );

			$fieldset->add( clone $this->template['placement'] );
			$fieldset->add( clone $this->template['element'] );
		}

		/**
		 * shortcode fields.
		 *
		 * @param \WPO\Container $container
		 */
		protected function shortcode( $container ) {
			$container->subheading( __( 'Shortcode Subtitle Configuration', 'wc-product-subtitle' ) );
			$fieldset = $container->set_group( 'shortcode' );
			$fieldset->field( clone( $this->template['element'] ) );
			$fieldset->markdown( wc_product_subtitle()->plugin_path( 'assets/markdown/shortcode.md' ) );
		}

		/**
		 * Email fields.
		 *
		 * @param \WPO\Container $container
		 */
		protected function email( $container ) {
			$container->subheading( __( 'Email Configuration', 'wc-product-subtitle' ) );
			$fieldset = $container->set_group( 'email' );
			$fieldset->add( clone $this->template['position'] )
				->options( wp_product_subtitle_placements( 'email' ) );
			$fieldset->add( clone $this->template['placement'] );
			$fieldset->add( clone $this->template['element'] );

			$before = wpo_field( 'text', 'before_subtitle', __( 'Before Subtitle', 'wc-product-subtitle' ) )
				->help( __( 'HTML Tags Are Supported', 'wc-product-subtitle' ) )
				->horizontal( true )
				->wrap_class( 'col-xs-12 col-md-6' )
				->style( 'width:100%;' );
			$after  = wpo_field( 'text', 'after_subtitle', __( 'After Subtitle', 'wc-product-subtitle' ) )
				->help( __( 'HTML Tags Are Supported', 'wc-product-subtitle' ) )
				->horizontal( true )
				->wrap_class( 'col-xs-12 col-md-6' )
				->style( 'width:100%;' );

			$fieldset->content( __( 'Email Before & After Are Used To Add Custom Line Brakes Before & After The Subtitle To Style It Based On Your Needs', 'wc-product-subtitle' ) );

			$html = $fieldset->accordion( 'html' )
				->heading( __( 'HTML Email Before & After', 'wc-product-subtitle' ) );
			$html->add( clone $before );
			$html->add( clone $after );

			$plain = $fieldset->accordion( 'plain' )
				->heading( __( 'Plain Text Email Before & After', 'wc-product-subtitle' ) );
			$plain->add( clone $before );
			$plain->add( clone $after );
		}
	}
}
