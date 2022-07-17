<?php

add_filter( 'the_editor', 'kubio_maintainable_page_editor' );

function kubio_maintainable_page_editor( $editor ) {
	global $post;

	if ( ! property_exists( 'post_type', $post ) || ( property_exists( 'post_type', $post ) && 'page' !== $post->post_type ) ) {
		return $editor;
	}
	?>
	<style>
		div#wp-content-editor-container {
			position: relative;
		}

		div#wp-content-editor-container textarea {
			max-height: 500px;
			height: 500px;
		}

		div#wp-content-editor-tools {
			display: none;
		}

		table#post-status-info {
			display: none;
		}

		.kubio-classic-editor-overlay {
			position: absolute;
			top: 0;
			right: 0;
			bottom: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: #ececec;
			border: 1px solid #cacaca;
		}

		div#wp-content-wrap {
			margin-top: 1rem;
			margin-bottom: 1rem;
			min-height: 300px;
		}

		.kubio-classic-editor-overlay .middle-align {
			position: relative;
			top: 50%;
			transform: translateY(-50%);
			text-align: center;
		}

		.kubio-classic-editor-overlay i.dashicons.dashicons-edit {
			font-size: 1.8em;
			width: auto;
			margin-right: 2px;
			vertical-align: middle;
			height: 30px;
		}
		.kubio-classic-editor-overlay .button.button-link,
		.kubio-classic-editor-overlay .button.button-link:hover,
		.kubio-classic-editor-overlay .button.button-link:focus {
			background: transparent;
			outline: none;
			box-shadow: none;
		}

	</style>

	<script>
		window.cp_open_page_in_kubio = function (postId, postType) {
			var link = '<?php echo admin_url( 'admin.php?page=kubio' ); ?>&postId='+postId+'&postType='+postType;
			return link;
		}
	</script>

	<div class="kubio-classic-editor-overlay">
		<div class="middle-align">
			<div>
				<button onclick="window.location.replace(cp_open_page_in_kubio('<?php echo $post->ID; ?>', '<?php echo get_post_type( $post->ID ); ?>')); return false;" class="button button-hero button-primary">
					<i class="dashicons dashicons-edit"></i>
					<?php _e( 'Edit in Kubio', 'kubio' ); ?>
				</button>
			</div>
		</div>
	</div>
		<?php
}
