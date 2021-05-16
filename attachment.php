<?php
/**
 * The template for displaying full-size images
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package charmer
 */

include(plugin_dir_path(__FILE__).'header-attachment.php');
?>
	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			include(plugin_dir_path(__FILE__).'template-parts/content-attachment.php');

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->
</div><!-- #page -->

<?php wp_footer(); ?>


</body>
</html>