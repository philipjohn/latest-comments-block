/**
 * WordPress dependencies
 */
 import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
 import {
	 Disabled,
	 PanelBody,
	 RangeControl,
 } from '@wordpress/components';
 import ServerSideRender from '@wordpress/server-side-render';
 import { __ } from '@wordpress/i18n';

 /**
  * Minimum number of comments a user can show using this block.
  *
  * @type {number}
  */
 const MIN_COMMENTS = 1;
 /**
  * Maximum number of comments a user can show using this block.
  *
  * @type {number}
  */
 const MAX_COMMENTS = 100;

 /**
  * Minimum number of comments a user can show using this block.
  *
  * @type {number}
  */
 const MIN_WORDS = 5;
 /**
  * Maximum number of comments a user can show using this block.
  *
  * @type {number}
  */
 const MAX_WORDS = 100;

 export default function LLLatestComments( { attributes, setAttributes } ) {
	 const { commentsToShow, wordsToShow } = attributes;

	 return (
		 <div { ...useBlockProps() }>
			 <InspectorControls>
				 <PanelBody title={ __( 'Settings' ) }>
					 <RangeControl
						 label={ __( 'Number of comments' ) }
						 value={ commentsToShow }
						 onChange={ ( value ) =>
							 setAttributes( { commentsToShow: value } )
						 }
						 min={ MIN_COMMENTS }
						 max={ MAX_COMMENTS }
						 required
					 />
					 <RangeControl
						 label={ __( 'Number of words per comment' ) }
						 value={ wordsToShow }
						 onChange={ ( value ) =>
							 setAttributes( { wordsToShow: value } )
						 }
						 min={ MIN_WORDS }
						 max={ MAX_WORDS }
						 required
					 />
				 </PanelBody>
			 </InspectorControls>
			 <Disabled>
				 <ServerSideRender
					 block="lcm/ll-latest-comments"
					 attributes={ attributes }
					 // The preview uses the site's locale to make it more true to how
					 // the block appears on the frontend. Setting the locale
					 // explicitly prevents any middleware from setting it to 'user'.
					 urlQueryArgs={ { _locale: 'site' } }
				 />
			 </Disabled>
		 </div>
	 );
 }
