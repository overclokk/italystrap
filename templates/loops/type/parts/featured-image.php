<?php
/**
 * The template part for displaying Featured Image
 *
 * List of built in action:
 * do_action( 'begin_fetch_post_thumbnail_html', $post->ID, $post_thumbnail_id, $size );
 * do_action( 'end_fetch_post_thumbnail_html', $post->ID, $post_thumbnail_id, $size );
 *
 * List of built in filters:
 * $size = apply_filters( 'post_thumbnail_size', $size );
 * return apply_filters( 'post_thumbnail_html', $html, $post->ID, $post_thumbnail_id, $size, $attr );
 *
 * @uses has_post_thumbnail()
 * @uses the_post_thumbnail( $size, $attr )
 *
 * @see get_the_post_thumbnail() wp-includes/post-thumbnail-template.php
 *
 * @package ItalyStrap
 * @since 1.0.0
 * @since 4.0.0 Code refactoring.
 */

namespace ItalyStrap;

$context = null;

if ( empty( $post ) ) {
	global $post;
}

if ( has_post_thumbnail() ) { ?>
	<figure <?php Core\get_attr( 'featured_image', array( 'class' => 'featured-image' ), true ); ?>>
		<?php

		/**
		 * Filter the post thumbnail size with context
		 * You ca use the built in 'post_thumbnail_size' filter as well but without the context
		 *
		 * @param string $size    The size of the post_thumbnail. (Default: 'post-thumbnail')
		 * @param string $context The context in wich this function is called.
		 *
		 * @var string
		 */
		$size = apply_filters( 'italystrap_post_thumbnail_size', $this->theme_mod['post_thumbnail_size'], $context );

		/**
		 * Filter the post thumbnail attributes with context
		 *
		 * @param array  $attr    The attributes of the post_thumbnail.
		 * @param string $context The context in wich this function is called.
		 * @param string $size    The size of the post_thumbnail.
		 *
		 * @var array
		 */
		$attr = apply_filters( 'italystrap_post_thumbnail_attr',
			array(
				'class'		=> sprintf(
					'attachment-%1$s size-%1$s %1$s %2$s img-responsive',
					$size,
					$this->theme_mod['post_thumbnail_alignment']
				),
				'alt'		=> trim( strip_tags( get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true ) ) ),
				'itemprop'	=> 'image',
			),
			$context,
			$size
		);

		the_post_thumbnail( $size, $attr );
  		?>
	</figure>
<?php } else {

	/**
	 * This action fire in case no thumbnail is loaded
	 */
	do_action( 'italystrap_no_post_thumbnail' );
}
