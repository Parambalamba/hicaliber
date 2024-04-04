<?php
/**
 * Template Name: Page (Default)
 * Description: Page template with Sidebar on the left side.
 *
 */

get_header();

the_post();
?>
<div class="row">
	<div class="col-md-8 order-md-2 col-sm-12">
		<div id="post-<?php the_ID(); ?>" <?php post_class( 'content' ); ?>>
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php
				the_content();

				wp_link_pages(
					array(
						'before'   => '<nav class="page-links" aria-label="' . esc_attr__( 'Page', 'my-bootstrap' ) . '">',
						'after'    => '</nav>',
						'pagelink' => esc_html__( 'Page %', 'my-bootstrap' ),
					)
				);
				edit_post_link(
					esc_attr__( 'Edit', 'my-bootstrap' ),
					'<span class="edit-link">',
					'</span>'
				);
			?>
		</div><!-- /#post-<?php the_ID(); ?> -->
		<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
		?>
	</div><!-- /.col -->
	<?php
		//get_sidebar();
	?>
</div><!-- /.row -->

<?php $titles = get_field( 'titles' ); ?>

<?php
/**
 * Check if Title field in Posts Element filled then create
 * section for posts Element. It was made to simplify. Better way is
 * add checkbox to use or not use on this page Posts Element Section
 */
if ( $titles['title'] ) :
    $post_types = ['post'];
	$args = [
		'public'    => true,
		'_builtin'  => false
	];
	$output = 'names';
	$operator = 'and';
	$custom_post_types = get_post_types($args, $output, $operator);

	$select_categories = get_field( 'select_post_categories' );

    $post_status = get_field( 'display_featured_posts' );

	if ( $custom_post_types )
		$post_types = array_merge( $post_types, $custom_post_types );

    if ( $select_categories === 'all_categories' ) {
	    $block_posts = get_posts( [
		    'posts_per_page' => - 1,
		    'post_type'      => $post_types,
            'post_status'   => $post_status
	    ] );
    } else {
        $specific_categories = get_field( 'select_specific_categories' );
        $conditions = [];

        if ( $specific_categories ) {
            $tq = ['relation' => 'OR'];
            foreach ( $specific_categories as $term_id ) {
                $spec_term = get_term( $term_id );
                $sl = $spec_term->slug;
                $tax = $spec_term->taxonomy;
                $tq[] = [
                    'taxonomy' => $tax,
                    'field' => 'slug',
                    'terms' => [$sl]
                ];
            }
        }

	    $block_posts = get_posts( [
		    'posts_per_page' => - 1,
		    'post_type'      => $post_types,
		    'post_status'   => $post_status,
            'tax_query' => $tq
	    ] );
    }

?>
    <section <?= get_field( 'add_section_id' ) ? 'id="' . preg_replace( '/^(.+?),.+$/', '\\1', get_field( 'add_section_id' ) ) . '" ' : ''; ?> class="row <?= get_field( 'add_section_classes' ) ? preg_replace( '/([;.,!?:])/', ' ', get_field( 'add_section_classes' ) ) : ''; ?>">
        <h2 class="label text-center"><?= esc_html( $titles['title'] ); ?></h2>
        <?php if ( $titles['subtitle'] ) : ?>
            <h4 class="label text-center"><?= esc_html( $titles['subtitle'] ); ?></h4>
        <?php endif; ?>
        <?php if ( $block_posts ) : ?>
            <div class="posts <?= get_field( 'display_posts_per_row' ); ?>">
                <?php foreach ( $block_posts as $block_post ) : ?>
                    <?php setup_postdata( $block_post ); ?>
                    <div class="post-item <?= get_field( 'image_position' ); ?>">
                        <?= get_the_post_thumbnail($block_post, 'full'); ?>
                        <div class="post-item-info">
                            <a href="<?= get_the_permalink( $block_post ); ?>" class="link-dark"><?= get_the_title( $block_post ); ?></a>
                            <span><?= wp_trim_words( apply_filters( 'the_content', get_the_content( $block_post ) ), 12, ' ...' ); ?></span>
                        </div>

                    </div>
                <?php endforeach; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php
get_footer();
