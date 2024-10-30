<?php
class MHBoard{
	/*
	 * @public plugin_dir
	 */
	public $plugin_dir;
	/**
	 * @public board post type
	 */
	public $board_post_type;
	
	public $board_slug;
	var $board_type;
	var $current_board_id;
	var $current_board_cat;
	var $board_template;
	var $current_user_role = 'guest';
	
	public function MHBoard() {
		$this->__construct();
	}
	public function __construct(){
		$this->define_value();	
		$this->includes();
		$this->init();
		$this->current_user_role();
		
	}
	private function define_value(){
		$this->plugin_dir = dirname(__FILE__);
		$this->board_template = 'template';
		$this->board_post_type = apply_filters( 'mhb_board_post_type', 'board' );
		$this->board_slug = apply_filters( 'mhb_board_slug', 'board' );
	}
	private function includes(){
		
	}
	
	private function init(){
		register_activation_hook(__FILE__ , array(&$this, 'mh_pageview_install'));//페이지뷰 테이블 생성
		add_action('wp_ajax_nopriv_wpp_update', array(&$this, 'wpp_ajax_update'));
		add_action('wp_ajax_wpp_update', array(&$this, 'wpp_ajax_update'));
		add_action('mh_head', array(&$this, 'wpp_print_ajax'));
		//add_action('mhb_generate_rewrite_rules', array($this, 'generate_rewrite_rules'), 5 );
		
	}
	
