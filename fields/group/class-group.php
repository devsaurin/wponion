<?php
/**
 *
 * Initial version created 21-05-2018 / 06:45 PM
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

if ( ! class_exists( '\WPOnion\Field\Group' ) ) {
	/**
	 * Class group
	 *
	 * @package WPOnion\Field
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Group extends Accordion {
		/**
		 * loop_count
		 *
		 * @var string
		 */
		protected $loop_count = '{wponionCloneID}';

		/**
		 * is_js_sample
		 *
		 * @var bool
		 */
		protected $is_js_sample = false;

		/**
		 * loop_value
		 *
		 * @var array
		 */
		protected $loop_value = array();

		/**
		 * Creates / inits its sub fields.
		 */
		protected function init_subfields() {
			return false;
		}

		/**
		 * Renders Single Field HTML.
		 *
		 * @param $field
		 */
		protected function render_single_field( $field ) {
			$value = ( false === $this->is_js_sample ) ? wponion_get_field_value( $field, $this->loop_value ) : null;

			if ( 'wp_editor' === $field['type'] ) {
				$field['render_js_template'] = true;
				$field['in_group']           = true;
				$field['group_count']        = is_numeric( $this->loop_count ) ? $this->loop_count : '';
			}

			echo $this->sub_field( $field, $value, $this->name( '[' . $this->loop_count . ']' ), false );
		}

		/**
		 * Final HTML Output
		 */
		protected function output() {
			echo $this->before();
			$this->get_first_field();
			$this->loop_count = 0;
			$default_title    = $this->data( 'heading' );
			$count            = ( empty( $this->value ) ) ? 0 : count( $this->value );
			echo '<div class="wponion-group-wrap" data-wponion-clone-count="' . $count . '">';
			if ( is_array( $this->value ) ) {
				foreach ( $this->value as $i => $value ) {
					$this->loop_count       = $this->loop_count + 1;
					$this->loop_value       = $value;
					$this->field['heading'] = $this->get_accordion_title( $value, $default_title );
					$this->render_fields();
				}
			}
			echo '</div>';

			echo $this->sub_field( $this->handle_args( 'label', $this->data( 'add_button' ), array(
				'class'       => array( 'button button-primary' ),
				'type'        => 'button',
				'attributes'  => array(
					'data-wponion-jsid'      => $this->js_field_id(),
					'data-wponion-group-add' => 'yes',
				),
				'only_field'  => true,
				'button_type' => 'button',
				'label'       => __( 'Add New' ),
			) ), false, $this->unique() );

			$this->is_js_sample     = true;
			$this->loop_value       = array();
			$this->loop_count       = '{wponionCloneID}';
			$this->field['heading'] = $default_title;
			$this->catch_output( 'start' );
			$this->render_fields();
			$template           = $this->catch_output( 'stop' );
			$this->is_js_sample = false;
			echo $this->after();

			$this->localize_field( array( 'group_template' => $template ) );
		}

		/**
		 * After Accordion Callback
		 */
		protected function after_accordion() {
			echo '<div class="wponion-group-action">';
			echo $this->sub_field( $this->handle_args( 'label', $this->data( 'remove_button' ), array(
				'class'       => array( 'button button-secondary' ),
				'type'        => 'button',
				'attributes'  => array(
					'data-wponion-jsid' => $this->js_field_id(),
				),
				'only_field'  => true,
				'button_type' => 'button',
				'label'       => __( 'Remove ' ),
			) ), false, $this->unique() );
			echo '</div>';
		}

		/**
		 * Returns all required values to use in js.
		 *
		 * @return array
		 */
		protected function js_field_args() {
			$error_notice = $this->handle_args( 'content', $this->data( 'error_msg' ), array(
				'type' => 'notice_danger',
			), array( 'only_field' => true ) );

			return array(
				'default_heading'        => __( 'Group #[count]' ),
				'heading_counter'        => strpos( $this->data( 'heading' ), '[count]' ),
				'heading'                => $this->data( 'heading' ),
				'limit'                  => $this->data( 'limit' ),
				'error_msg'              => wponion_add_element( $error_notice, false, false ),
				'remove_button_title'    => $this->data( 'remove_button_title' ),
				'matched_heading_fields' => $this->get_accordion_title( array(), $this->data( 'heading' ), true ),
			);
		}

		/**
		 * Validates And Returns A Accordion Title.
		 *
		 * @param      $value
		 * @param      $default
		 * @param bool $is_fields
		 *
		 * @return array|mixed
		 */
		protected function get_accordion_title( $value, $default, $is_fields = false ) {
			$accordion_title = ( true === $is_fields ) ? array() : $default;
			$fields_ids      = $this->get_first_field();

			foreach ( $fields_ids as $id => $val ) {
				if ( $is_fields ) {
					$is_matched = preg_match( '/\b(' . $id . ')\b/', $default );
					if ( $is_matched ) {
						$accordion_title[] = $id;
					}
				} else {
					if ( isset( $value[ $id ] ) && is_string( $value[ $id ] ) ) {
						$is_matched = preg_match( '/\b(' . $id . ')\b/', $accordion_title );
						if ( $is_matched ) {
							$accordion_title = str_replace( $id, $value[ $id ], $accordion_title );
						}
					}
				}
			}

			if ( ! is_array( $accordion_title ) ) {
				if ( false !== strpos( $accordion_title, '[count]' ) ) {
					$accordion_title = str_replace( '[count]', $this->loop_count, $accordion_title );
				}
			}

			return $accordion_title;
		}

		/**
		 * Gets All First Level Field IDS.
		 *
		 * @return array|bool
		 */
		protected function get_first_field() {
			static $fields_ids = false;
			if ( false === $fields_ids ) {
				$fields_ids = wponion_get_all_fields_ids_and_defaults( $this->data( 'fields' ), false );
			}
			return $fields_ids;
		}

		/**
		 * Returns all fields default.
		 *
		 * @return array|mixed
		 */
		protected function field_default() {
			return $this->parse_args( array(
				'add_button'    => __( 'Add New' ),
				'remove_button' => __( 'Remove' ),
				'limit'         => false,
				'heading'       => false,
				'error_msg'     => __( 'You Can\'t Add More..' ),
			), parent::field_default() );
		}

		/**
		 * Loads the required plugins assets.
		 *
		 * @return mixed|void
		 */
		public function field_assets() {
			wp_enqueue_script( 'wponion-cloner' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			parent::field_assets();
		}
	}
}
