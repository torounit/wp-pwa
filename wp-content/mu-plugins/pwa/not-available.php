<?php get_header();?>

	<div class="wrap">
		<div id="primary" class="content-area">
			<main id="main" class="site-main" role="main">

				<section class="error-404 not-found error-not-available">
					<header class="page-header">
						<h1 class="page-title"><?php _e( 'Oops! That page is not available.', 'twentyseventeen' ); ?></h1>
					</header><!-- .page-header -->
					<div class="page-content">
						<p><?php _e( 'It looks like not available on offline mode' ); ?></p>

					</div><!-- .page-content -->
				</section><!-- .error-404 -->
			</main><!-- #main -->
		</div><!-- #primary -->
	</div><!-- .wrap -->


<?php get_footer();?>