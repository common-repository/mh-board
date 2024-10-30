<?php
/**
 * MH Board 고유주소 설정
 * version 0.9.4
 * date 12.08.28
 * author MinHyeong Lim
 */
add_action('admin_init','mh_board_permalink_options');
function mh_board_permalink_options(){
	register_setting( 'mh-board-permalink-options', 'mh_board_permalink' );
}
function mh_board_permalink(){
	global $wp_rewrite;

?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br></div><h2><?php echo __('MH Board Permalink','mhboard');?></h2>
	<p class="ssamture_net" style="text-align:right">
	<a href="http://ssamture.net"><img src="http://ssamture.net/ssamturenet.png" border="0"></a>
	</p>
	<form method="post" action="options.php">
		<?php settings_fields( 'mh-board-permalink-options' ); ?>
		<?php $mh_board_permalink = get_option('mh_board_permalink');
		$home_url = home_url();
		MH_Custom_Post_Type_Permalinks::add_tax_rewrite();
		MH_Custom_Post_Type_Permalinks::set_archive_rewrite();
		MH_Custom_Post_Type_Permalinks::set_rewrite();

		//flush_rewrite_rules();
		
		

		?>
		<table class="form-table">
		<tbody>
			<tr>
				<th><label><input name="mh_board_permalink" type="radio" value="/%year%/%monthnum%/%day%/%postname%/" class="tog"<?php if($mh_board_permalink == '/%year%/%monthnum%/%day%/%postname%/'){echo " checked";}?>> Day and name</label></th>
				<td><code><?php echo $home_url;?>/board/2012/08/28/sample-post/</code></td>
			</tr>
			<tr>
				<th><label><input name="mh_board_permalink" type="radio" value="/%year%/%monthnum%/%postname%/" class="tog"<?php if($mh_board_permalink == '/%year%/%monthnum%/%postname%/'){echo " checked";}?>> Month and name</label></th>
				<td><code><?php echo $home_url;?>/board/2012/08/sample-post/</code></td>
			</tr>
			<tr>
				<th><label><input name="mh_board_permalink" type="radio" value="%post_id%" class="tog"<?php if($mh_board_permalink == '%post_id%'){echo " checked";}?>> Numeric</label></th>
				<td><code><?php echo $home_url;?>/board/123</code></td>
			</tr>
			<tr>
				<th><label><input name="mh_board_permalink" type="radio" value="/%postname%/" class="tog"<?php if($mh_board_permalink == '/%postname%/'){echo " checked";}?>> Post name</label></th>
				<td><code><?php echo $home_url;?>/board/sample-post/</code></td>
			</tr>
		</tbody>
		</table>
		<?php submit_button();?>
	</form>
</div>
<?php
}
class MH_Custom_Post_Type_Permalinks {

	static public $default_structure = '/%postname%/';

	public function  __construct () {
		add_action('wp_loaded',array(&$this,'set_archive_rewrite'),99);
		add_action('wp_loaded', array(&$this,'set_rewrite'),100);
		add_action('wp_loaded', array(&$this,'add_tax_rewrite'));

		if(get_option("permalink_structure") != "") {
			add_filter('post_type_link', array(&$this,'set_permalink'),10,3);
			add_filter('getarchives_where', array(&$this,'get_archives_where'), 10, 2);
			add_filter('get_archives_link', array(&$this,'get_archives_link'),20,1);
			add_filter('term_link', array(&$this,'set_term_link'),10,3);
		}
	}