	/**
	 * since 1.0
	 * 
	 */
	function mh_board_list(){
		global $wp_query;
		require_once(dirname(__FILE__).'/template/mh-board-list.php');
	}
	function mh_board_view(){
		require_once(dirname(__FILE__).'/template/mh-board-view.php');
	}
	function mh_board_write(){
		require_once(dirname(__FILE__).'/template/post-form.php');
	}
	function mh_board_sidebar(){
		require_once(dirname(__FILE__).'/template/mh-board-sidebar.php');
	}
	function mh_pageview_install(){
		global $wpdb;
		$sql = "";
		$charset_collate = "";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		if ( ! empty($wpdb->charset) ) $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( ! empty($wpdb->collate) ) $charset_collate .= " COLLATE $wpdb->collate";
		
		// set table name
		$table = $wpdb->prefix . "popularpostsdata";
		
		// does popularpostsdata table exists?
		if ( $wpdb->get_var("SHOW TABLES LIKE '$table'") != $table ) { // fresh setup
			// create tables popularpostsdata and popularpostsdatacache
			$sql = "CREATE TABLE " . $table . " ( UNIQUE KEY id (postid), postid int(10) NOT NULL, day datetime NOT NULL default '0000-00-00 00:00:00', last_viewed datetime NOT NULL default '0000-00-00 00:00:00', pageviews int(10) default 1 ) $charset_collate; CREATE TABLE " . $table ."cache ( UNIQUE KEY id (id, day), id int(10) NOT NULL, day datetime NOT NULL default '0000-00-00 00:00:00', pageviews int(10) default 1 ) $charset_collate;";
		} else {
			$cache = $table . "cache";
			if ( $wpdb->get_var("SHOW TABLES LIKE '$cache'") != $cache ) {
				// someone is upgrading from version 1.5.x
				$sql = "CREATE TABLE " . $table ."cache ( UNIQUE KEY id (id, day), id int(10) NOT NULL, day datetime NOT NULL, pageviews int(10) default 1 ) $charset_collate;";
			}
			
			$dateField = $wpdb->get_results("SHOW FIELDS FROM " . $table ."cache", ARRAY_A);
			if ($dateField[1]['Type'] != 'datetime') $wpdb->query("ALTER TABLE ". $table ."cache CHANGE day day datetime NOT NULL default '0000-00-00 00:00:00';");
		}
		
		dbDelta($sql);
	}
	//페이지뷰 정보 갱신
	function wpp_ajax_update() {
		$nonce = $_POST['token'];
		echo 'test';
		// is this a valid request?
		if (! wp_verify_nonce($nonce, 'wpp-token') ) die("Oops!");
		
		if (is_numeric($_POST['id']) && (intval($_POST['id']) == floatval($_POST['id'])) && ($_POST['id'] != '')) {
			$id = $_POST['id'];
		} else {
			die("Invalid ID");
		}		
		// if we got an ID, let's update the data table
					
		global $wpdb;
		
		$wpdb->show_errors();
		
		$table = $wpdb->prefix . 'popularpostsdata';
		
		// update popularpostsdata table
		$exists = $wpdb->get_results("SELECT postid FROM $table WHERE postid = '$id'");							
		if ($exists) {
			$result = $wpdb->query("UPDATE $table SET last_viewed = '".$this->now()."', pageviews = pageviews + 1 WHERE postid = '$id'");
		} else {
			$result = $wpdb->query("INSERT INTO $table (postid, day, last_viewed) VALUES ('".$id."', '".$this->now()."', '".$this->now()."')" );
		}
		
		// update popularpostsdatacache table
		$isincache = $wpdb->get_results("SELECT id FROM ".$table."cache WHERE id = '" . $id ."' AND day BETWEEN '".$this->curdate()." 00:00:00' AND '".$this->curdate()." 23:59:59';");
		if ($isincache) {
			$result2 = $wpdb->query("UPDATE ".$table."cache SET pageviews = pageviews + 1, day = '".$this->now()."' WHERE id = '". $id . "' AND day BETWEEN '".$this->curdate()." 00:00:00' AND '".$this->curdate()." 23:59:59';");
		} else {
			$result2 = $wpdb->query("INSERT INTO ".$table."cache (id, day) VALUES ('".$id."', '".$this->now()."')");
		}
		
		if (($result == 1) && ($result2 == 1)) {
			die("OK");
		} else {
			die($wpdb->print_error);
		}		
		
	}
	function wpp_print_ajax() {		
		// let's add jQuery
		wp_print_scripts('jquery');
			
		// create security token
		$nonce = wp_create_nonce('wpp-token');
		
		// get current post's ID
		global $wp_query;
		wp_reset_query();

		// if we're on a page or post, load the script
		if(is_single() || is_page()){
		if ( is_single() ) {
			$id = $wp_query->post->ID;
		}
		if(is_board_view()){
			$id = get_query_var('board_id');
		}
		if($id > 0):
		?>
<script type="text/javascript">
/* <![CDATA[ */				
jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {action: 'wpp_update', token: '<?php echo $nonce; ?>', id: <?php echo $id; ?>});
/* ]]> */
</script>
        <?php
        endif;
		}
	}
	function now() {		
		//return "'".current_time('mysql')."'";
		return current_time('mysql');
	}
	function curdate() {
		//return "'".gmdate( 'Y-m-d', ( time() + ( get_option( 'gmt_offset' ) * 3600 ) ))."'";
		return gmdate( 'Y-m-d', ( time() + ( get_option( 'gmt_offset' ) * 3600 ) ));
	}
	function get_count( $id ){
		global $wpdb;
		
		$table = $wpdb->prefix.'popularpostsdata';
		
		if($result = $wpdb->get_results("select pageviews from {$table} where postid = $id")){
			return $result[0]->pageviews;
		}else{
			return 0;
		}
	} 
	function get_board_types(){
		$data = get_terms('board_cat',array('hide_empty'=>0));
		$board_type = array();
		foreach($data as $d){
			$board_type[] = $d->slug;
		}
		return $board_type;
	}
	/* rewrite rule 
	public function generate_rewrite_rules( $wp_rewrite ){
		$mh_b_rules = array(
			// edit board
			$this->board_slug     . '/([^/]+)/edit/?$' => 'index.php?' . $this->board_post_type  . '=' . $wp_rewrite->preg_index( 1 ) . '&edit=1',
			// write board
			$this->board_slug     . '/([^/]+)/write/?$' => 'index.php?' . $this->board_post_type  . '=' . $wp_rewrite->preg_index( 1 ) . '&write=1',
		);

		$wp_rewrite->rules = array_merge( $mh_b_rules, $wp_rewrite->rules );

		return $wp_rewrite;
	}
	*/
	function current_board_cat(){
		$this->current_board_cat = get_current_mh_board_term();
		if(isset($this->current_board_cat->term_id)){
			$this->get_board_permission($this->current_board_cat->term_id);
		}
		
	}
	/**
	 * 글쓰기 퍼미션 
	 */
	function current_write_permission(){
		if(empty($this->current_board_cat)){
			$this->current_board_cat();
		}
		if(empty($this->current_board_permission[$this->current_user_role]['write']) || $this->current_board_permission[$this->current_user_role]['write'] == 'on'){
			return true;
		}
		return false;

	}

	/**
	 * 글보기 퍼미션
	 */
	function current_view_permission(){
		if(empty($this->current_board_cat)){
			$this->current_board_cat();
		}
		if(empty($this->current_board_permission[$this->current_user_role]['read']) || $this->current_board_permission[$this->current_user_role]['read'] == 'on'){
			return true;
		}
		return false;
	}

	/**
	 * 사용자 롤
	 */
	function current_user_role(){
		if(!function_exists('wp_get_current_user')){
			require_once(ABSPATH.'wp-includes/pluggable.php');
		}

		if(get_current_user_id()){

			$this->current_user_role = mh_get_user_role();
		}
	}
	/**
	 * 퍼미션 설정 가져오기
	 */
	function get_board_permission($term_id){
		if(empty($this->current_board_permission)){
			$this->current_board_permission = get_option('mh_board_permission_'.$term_id);
			
		}
	}

}
?>