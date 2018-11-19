<?php
/**
 *
 * Initial version created 25-05-2018 / 09:40 AM
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @since 1.0
 * @package
 * @link
 * @copyright 2018 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */

namespace WPOnion;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}
if ( ! class_exists( 'WPOnion\Value_API' ) ) {
	/**
	 * Class WPOnion_Values
	 *
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class Value_API extends \WPOnion\Core\Array_Finder {
		/**
		 * variable
		 *
		 * @var string
		 */
		protected $variable = 'contents';

		/**
		 * unique
		 *
		 * @var null
		 */
		protected $unique = null;

		/**
		 * fields
		 *
		 * @var array
		 */
		protected $fields = array();

		/**
		 * field_ids
		 *
		 * @var array
		 */
		protected $field_ids = array();

		/**
		 * Stores DB Values.
		 *
		 * @var array
		 */
		protected $db_values = array();

		/**
		 * Value_API constructor.
		 *
		 * @param array $db_values
		 * @param array $fields
		 * @param array $args
		 */
		public function __construct( $db_values = array(), $fields = array(), $args = array() ) {
			$this->{$this->variable} = null;
			$this->db_values         = $db_values;
			$this->fields            = $fields;
			$args                    = $this->parse_args( $args, $this->defaults() );
			$this->plugin_id         = $args['plugin_id'];
			$this->module            = $args['module'];
			$this->unique            = $args['unique'];
			parent::__construct();
			$this->path_separator = '/';
			$this->init();
		}

		/**
		 * @param          $path
		 * @param callable $callback
		 * @param bool     $create_path
		 * @param null     $current_offset
		 */
		protected function call_at_path( $path, callable $callback, $create_path = false, &$current_offset = null ) {
			if ( null === $current_offset ) {
				$current_offset = &$this->{$this->variable};
				if ( is_string( $path ) && '' === $path ) {
					$callback( $current_offset );
					return;
				}
			}

			$explode_path = $this->explode( $path );
			$next_path    = array_shift( $explode_path );

			if ( $current_offset instanceof \WPOnion\Bridge\Value ) {
				$current_offset = $current_offset->get( $next_path );
			} else {
				if ( ! isset( $current_offset[ $next_path ] ) ) {
					if ( $create_path ) {
						$current_offset[ $next_path ] = [];
					} else {
						return;
					}
				}
			}

			if ( count( $explode_path ) > 0 ) {
				if ( $current_offset instanceof \WPOnion\Bridge\Value ) {
					$this->call_at_path( $this->implode( $explode_path ), $callback, $create_path, $current_offset );
				} else {
					$this->call_at_path( $this->implode( $explode_path ), $callback, $create_path, $current_offset[ $next_path ] );
				}
			} else {
				if ( $current_offset instanceof \WPOnion\Bridge\Value ) {
					$callback( $current_offset );
				} else {
					$callback( $current_offset[ $next_path ] );
				}
			}
		}

		/**
		 * @param null $path
		 * @param null $default
		 *
		 * @return mixed|\WPOnion\Bridge\Value|\WPOnion\Value\image|array
		 */
		public function get( $path = null, $default = null ) {
			return parent::get( $path, $default );
		}

		/**
		 * @param $parent
		 * @param $new
		 *
		 * @return string
		 */
		protected function path_string( $parent, $new ) {
			return ( empty( $parent ) ) ? $new : $parent . '' . $this->path_separator . $new;
		}

		/**
		 * @param $field
		 *
		 * @return bool|mixed
		 */
		protected function init_field( $field ) {
			$value = wponion_get_field_value( $field, $this->db_values );
			$class = wponion_field_value_class( $field );
			if ( class_exists( $class ) ) {
				$value = new $class( $field, $value, array(
					'unique'    => $this->unique,
					'plugin_id' => $this->plugin_id(),
					'module'    => $this->module(),
				) );
			}
			return $value;
		}

		/**
		 * @param array  $array
		 * @param string $parent_id
		 */
		protected function init( $array = array(), $parent_id = '' ) {
			$array = ( ! empty( $array ) ) ? $array : $this->fields;
			if ( $array->has_fields() ) {
				foreach ( $array->fields() as $field ) {
					if ( true === wponion_valid_field( $field ) && true === wponion_valid_user_input_field( $field ) ) {
						if ( wponion_is_unarrayed( $field ) && isset( $field['fields'] ) ) {
							$this->init( $field, $this->path_string( $parent_id, $field['id'] ) );
						} else {
							$this->set( $this->path_string( $parent_id, $field['id'] ), $this->init_field( $field ) );
						}
					}
				}
			} elseif ( $array->has_sections() ) {
				foreach ( $array->sections() as $section ) {
					$this->init( $section );
				}
			} else {
				foreach ( $array as $page ) {
					if ( $page->has_sections() || $page->has_fields() ) {
						$this->init( $page );
					} else {
						var_dump( $page );
					}
				}
			}
		}

		/**
		 * Returns Class Defaults.
		 *
		 * @return array
		 */
		protected function defaults() {
			return array(
				'module'    => false,
				'unique'    => false,
				'plugin_id' => false,
			);
		}
	}
}
