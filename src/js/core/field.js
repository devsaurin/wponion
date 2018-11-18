//import { array_merge, empty, is_callable, is_jquery, is_undefined } from 'vsp-js-helper/index';
//const to_jquery    = require( 'vsp-js-helper/index' ).to_jquery;
/* global swal:true */
/* global console:true */

const array_merge  = require( 'vsp-js-helper/index' ).array_merge;
const empty        = require( 'vsp-js-helper/index' ).empty;
const is_callable  = require( 'vsp-js-helper/index' ).is_callable;
const is_jquery    = require( 'vsp-js-helper/index' ).is_jquery;
const is_undefined = require( 'vsp-js-helper/index' ).is_undefined;

import $wponion from './core';
import $wponion_debug from './debug';
import WPOnion_Module from './module';
import WPOnion_Validation from '../core/validation';

/**
 * WPOnion Field Abstract Class.
 */
export default class extends WPOnion_Module {
	constructor( $selector, $context, $config = null ) {
		super( $selector, $context );
		this.set_args( false );
		this.field_debug();
		this.config = $config;
		this.init();
		this.js_error_handler();
		this.js_validator();
	}

	init() {
	}

	js_error( err ) {
		err.error.appendTo( this.element.find( '.wponion-fieldset' ) );
	}

	js_error_handler( element = this.element ) {
		element.on( 'wponion_js_validation_message', '> .wponion-fieldset :input', ( e, data ) => this.js_error( data ) );
	}

	js_validator() {
		if( false === is_undefined( this.option( 'js_validate', false ) ) ) {
			if( false !== this.option( 'js_validate', false ) ) {
				this.maybe_js_validate_elem( this.option( 'js_validate', false ), this.element );
			}
		}
	}

	maybe_js_validate_elem( $args, $elem ) {
		if( WPOnion_Validation.get_form() ) {
			this.js_validate_elem( $args, $elem );
		}
	}

	js_validate_elem( $args, $elem ) {
		$elem.find( ':input' ).each( function() {
			jQuery( this ).rules( 'add', $args );
		} );
	}

	handle_args( $arg, $key = false ) {
		let $args   = $wponion.js_func( $arg ),
			$exists = $wponion_debug.get( this.id(), { 'PHP Args': {}, 'JS Args': {} } );
		$exists     = array_merge( $exists, { 'PHP Args': {}, 'JS Args': {} } );

		if( false === $key ) {
			$exists[ 'JS Args' ] = $args;
		} else {
			$exists[ 'JS Args' ][ $key ] = $args;
		}
		$wponion_debug.add( this.id(), $exists );
		return $args;
	}

	field_debug() {
		if( false !== $wponion_debug.get( this.id() ) ) {
			return;
		}

		let $info = this.option( 'debug_info' ),
			$arr  = {};

		if( false === is_undefined( $info ) ) {
			if( false === empty( $info ) ) {
				$arr[ 'Raw Field Args' ] = $info[ 'Raw Field Args' ];
				$arr.Field               = $info[ 'Field Args' ];
				$arr[ 'Field Errors' ]   = $info[ 'Field Errors' ];
				$arr[ 'Field Value' ]    = $info[ 'Field Value' ];
				$arr[ 'Plugin ID' ]      = $info[ 'Plugin ID' ];
				$arr.Module              = $info.Module;
				$arr.Unique              = $info.Unique;
				$wponion_debug.add( this.id(), { 'PHP Args': $arr, 'JS Args': {} } );
			}
		}

		let $found = false;
		if( !this.element.hasClass( 'wponion-field-debug' ) ) {
			let $ID   = this.id(),
				$elem = jQuery( 'div.wponion-element[data-wponion-jsid=' + $ID + ']' );
			if( $elem.hasClass( 'wponion-field-debug' ) ) {
				$found = $elem;
			}
		} else {
			$found = this.element;
		}

		if( false !== $found ) {
			let $this = this;

			$found.find( '> .wponion-field-title > h4' )
				  .attr( 'title', $wponion.txt( 'click_to_view_debug_info', 'Click To View Field Debug Info' ) )
				  .tippy( {
					  arrow: true,
					  arrowType: 'round',
					  placement: 'bottom',
					  theme: 'light',
					  animation: 'scale'
				  } );

			$found.find( '> .wponion-field-title > h4' ).on( 'click', () => {
				let $d          = $this.id() + 'debugINFO',
					$notice_txt = '<p class=\'wponion-field-debug-notice\'>' + $wponion.option( 'debug_notice' ) + '</p>',
					$elem       = jQuery( '<div id="' + $d + '" class="wponion-field-debug-popup" ><div id="' + $d + '" ></div>' + $notice_txt + '</div>' );
				let $data       = $wponion_debug.get( $this.id() );
				swal( {
					html: $elem,
					showConfirmButton: true,
					confirmButtonText: $wponion.txt( 'get_json_output', 'As JSON' ),
					showCloseButton: false,
					width: '800px',
					onOpen: () => jQuery( '#swal2-content > div > #' + $d ).jsonView( $data )
				} ).then( ( result ) => {
					if( result.value ) {
						swal( {
							width: '600px',
							html: '<textarea style="min-width:550px; min-height:300px">' + JSON.stringify( $wponion_debug.get( $this.id() ) ) + '</textarea>'
						} );
					}
				} );
			} );

			$found.find( '> .wponion-fieldset .wponion-field-debug-code-gen' ).on( 'click', () => {
				swal( {
					html: this.option( 'debug_field_code' ),
					width: '800px',
				} );
			} );
		}
	}