	/**
	 *
	 * Get Custom Taxonomies parents.
	 *
	 */
	private function get_taxonomy_parents( $id, $taxonomy = 'category', $link = false, $separator = '/', $nicename = false, $visited = array() ) {
		$chain = '';
		$parent = &get_term( $id, $taxonomy, OBJECT, 'raw');
		if ( is_wp_error( $parent ) ) {
			return $parent;
		}

		if ( $nicename ){
			$name = $parent->slug;
		}else {
			$name = $parent->name;
		}

		if ( $parent->parent && ( $parent->parent != $parent->term_id ) && !in_array( $parent->parent, $visited ) ) {
			$visited[] = $parent->parent;
			$chain .= $this->get_taxonomy_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
		}

		if ( $link ) {
			$chain .= '<a href="' . get_term_link( $parent->term_id, $taxonomy ) . '" title="' . esc_attr( sprintf( __( "View all posts in %s" ), $parent->name ) ) . '">'.$name.'</a>' . $separator;
		}else {
			$chain .= $name.$separator;
		}
		return $chain;
	}

	/**
	 *
	 * Add rewrite rules for archives.
	 *
	 */
	public function set_archive_rewrite() {
		$post_types = array('board');

		foreach ( $post_types as $post_type ):
			if( !$post_type )
				continue;
			$permalink = get_option( 'mh_board_permalink' );
			$post_type_obj = get_post_type_object($post_type);
			$slug = $post_type_obj->rewrite['slug'];
			if( !$slug )
				$slug = $post_type;

			if( is_string( $post_type_obj->has_archive ) ){
				$slug = $post_type_obj->has_archive;
			};

			add_rewrite_rule( $slug.'/date/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$', 'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/date/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$', 'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&feed=$matches[4]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/date/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/page/?([0-9]{1,})/?$', 'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&paged=$matches[4]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/date/([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})/?$', 'index.php?year=$matches[1]&monthnum=$matches[2]&day=$matches[3]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/date/([0-9]{4})/([0-9]{1,2})/feed/(feed|rdf|rss|rss2|atom)/?$', 'index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/date/([0-9]{4})/([0-9]{1,2})/(feed|rdf|rss|rss2|atom)/?$', 'index.php?year=$matches[1]&monthnum=$matches[2]&feed=$matches[3]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/date/([0-9]{4})/([0-9]{1,2})/page/?([0-9]{1,})/?$', 'index.php?year=$matches[1]&monthnum=$matches[2]&paged=$matches[3]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/date/([0-9]{4})/([0-9]{1,2})/?$', 'index.php?year=$matches[1]&monthnum=$matches[2]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/date/([0-9]{4})/feed/(feed|rdf|rss|rss2|atom)/?$', 'index.php?year=$matches[1]&feed=$matches[2]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/date/([0-9]{4})/(feed|rdf|rss|rss2|atom)/?$', 'index.php?year=$matches[1]&feed=$matches[2]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/date/([0-9]{4})/page/?([0-9]{1,})/?$', 'index.php?year=$matches[1]&paged=$matches[2]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/date/([0-9]{4})/?$', 'index.php?year=$matches[1]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/author/([^/]+)/?$', 'index.php?author=$matches[1]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/page/?([0-9]{1,})/?$', 'index.php?paged=$matches[1]&post_type='.$post_type, 'top' );
			add_rewrite_rule( $slug.'/?$', 'index.php?post_type='.$post_type, 'top' );


		endforeach;
	}


	/**
	 *
	 * Add Rewrite rule for single posts.
	 *
	 */
	public function set_rewrite() {
		global $wp_rewrite;

		$post_types = array('board');
		foreach ( $post_types as $post_type ):
			$permalink = get_option( 'mh_board_permalink' );

			if( !$permalink )
				$permalink = self::$default_structure;

			$permalink = str_replace( '%postname%', '%'.$post_type.'%', $permalink );
			$permalink = str_replace( '%post_id%', '%'.$post_type.'_id%', $permalink );

			$slug = get_post_type_object($post_type)->rewrite['slug'];

			if( !$slug )
				$slug = $post_type;

			$permalink = '/'.$slug.'/'.$permalink;
			$permalink = $permalink.'/%'.$post_type.'_page%';
			$permalink = str_replace( '//', '/', $permalink );

			$wp_rewrite->add_rewrite_tag( '%post_type%', '([^/]+)', 'post_type=' );
			$wp_rewrite->add_rewrite_tag( '%'.$post_type.'_id%', '([0-9]{1,})','post_type='.$post_type.'&p=' );
			$wp_rewrite->add_rewrite_tag( '%'.$post_type.'_page%', '([0-9]{1,}?)',"page=" );

			$wp_rewrite->generate_rewrite_rules( $permalink, EP_NONE, true, true, true,true);
			$wp_rewrite->add_permastruct( $post_type, $permalink, false );
		endforeach;

		$taxonomies = get_taxonomies( array("show_ui" => true, "_builtin" => false), 'objects' );
		foreach ( $taxonomies as $taxonomy => $objects ):
			$wp_rewrite->add_rewrite_tag( "%$taxonomy%", '(.+?)', "$taxonomy=" );
		endforeach;

		$wp_rewrite->use_verbose_page_rules = true;
	}

