<?php 
	/*
	Plugin Name: Author Manager
	Description: View author post statistics by date.
	Version: 1.0
	Author: Peter Mandell
	*/

	add_action('admin_init', 'author_manager_admin_init' );
	add_action('admin_menu', 'author_manager_admin_menu' );
 
    function author_manager_admin_init() {
    	wp_register_style('jqueryui-css', plugins_url('resources/jqueryui.css', __FILE__));
    	wp_register_style('am-styles', plugins_url('resources/am.css', __FILE__));
    	wp_register_style('tablesorter-css', plugins_url('resources/tablesorter.css', __FILE__));
    	wp_register_script('am-js', plugins_url('resources/am.js', __FILE__));
        wp_register_script('tablesorter-js', plugins_url('resources/tablesorter.js', __FILE__));

    }
   
    function author_manager_admin_menu() {
        $page = add_submenu_page('users.php', 
                                 __( 'Author Manager', 'author-manager' ), 
                                 __( 'Author Manager', 'author-manager' ),
                                 'administrator',
                                 __FILE__, 
                                 'author_manager');
  
    	add_action('admin_print_styles-' . $page, 'get_am_styles' );
    	add_action('admin_print_scripts-' . $page, 'get_am_scripts');
    }
   
    function get_am_styles() {
    	wp_enqueue_style('am-styles');
    	wp_enqueue_style('jqueryui-css');
    	wp_enqueue_style('tablesorter-css');
    }

    function get_am_scripts() {       
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('am-js');
        wp_enqueue_script('tablesorter-js');        
    }   
   
    function author_manager(){ 
    	$searchMethod = 'am-week-filter';
    	if (isset($_POST['am-filter-type'])){
    		$searchMethod = $_POST['am-filter-type'];
    		if ($searchMethod == 'am-week-filter'){
    			$weekString = $_POST['am-week'];
    			$splitWeek = explode(' - ', $weekString);
    			
    			$authors = get_posts_by_author($splitWeek);  		
    		}else{
    			$splitWeek = array();
    			$splitWeek[0] = $_POST['am-start-date'];
    			$splitWeek[1] = $_POST['am-end-date'];

    			$authors = get_posts_by_author($splitWeek); 
    		}
    	}

    	$dates = get_post_date_range();
    	
    	$firstDate = strtotime($dates->firstDate);
    	$lastDate = strtotime($dates->lastDate);

    	function dayofweek(){
        	$days = array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");
        	return array_search(date("D"), $days) + 1;
		}

    	$current_date = time();
		$current_day = dayofweek();
		$last_monday = mktime(0,0,0, date("m", $current_date), date("d", $current_date)-($current_day-1), date("Y", $current_date));

		$day = $last_monday;

		$weeks = array();
		while ($day > $firstDate){
			$prevWeek = strtotime ( '-7 days' , $day);
			array_push($weeks, date('m/d/y', $prevWeek) . ' - ' . date('m/d/y', strtotime ( '-1 days' , $day)));
			$day = $prevWeek;
		}
    	
    ?>
	<div class='wrap'>
		<div id="icon-users" class="icon32"><br></div>
		<h2>Author Manager</h2>		

<div id="poststuff" class="metabox-holder has-right-sidebar">
	<div id="side-info-column" class="inner-sidebar">
		<div id="side-sortables" class="meta-box-sortables ui-sortable"><div id="gpaisrpro_about" class="postbox">
			<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle"><span>About</span></h3>
				<div class="inside">
					<p><b>Peter Mandell</b> (developer)</p>
					<p>I am a young web developer currently based out of Philadelphia, PA. My skills include PHP, ColdFusion, MySQL, jQuery, and more.</p>
					<p><a href='http://www.petergmandell.com' target='_blank'>Visit my site</a></p>
				</div>
			</div>
			<div id="gpaisrpro_social" class="postbox ">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle"><span>Donate</span></h3>
				<div class="inside" style='text-align:center;'>
					<p>If you found this plugin useful, please donate!</p>
					<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
						<input type="hidden" name="cmd" value="_s-xclick">
						<input type="hidden" name="hosted_button_id" value="6C326SYQ6XA7L">
						<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
						<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
					</form>	
				</div>
			</div>
		</div>
	</div>
	<div id="post-body" class="has-sidebar">
		<div id="post-body-content" class="has-sidebar-content">
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div id="gpaisrpro_general" class="postbox ">
					<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>General</span></h3>
					<div class="inside">
						<p>This plugin will let you search all published posts and display a list of authors who posted during a given time frame. Choose a week from the drop-down menu, or input a specific date range.</p>
						</p>
					</div>
				</div>
			</div>
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div id="gpaisrpro_general" class="postbox ">
					<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Search Posts</span></h3>
					<div class="inside">
						<p>Search by:</p>
						<form method='post' id='am-form'>
							<table>
								<tr>
									<th>Week<br/><input type='radio' name='am-filter-type' id='am-filter-type-week' <?php if ($searchMethod == 'am-week-filter'){ echo "checked";}?> value='am-week-filter'/></th>
									<th>&nbsp;</th>
									<th>Dates<br/><input type='radio' name='am-filter-type' id='am-filter-type-date' <?php if ($searchMethod == 'am-date-filter'){ echo "checked";}?> value='am-date-filter'/></th>
								</tr>
								<tr>
									<td>
										<table>
											<tr>
												<td>
													<select name='am-week' id='am-week'>
														<?php foreach ($weeks as $week){ ?>
														<option value='<?php echo $week;?>'<?php if (isset($_POST['am-filter-type']) && $_POST['am-filter-type'] == 'am-week-filter'){if ($week == $_POST['am-week']){echo 'selected';}}?>
								><?php echo $week;?></option>
														<?php } ?>
													</select>
												</td>
											</tr>							
										</table>
									</td>
									<td>------ OR ------</td>
									<td>
										<table>
											<tr>
												<th>Start Date:</th>
												<td>
													<input type='text' class='am-date' id='am-start-date' name='am-start-date' value='<?php if (isset($_POST['am-start-date'])){echo $_POST['am-start-date'];}?>'/>
												</td>
											</tr>	
											<tr>
												<th>End Date:</th>
												<td>
													<input type='text' class='am-date' id='am-end-date' name='am-end-date' value='<?php if (isset($_POST['am-start-date'])){echo $_POST['am-end-date'];}?>'/>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><button class='am-search button-primary'>Search</button></td>
									<td>&nbsp;</td>
								</tr>
							</table>

						</form>
					</div>
				</div>
			</div>
			<?php if ($_POST){ ?>
			<div id="normal-sortables" class="meta-box-sortables ui-sortable">
				<div id="gpaisrpro_general" class="postbox ">
					<div class="handlediv" title="Click to toggle"><br></div><h3 class="hndle"><span>Results</span></h3>
					<div class="inside">
						<div class='am-results'>
							<?php if (isset($authors) && $authors){ ?>
							<table id='am-results-table' class='tablesorter-default'>
								<thead>
								<tr>
									<th style='width:170px;'>Name</th>
									<th style='width:80px;'># of Posts</th>
									<th data-sorter='false'>Show Posts</th>
								</tr>
								</thead>
								<tbody>
								<?php foreach ($authors as $author){?>
								<tr>
									<td><?php echo $author['name'];?></td>
									<td><?php echo $author['count'];?></td>
									<td>
										<a href='#' class='am-show-author-posts'>Show</a>
										<ul class='author-posts'>
											<?php foreach($author['posts'] as $authorPost){ ?>
												<li><a href='<?php echo get_permalink($authorPost['postID']);?>' target='_blank'><?php echo $authorPost['title'];?></a></li>						
											<?php } ?>
										</ul>
									</td>
								</tr>
								<?php } ?>
								</tbody>
							</table>
							<?php }else{ ?>
								<h4>No results founds.</h4>
							<?php } ?>
						</div>						
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</div>
<br class="clear">
<input type="hidden" name="option_page" value="gpaisrpro_options_group"><input type="hidden" name="action" value="update"><input type="hidden" id="_wpnonce" name="_wpnonce" value="96a4136c2b"><input type="hidden" name="_wp_http_referer" value="/wp-admin/options-general.php?page=gpaisrpro"><input type="hidden" id="closedpostboxesnonce" name="closedpostboxesnonce" value="d782824df1"><input type="hidden" id="meta-box-order-nonce" name="meta-box-order-nonce" value="73fcb82cf8">				<input type="hidden" value="1171f0681c" name="gpaisrpro-options-ajax-nonce" id="gpaisrpro-options-ajax-nonce">
</div>



 	</div>   

   <?php }

	function get_post_date_range(){
		global $wpdb;
		$wpdb->query("SELECT 
					(SELECT post_date FROM $wpdb->posts WHERE post_type = 'post' ORDER BY post_date LIMIT 1) AS firstDate, 
					(SELECT post_date FROM $wpdb->posts WHERE post_type = 'post' ORDER BY post_date DESC LIMIT 1) AS lastDate");
		return $wpdb->last_result[0];
	}

	function get_posts_by_author($splitWeek){
		global $wpdb;

		$start = date("Y-m-d H:i:s", strtotime(trim($splitWeek[0])));
		$end = date("Y-m-d H:i:s", strtotime(trim($splitWeek[1])));
		$end = date('Y-m-d H:i:s', strtotime($end) + 86399);

		$wpdb->query("SELECT p.post_title as title, p.ID as postID, p.post_author as authorID, u.display_name as name
			          FROM $wpdb->posts p
			          JOIN $wpdb->users u
			          ON p.post_author = u.id
			          WHERE post_type = 'post' 
			          AND post_status = 'publish' 
			          AND post_date >= '$start' 
			          AND post_date <= '$end' 
			          ORDER BY post_date ASC");

		$results = $wpdb->last_result;
		$authorPosts = array();

		for ($i=0; $i<count($wpdb->last_result); $i++){
			if (!array_key_exists($results[$i]->authorID, $authorPosts)){
				$authorPosts[$results[$i]->authorID] = array('name' => $results[$i]->name, 'posts' => array(), 'count' => 0);
			}
			array_push($authorPosts[$results[$i]->authorID]['posts'], array('title'=>$results[$i]->title, 'postID' => $results[$i]->postID));
			$authorPosts[$results[$i]->authorID]['count'] = $authorPosts[$results[$i]->authorID]['count'] + 1;
			
		}
		return $authorPosts;

	}









?>