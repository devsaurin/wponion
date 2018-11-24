<?php
/**
 *
 * Initial version created 12-05-2018 / 07:05 PM
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @since 1.0
 * @package
 * @link
 * @copyright 2018 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */

namespace WPOnion\Field;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}


if ( ! class_exists( '\WPOnion\Field\Notice' ) ) {
	/**
	 * Class WPOnion_Field_Notice
	 *
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Notice extends \WPOnion\Field\heading {

		protected $notice_type = 'success';

		public function handle_field_args( $data = array() ) {
			$data['type'] = 'notice';
			return $data;
		}

		/**
		 * Final HTML Output;
		 *
		 * @return mixed;
		 */
		public function output() {
			echo $this->before();
			$auto_close = ( false === $this->data( 'autoclose' ) ) ? '' : ' data-autoclose="' . intval( $this->data( 'autoclose' ) ) . '" ';
			echo '<div class="alert alert-' . $this->data( 'notice_type' ) . '" ' . $auto_close . '>';
			echo $this->data( 'content' );
			if ( true === $this->data( 'close' ) && false === $this->data( 'autoclose' ) ) {
				echo '<a class="wponion-remove dashicons" data-tippy="' . __( 'Hide' ) . '"></a>';
			}
			echo '</div>';
			echo $this->after();
		}

		/**
		 * Final HTML Output;
		 *
		 * @return mixed;
		 */
		protected function field_default() {
			return $this->parse_args( array(
				'notice_type' => $this->notice_type,
				'autoclose'   => false,
				'close'       => true,
			), parent::field_default() );
		}
	}
}
