<?php
/**
 *
 * Initial version created 15-05-2018 / 06:17 AM
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

if ( ! class_exists( '\WPOnion\Field\Image_Select' ) ) {
	/**
	 * Class Image_Select
	 *
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Image_Select extends checkbox_radio {
		/**
		 * Final HTML Output;
		 *
		 * @return mixed;
		 */
		protected function output() {
			$this->field['type'] = $this->field['image_type'];
			return parent::output();
		}

		/**
		 * Renders elementHTML.
		 *
		 * @param $label_attr
		 * @param $field_attr
		 * @param $value
		 * @param $attr
		 * @param $options
		 *
		 * @return string
		 */
		protected function _element_html( $label_attr, $field_attr, $value, $attr, $options ) {
			return '<div class="wponion-checker">
				<label ' . wponion_array_to_html_attributes( $label_attr ) . '>
					<input ' . $field_attr . ' ' . $this->checked( $value, $attr['value'], 'checked' ) . '  />
					<div class="wponion-image-select-thumbnail wponion-checker-content"><img alt="" src="' . $options['label'] . '" /></div>
				</label>
			</div>';
		}


		protected function field_default() {
			return $this->parse_args( parent::field_default(), array( 'image_type' => 'checkbox' ) );
		}
	}
}
