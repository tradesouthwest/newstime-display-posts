<?php
/**
 * Plugin Name: NewsTime Display Posts Shortcode
 * Description: Shortcode to display posts inside page content for ClassicPress.
 * Version:     1.0.0
 * Requires PHP: 7.4
 * Requires CP:  2.4
 * Author:      NewsTime by Tradesouthwest
 * License:     GPLv2 or later
 * Text Domain: newstime
 * -----------------------------------------------------------------------------
 * This is free software released under the terms of the General Public License,
 * version 2, or later. It is distributed WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. Full
 * text of the license is available at https://www.gnu.org/licenses/gpl-2.0.txt.
 * -----------------------------------------------------------------------------
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

function newstime_display_posts_styles() {

	 wp_enqueue_style( 'onlist-style',   plugin_dir_url(__FILE__)
                      . '/css/newstime-display-posts-style.css',array(), 
					  '1.0', false );
}
//load language scripts
function newstime_display_posts_load_text_domain()
{
    load_plugin_textdomain( 'newstime-display-posts', false,
                            basename( dirname( __FILE__ ) ) . '/languages' );
}

//activate plugin
function newstime_display_posts_plugin_reactivate()
{
        return false;
}
//deactivation settings
function newstime_display_posts_plugin_deactivate()
{
		return false;
}
    //ready, set, go
    register_activation_hook(__FILE__,   'newstime_display_posts_plugin_activate');
    register_deactivation_hook(__FILE__, 'newstime_display_posts_plugin_deactivate');


/**
 * Register [newstime_posts] Shortcode
 *
 * Usage: [newstime_posts posts_per_page="4" category="news"]
 *
 * @param array $atts Shortcode attributes.
 * @return string HTML output.
 */
function newstime_posts_shortcode( $atts ) {
	$atts = shortcode_atts(
		array(
			'posts_per_page' => 4,
			'offset'         => 0,
			'category'       => '',
			'orderby'        => 'date',
			'order'          => 'DESC',
		),
		$atts,
		'newstime_posts'
	);

	$query_args = array(
		'post_type'           => 'post',
		'posts_per_page'      => intval( $atts['posts_per_page'] ),
		'offset'              => intval( $atts['offset'] ),
		'orderby'             => sanitize_text_field( $atts['orderby'] ),
		'order'               => sanitize_text_field( $atts['order'] ),
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
	);

	if ( ! empty( $atts['category'] ) ) {
		$query_args['category_name'] = sanitize_text_field( $atts['category'] );
	}

	$custom_query = new WP_Query( $query_args );

	// Use Output Buffering so posts render in-place within the Classic Editor text
	ob_start();

	if ( $custom_query->have_posts() ) :
		?>
		<div class="newstime-shortcode-grid">
			<?php
			while ( $custom_query->have_posts() ) :
				$custom_query->the_post();
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class( 'shortcode-post-card' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="shortcode-card-image">
							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'medium' ); ?>
							</a>
						</div>
					<?php endif; ?>

					<div class="shortcode-card-content">
						<h3 class="shortcode-card-title">
							<a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a>
						</h3>
						<span class="shortcode-card-date"><?php echo esc_html( get_the_date() ); ?></span>
					</div>
				</article>
				<?php
			endwhile;
			wp_reset_postdata(); // Cleans up global $post state
			?>
		</div>
		<?php
	endif;

	return ob_get_clean();
}
add_shortcode( 'newstime_posts', 'newstime_posts_shortcode' );

?>