	/**
	 *
	 * Fix permalinks output.
	 *
	 */
	public function set_permalink( $post_link, $post,$leavename ) {
		global $wp_rewrite;

		
		$draft_or_pending = isset( $post->post_status ) && in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) );
		if( $draft_or_pending and !$leavename )
			return $post_link;

		$post_type = $post->post_type;
		$permalink = $wp_rewrite->get_extra_permastruct( $post_type );

		$permalink = str_replace( '%post_type%', get_post_type_object($post->post_type)->rewrite['slug'], $permalink );
		$permalink = str_replace( '%'.$post_type.'_id%', $post->ID, $permalink );
		$permalink = str_replace( '%'.$post_type.'_page%', "", $permalink );
		$permalink = str_replace( '%'.$post_type.'_cpage%', "", $permalink );

		$parentsDirs = "";
		$postId = $post->ID;
		while ($parent = get_post($postId)->post_parent) {
			$parentsDirs = get_post($parent)->post_name."/".$parentsDirs;
			$postId = $parent;
		}

		$permalink = str_replace( '%'.$post_type.'%', $parentsDirs.'%'.$post_type.'%', $permalink );

		if( !$leavename ){
			$permalink = str_replace( '%'.$post_type.'%', $post->post_name, $permalink );
		}

		$taxonomies = get_taxonomies( array('show_ui' => true),'objects' );

		foreach ( $taxonomies as $taxonomy => $objects ) {
			if ( strpos($permalink, "%$taxonomy%") !== false ) {
				$terms = get_the_terms( $post->ID, $taxonomy );

				if ( $terms ) {
					usort($terms, '_usort_terms_by_ID'); // order by ID
					$term = $terms[0]->slug;

					if ( $parent = $terms[0]->parent )
						$term = $this->get_taxonomy_parents( $parent,$taxonomy, false, '/', true ) . $term;
				}

				if( isset($term) ) {
					$permalink = str_replace( "%$taxonomy%", $term, $permalink );
				}
			}
		}

		$user = get_userdata( $post->post_author );
		$permalink = str_replace( "%author%", $user->user_nicename, $permalink );

		$post_date = strtotime( $post->post_date );
		$permalink = str_replace( "%year%", 	date("Y",$post_date), $permalink );
		$permalink = str_replace( "%monthnum%", date("m",$post_date), $permalink );
		$permalink = str_replace( "%day%", 		date("d",$post_date), $permalink );
		$permalink = str_replace( "%hour%", 	date("H",$post_date), $permalink );
		$permalink = str_replace( "%minute%", 	date("i",$post_date), $permalink );
		$permalink = str_replace( "%second%", 	date("s",$post_date), $permalink );

		$permalink = str_replace('//', "/", $permalink );

		$permalink = home_url( user_trailingslashit( $permalink ) );
		$str = rtrim( preg_replace("/%[a-z,_]*%/","",get_option("permalink_structure")) ,'/');
		return $permalink = str_replace($str, "", $permalink );

	}

	/**
	 *
	 * wp_get_archives fix for custom post
	 * Ex:wp_get_archives('&post_type='.get_query_var( 'post_type' ));
	 *
	 */

	public $get_archives_where_r;

	public function get_archives_where( $where, $r ) {
		$this->get_archives_where_r = $r;
		if ( isset($r['post_type']) )
			$where = str_replace( '\'post\'', '\'' . $r['post_type'] . '\'', $where );

		return $where;
	}

	public function get_archives_link( $link ) {
		if (isset($this->get_archives_where_r['post_type'])  and  $this->get_archives_where_r['type'] != 'postbypost'){
			$blog_url = get_bloginfo("url");

			// /archive/%post_id%
			if($str = rtrim( preg_replace("/%[a-z,_]*%/","",get_option("permalink_structure")) ,'/')) {
				$ret_link = str_replace($str, '/'.'%link_dir%', $link);
			}else{
				$blog_url = rtrim($blog_url,"/");
				$ret_link = str_replace($blog_url,$blog_url.'/'.'%link_dir%',$link);
			}
			$link_dir = $this->get_archives_where_r['post_type'];

			if(!strstr($link,'/date/')){
				$link_dir = $link_dir .'/date';
			}

			$ret_link = str_replace('%link_dir%',$link_dir,$ret_link);

			return $ret_link;
		}
		return $link;
	}

	/**
	 *
	 * Add rewrite rules for custom taxonomies.
	 * @since 0.6
	 *
	 */
	public function add_tax_rewrite() {
		if(get_option('no_taxonomy_structure'))
			return false;

		global $wp_rewrite;
		$taxonomies = get_taxonomies(array( '_builtin' => false));
		if(empty($taxonomies))
			return false;

		foreach ($taxonomies as $taxonomy) :
			//$post_types = get_taxonomy($taxonomy)->object_type;
			$post_types = array('board');
			//print_r($post_types);

			foreach ($post_types as $post_type):
				$post_type_obj = get_post_type_object($post_type);
				$slug = $post_type_obj->rewrite['slug'];
				if(!$slug) {
					$slug = $post_type;
				}

				if(is_string($post_type_obj->has_archive)){
					$slug = $post_type_obj->has_archive;
				};

				//add taxonomy slug
				add_rewrite_rule( $slug.'/'.$taxonomy.'/(.+?)/page/?([0-9]{1,})/?$', 'index.php?'.$taxonomy.'=$matches[1]&paged=$matches[2]', 'top' );
				add_rewrite_rule( $slug.'/'.$taxonomy.'/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$', 'index.php?'.$taxonomy.'=$matches[1]&feed=$matches[2]', 'top' );
				add_rewrite_rule( $slug.'/'.$taxonomy.'/(.+?)/(feed|rdf|rss|rss2|atom)/?$', 'index.php?'.$taxonomy.'=$matches[1]&feed=$matches[2]', 'top' );
				add_rewrite_rule( $slug.'/'.$taxonomy.'/(.+?)/?$', 'index.php?'.$taxonomy.'=$matches[1]', 'top' );

			endforeach;
		endforeach;
	}


	/**
	 *
	 * Fix taxonomy link outputs.
	 * @since 0.6
	 *
	 */
	public function set_term_link( $termlink, $term, $taxonomy ) {
		if( get_option('no_taxonomy_structure') )
			return  $termlink;

		$taxonomy = get_taxonomy($taxonomy);
		if( $taxonomy->_builtin )
			return $termlink;

		if( empty($taxonomy) )
			return $termlink;

		$wp_home = rtrim( get_option('home'), '/' );

		$post_type = $taxonomy->object_type[0];
		$slug = get_post_type_object($post_type)->rewrite['slug'];


		//$termlink = str_replace( $term->slug.'/', $this->get_taxonomy_parents( $term->term_id,$taxonomy->name, false, '/', true ), $termlink );
		$termlink = str_replace( $wp_home, $wp_home.'/'.$slug, $termlink );
		$str = rtrim( preg_replace("/%[a-z_]*%/","",get_option("permalink_structure")) ,'/');
		return str_replace($str, "", $termlink );

	}
}
new MH_Custom_Post_Type_Permalinks;
?>