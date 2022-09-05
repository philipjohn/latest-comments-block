<?php
/**
 * Plugin Name:       LL Latest Comments
 * Description:       Display recent comments from across your WordPress site and customise how it looks
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.2.0
 * Author:            Philip John
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       ll-latest-comments
 *
 * @package           ll-latest-comments
 */

/**
 * Get the post title.
 *
 * The post title is fetched and if it is blank then a default string is
 * returned.
 *
 * Copied from `wp-admin/includes/template.php`, but we can't include that
 * file because:
 *
 * 1. It causes bugs with test fixture generation and strange Docker 255 error
 *    codes.
 * 2. It's in the admin; ideally we *shouldn't* be including files from the
 *    admin for a block's output. It's a very small/simple function as well,
 *    so duplicating it isn't too terrible.
 *
 * @since 3.3.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string The post title if set; "(no title)" if no title is set.
 */
function ll_latest_comments_draft_or_post_title( $post = 0 ) {
	$title = get_the_title( $post );
	if ( empty( $title ) ) {
		$title = __( '(no title)' );
	}
	return $title;
}

/**
 * Renders the `lcm/ll-latest-comments` block on server.
 *
 * @param array $attributes The block attributes.
 *
 * @return string Returns the post content with latest comments added.
 */
function render_block_ll_latest_comments( $attributes = array() ) {
	$comments = get_comments(
		/** This filter is documented in wp-includes/widgets/class-wp-widget-recent-comments.php */
		apply_filters(
			'widget_comments_args',
			[
				'number'      => $attributes['commentsToShow'],
				'status'      => 'approve',
				'post_status' => 'publish',
			],
			[]
		)
	);

	$list_items_markup = '';
	$new_list_items_markup = '';
	if ( ! empty( $comments ) ) {

		foreach ( $comments as $comment ) {
			$new_list_items_markup .= '<li class="wp-block-ll-latest-comments__comment">';
			$new_list_items_markup .= '<article>';

			// `_draft_or_post_title` calls `esc_html()` so we don't need to wrap that call in
			// `esc_html`.
			$new_comment_text = '<span class="wp-block-ll-latest-comments__post-title"><a class="wp-block-ll-latest-comments__comment-link" href="%1$s"><blockquote>%2$s</blockquote></a></span>';

			$new_list_items_markup .= sprintf(
				$new_comment_text,
				esc_url( get_comment_link( $comment ) ),
				esc_html( wp_trim_words( get_comment_text( $comment ), $attributes['wordsToShow'] ) )
			);

			$new_list_items_markup .= '</article>';

		}

	}

	$classnames = array('has-excerpts');
	if ( empty( $comments ) ) {
		$classnames[] = 'no-comments';
	}
	$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classnames ) ) );

	return ! empty( $comments ) ? sprintf(
		'<ol %1$s>%2$s</ol>',
		$wrapper_attributes,
		$new_list_items_markup
	) : sprintf(
		'<div %1$s>%2$s</div>',
		$wrapper_attributes,
		__( 'No comments to show.' )
	);
}

/**
 * Registers the `lcm/ll-latest-comments` block.
 */
function register_block_ll_latest_comments() {
	register_block_type(
		__DIR__ . '/build',
		[ 'render_callback' => 'render_block_ll_latest_comments' ]
	);
}

add_action( 'init', 'register_block_ll_latest_comments' );