	options() {
		if( this._args === false ) {
			this._args = $wponion.windowArgs( this.id() );
		}
		return this._args;
	}

	option( $key = '', $default = {} ) {
		let $args = this.options();
		return ( false === is_undefined( $args[ $key ] ) ) ? $args[ $key ] : $default;
	}

	id() {
		return $wponion.fieldID( this.element );
	}

	module() {
		return this.option( 'module', false );
	}

	plugin_id() {
		return this.option( 'plugin_id', false );
	}

	ajax( $action = '', $data = {} ) {
		let $ajax_key         = $wponion.option( 'ajax_action_key' );
		let $default          = {
			plugin_id: this.plugin_id(),
			module: this.module(),
		};
		$default[ $ajax_key ] = $action;
		$data.data            = ( false === is_undefined( $data.data ) ) ? array_merge( $default, $data.data ) : $default;

		return $wponion.ajax( $data );
	}

	init_field( $elem, $type ) {
		let $_instances = [];
		if( !is_jquery( $elem ) ) {
			$elem = this.element.find( $elem );
		}
		let $this = this;
		$elem.each( function() {
			let $class = $wponion.get_field_class( $type );
			if( false !== $class ) {
				try {
					if( is_callable( $class ) ) {
						$_instances.push( new $class( jQuery( this ) ) );
					}
				} catch( e ) {
					console.log( jQuery( this ) );
					console.log( 'Error: ' + e + ' | For : ' + $type );
					console.log( '--------------------------------------------------------' );
				}
			}
		} );
	}

	reload() {
		wp.hooks.addAction( 'wponion_before_fields_reload' );
		this.init_field( '.wponion-element-icon_picker', 'icon_picker' );
		this.init_field( '.wponion-element-font_picker', 'font_selector' );
		this.init_field( '.wponion-element-accordion', 'accordion' );
		this.init_field( '.wponion-element-group', 'group' );
		this.init_field( '.wponion-element-text:not(.wponion-inputmask)', 'text' );
		this.init_field( '.wponion-element-textarea', 'textarea' );
		this.init_field( '.wponion-element-background', 'background' );
		this.init_field( '.wponion-element-image_select', 'image_select' );
		this.init_field( '.wponion-element-select', 'select' );
		this.init_field( '.wponion-element-switcher', 'switcher' );
		this.init_field( '.wponion-element-color_palette', 'color_palette' );
		this.init_field( '.wponion-element-wp_editor', 'wp_editor' );
		this.init_field( '.wponion-element-fieldset', 'fieldset' );
		this.init_field( 'input[data-wponion-inputmask]', 'inputmask' );
		this.init_field( '.wponion-element-wp_link', 'wp_links' );
		this.init_field( '.wponion-element-checkbox', 'checkbox_radio' );
		this.init_field( '.wponion-element-radio', 'checkbox_radio' );
		this.init_field( '.wponion-element-key_value', 'keyvalue_pair' );
		this.init_field( '.wponion-element-color_picker', 'color_picker' );
		this.init_field( '.wponion-element-date_picker', 'date_picker' );
		this.init_field( '.wponion-element-gallery', 'gallery' );
		this.init_field( '.wponion-element-upload', 'upload' );
		this.init_field( '.wponion-element-image', 'image_upload' );
		this.init_field( '.wponion-element-tab', 'jquery_tab' );
		this.init_field( '.wponion-field-tooltip', 'field_tooltip' );
		this.init_field( '.wponion-element-google_maps', 'google_maps' );
		this.init_field( '.wponion-help', 'field_tooltip' );
		this.init_field( '.wponion-wrap-tooltip', 'field_tooltip' );
		this.init_field( '.wponion-element-clone', 'clone_element' );
		this.init_field( '.select2', 'select2' );
		this.init_field( '.chosen', 'chosen' );
		this.init_field( '.selectize', 'selectize' );
		this.init_field( '.wponion-element-sorter', 'sorter' );
		this.init_field( '.wponion-element-typography', 'typography' );
		this.init_field( '.wponion-element-oembed', 'oembed' );
		wp.hooks.addAction( 'wponion_after_fields_reload' );
		return this;
	}

	set_args( $args ) {
		this._args = $args;
		return this;
	}

	get_field_parent_by_id( $elem ) {
		let $ID = $wponion.fieldID( $elem );
		return jQuery( 'div.wponion-element[data-wponion-jsid="' + $ID + '"]' );
	}
}
