<?php
class MH_Board_Recent_Widget extends WP_Widget {
	/**
	 * Constructor
	 *
	 * @return void
	 **/
	function MH_Board_Recent_Widget() {
		$widget_ops = array( 'classname' => 'mh_board_recent_wdiget', 'description' => __( 'Recent Board', 'mh_board' ) );
		$this->WP_Widget( 'mh_board_recent_wdiget', __( 'Recent Board', 'mh_board' ), $widget_ops );
		$this->alt_option_name = 'widget_mh_visit_us';

		add_action( 'save_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache' ) );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @param array An array of standard parameters for widgets in this theme
	 * @param array An array of settings for this widget instance
	 * @return void Echoes it's output
	 **/
	function widget( $args, $instance ) {
		$cache = wp_cache_get( 'mh_board_recent_wdiget', 'widget' );

		if ( !is_array( $cache ) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = null;

		if ( isset( $cache[$args['widget_id']] ) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract( $args, EXTR_SKIP );

		$title = $instance['title'] ? $instance['title'] : 'Recent Board';
		$count = $instance['count'] ? $instance['count'] : '5';
		echo $before_widget;
		echo $before_title;
		echo $title; // Can set this with a widget option, or omit altogether
		echo $after_title;
		$args= array (
			'post_type' => 'board',
			'post_status' => 'publish',
			'posts_per_page'=>5,
			'page'=>1,
			'orderby' =>'post_date',
			'order' => 'DESC'
		);
		global $wp_query;
		query_posts( 'posts_per_page=5&post_type=board' );
		$wp_query = new WP_Query($args);
		?>
		<ul class="recent_board clearfix">
			<?php if ( have_posts() ) : ?>
			<?php while ( have_posts() ) : the_post(); ?>
			<li><a href="<?php the_permalink();?>"><?php the_title(); ?></a></li>
			<?php endwhile; ?>
		<?php endif;?>
		</ul>
		<?php
		echo $after_widget;
		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'mh_board_recent_wdiget', $cache, 'widget' );
	}

	/**
	 * Deals with the settings when they are saved by the admin. Here is
	 * where any validation should be dealt with.
	 **/
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['count'] = strip_tags( $new_instance['count'] );
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset( $alloptions['mh_board_recent_wdiget'] ) )
			delete_option( 'mh_board_recent_wdiget' );

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'mh_board_recent_wdiget', 'widget' );
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 **/
	function form( $instance ) {
		$title = isset( $instance['title']) ? esc_attr( $instance['title'] ) : '최근 게시판 글';
		$count = isset( $instance['count']) ? esc_attr( $instance['count'] ) : '5';
?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'mh_board' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php _e( 'Count:', 'mh_board_recent_wdiget' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="text" value="<?php echo esc_attr( $count ); ?>" /></p>

		<?php
	}
}

add_action( 'widgets_init', 'mh_board_wdiget_init' );
if( ! function_exists( 'mh_board_wdiget_init' ) ):
function mh_board_wdiget_init(){
	register_widget( 'MH_Board_Recent_Widget' );
}
endif;

?>