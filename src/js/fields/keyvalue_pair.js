import WPOnion_Field from '../core/field';

class field extends WPOnion_Field {
	init() {
		this.global_validate = false;
		this.element.find( '.wponion-keyvalue_wrap' ).WPOnionCloner( {
			add_btn: this.element.find( '.wponion-fieldset > .wponion-keyvalue-action-container  > button[data-wponion-keyvalue-add]' ),
			limit: ( -1 === this.option( 'limit' ) ) ? null : this.option( 'limit' ),
			clone_elem: '> .wponion-fieldset > .wponion-keyvalue-field',
			remove_btn: '.wponion-keyvalue-field > button[data-wponion-keyvalue-remove]',
			template: this.option( 'html_template' ),
			templateAfterRender: ( $elem ) => {
				this.hook.doAction( 'wponion_key_value_updated', $elem );
				this.js_validate_elem( this.option( 'js_validate', false ), $elem.find( '> div:last-child' ) );
			},
			onRemove: ( $elem ) => {
				$elem.parent().remove();
				this.hook.doAction( 'wponion_key_value_updated', $elem );
			},
			onLimitReached: () => {
				if( this.element.find( 'div.alert' ).length === 0 ) {
					this.element.find( '.wponion-keyvalue_wrap' ).after( jQuery( this.option( 'error_msg' ) ).hide() );
					this.element.find( 'div.alert' ).slideDown();
					window.wponion_notice( this.element.find( 'div.alert, div.notice' ) );
				}
			}
		} );
	}

	js_error( err ) {
		err.error.appendTo( err.element.parent().parent() );
	}

	/**
	 *
	 * @param $args
	 * @param $elem
	 */
	js_validate_elem( $args, $elem ) {
		if( true !== window.wponion._.isUndefined( $args.key ) ) {
			$elem.find( '.wponion-keyvalue-field' ).each( function() {
				jQuery( this ).find( '> div' ).eq( 0 ).find( ':input' ).rules( 'add', $args.key );
			} );
		}
		if( true !== window.wponion._.isUndefined( $args.value ) ) {
			$elem.find( '.wponion-keyvalue-field' ).each( function() {
				jQuery( this ).find( '> div' ).eq( 1 ).find( ':input' ).rules( 'add', $args.value );
			} );
		}

		if( true === window.wponion._.isUndefined( $args.key ) && true === window.wponion._.isUndefined( $args.value ) ) {
			$elem.find( ':input' ).each( function() {
				jQuery( this ).rules( 'add', $args );
			} );
		}
	}
}

export default ( ( w ) => w.wponion_render_field( 'keyvalue_pair', ( $elem ) => new field( $elem ) ) )( window );