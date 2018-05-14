<?php
/**
 *
 * Initial version created 12-05-2018 / 07:17 PM
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @since 1.0
 * @package
 * @link
 * @copyright 2018 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'WPOnion_Field_card' ) ) {
	class WPOnion_Field_card extends WPOnion_Field {
		protected function output() {
			if ( ! $this->has( 'options' ) ) {
				return false;
			}

			echo '<div class="row">';
			foreach ( $this->data( 'options' ) as $option ) {
				$option = $this->parse_args( $option, array(
					'image'           => false,
					'content'         => false,
					'body'            => false,
					'wrap_attributes' => false,
					'wrap_class'      => false,
				) );

				$wrap_attr          = $option['wrap_attributes'];
				$wrap_attr['class'] = isset( $wrap_attr['class'] ) ? $wrap_attr['class'] . ' ' . $option['wrap_class'] . ' card ' : $option['wrap_class'] . ' card ';

				echo '<div class="' . $this->data( 'card_col' ) . '">';
				echo '<div ' . wponion_array_to_html_attributes( $wrap_attr ) . '>';
				if ( false !== $option['image'] ) {
					$image = $this->handle_data( $option['image'], array(
						'src'        => false,
						'attributes' => array(),
						'alt'        => false,
					), 'src' );

					if ( ! isset( $image['attributes']['src'] ) ) {
						$image['attributes']['src'] = $image['src'];
					}

					if ( ! isset( $image['attributes']['alt'] ) ) {
						$image['attributes']['alt'] = $image['alt'];
					}

					echo '<img ' . wponion_array_to_html_attributes( $image['attributes'] ) . '/>';
				}

				if ( false !== $option['body'] || false !== $option['content'] ) {
					echo '<div class="card-body">';
					echo $option['body'];
					echo $option['content'];
					echo '</div>';
				}

				echo '</div>';
				echo '</div>';
			}

			echo '</div>';
		}

		public function field_assets() {
			// TODO: Implement field_assets() method.
		}

		protected function field_default() {
			return array(
				'options'  => array(),
				'card_col' => 'col',

			);
		}
	}
}