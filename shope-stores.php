<?php 
	/*
	Plugin Name: Shope Stores
	Plugin URI:  http://nascenture.com/
	Description: Locations of stores.
	Version: 1.0
	Author: Ravikant
	Author URI:  http://nascenture.com/
	License: GPL
	*/

	ob_start();
	define("PAGELIMIT", 10);  
 
	function ns_shope_store_menu() {

		add_menu_page('Shope Store', 'Shope Store', 'manage_options', 'stores', 'ns_stores');
		
		add_submenu_page( 'stores', 'Add New', 'Add New', 'manage_options', 'add-new-store', 'ns_add_new_store');
		
		add_submenu_page( '', 'Edit store', 'Edit store', 'manage_options', 'edit-store', 'ns_edit_store');
	 
	}
	
	add_action('admin_menu', 'ns_shope_store_menu');
	
	function ns_stores(){
		global $wpdb;
		if(isset($_GET['delete']) && !empty($_GET['delete'])){
			$id = (int)  $_GET['delete'];
			$delete = "DELETE FROM wp_shope_stores WHERE id = $id";
			if($wpdb->query($delete)){
				$temp_msg  = '<div id="message" class="updated below-h2"><p>Store has been deleted successfully.</p></div>';
			}
		}
		if(isset($_GET['created']) || isset($_GET['updated'])){
			$type = isset($_GET['created']) ? 'created' : 'updated';
			$temp_msg  = '<div id="message" class="updated below-h2"><p>Store has been '.$type.' successfully.</p></div>';
		}
		if( $_SERVER["REQUEST_METHOD"] == "POST" ){
			if($_POST['action'] == 'delete'){
				if(!empty($_POST['Store'])){
					foreach($_POST['Store'] as $store){
						$delete = "DELETE FROM wp_shope_stores WHERE id = $store";
						$wpdb->query($delete);
					}
					$temp_msg  = '<div id="message" class="updated below-h2"><p>Action completed successfully.</p></div>';
				}
			}
			 
		}
	    $pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
		$limit = PAGELIMIT;
		$offset = ( $pagenum - 1 ) * $limit;
		
		$stores = $wpdb->get_results( "SELECT * FROM wp_shope_stores LIMIT $offset, $limit" );
		 
		?>
		<div class="wrap">
		  <h2> <?php  _e('Stores') ?> <a class="add-new-h2"  href="?page=add-new-store">Add New</a> </h2>
		   <hr/>
			<?php
			if(isset($temp_msg)){
				echo $temp_msg; 
			}
		?>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
			<select name="action">
				<option value="-1" selected="selected">Bulk Actions</option>
				<option value="delete">Delete</option>
			</select>
			<input type="submit" name="" id="doaction" class="button action" value="Apply">
			</div>
			 
			<br class="clear">
			</div>		
		<table class="widefat page fixed" cellpadding="0">
		  <thead>
			<tr>
				<th id="cb" class="manage-column column-cb check-column" scope="col">
					<input type="checkbox"/>
				</th>  
				<th class="manage-column"><?php _e('Name')?></th>
				<th class="manage-column"><?php _e('Address')?></th>
				<th class="manage-column" style="width:100px;"><?php _e('City')?></th>
				<th class="manage-column" style="width:100px;"><?php _e('State')?></th>
				<th class="manage-column" style="width:100px;"><?php _e('Zip Code')?></th>
				<th class="manage-column" style="width:100px;"><?php _e('Phone')?></th>
				<th class="manage-column" style="width:100px;"><?php _e('Action')?></th>
			</tr>
		  </thead>
		  <tfoot>
			<tr>
				<th   class="manage-column column-cb check-column" scope="col">
					<input type="checkbox"/>
				</th> 
				<th class="manage-column"><?php _e('Name')?></th>
				<th class="manage-column"><?php _e('Address')?></th>
				<th class="manage-column"><?php _e('City')?></th>
				<th class="manage-column"><?php _e('State')?></th>
				<th class="manage-column"><?php _e('Zip Code')?></th>
				<th class="manage-column"><?php _e('Phone')?></th>
				<th class="manage-column"><?php _e('Action')?></th>
			</tr>
		  </tfoot>
			<tbody>
			<?php 
				if(!empty($stores)){
					$i = 0;
					foreach($stores as $val){ 
						$i++;
						$bgcolor = ($i%2 == 0 ) ? '#f9f9f9':'';
						echo '<tr style="background-color:'.$bgcolor.'">';
						?> 
						 
						<th id="cb" class="manage-column column-cb check-column" scope="col">
							<input name="Store[<?php echo $val->id; ?>]" value="<?php echo $val->id; ?>" type="checkbox"/>
						</th> 
						<td><?php echo $val->name; ?></td>
						<td><?php echo $val->address; ?></td>
						<td><?php echo $val->city; ?></td>
						<td><?php echo $val->state; ?></td>
						<td><?php echo $val->zip_code; ?></td>
						<td><?php echo $val->phone; ?></td>
						<td>
						<div class="row-actions-visible">
							<span class="edit"><a href="?page=edit-store&id=<?php echo $val->id?>">Edit</a></span> | <span class="delete">
						<a href="?page=stores&delete=<?php echo $val->id?>" onclick="return confirm('Are you sure you want to delete this link?');" style="color:red;">Delete</a>
						</span>
						  </div>
						</td>
						</tr>
						<?php	 
					}
				
				}
				else { ?>
				<tr><td colspan="8"><?php _e('There are no store yet.')?></td></tr>   
				<?php
				}
			?>
			
			</tbody>
		</table>
		<?php 
		$total = $wpdb->get_var( "SELECT COUNT(id) FROM wp_shope_stores" );
		$num_of_pages = ceil( $total / $limit );
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'pagenum', '%#%' ),
			'format' => '',
			'prev_text' => __( '&laquo;', 'aag' ),
			'next_text' => __( '&raquo;', 'aag' ),
			'total' => $num_of_pages,
			'current' => $pagenum
		) );

		if ( $page_links ) {
			echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
		}
		?>
		</form>
		</div>
		<?php
	}
	
	function ns_add_new_store(){
		
		global $wpdb;
		if( $_SERVER["REQUEST_METHOD"] == "POST" ){
			extract($_POST);
			$now = current_time('mysql', false);
			$data = array(
				'name'     => mysql_real_escape_string(trim($name)),
				'address'  => mysql_real_escape_string(trim($address)),
				'city'     => mysql_real_escape_string(trim($city)),
				'state'    => mysql_real_escape_string(trim($state)),
				'zip_code' => mysql_real_escape_string(trim($zip_code)),
				'phone'    => mysql_real_escape_string(trim($phone)),
				'created'  => $now
			); 
			if($wpdb->insert('wp_shope_stores',$data)){
				header('location:?page=stores&created=1');
			}
			 
		}
		 
		?>
		<div class="wrap">
		<h2>Add New Store</h2>
		<hr/>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<table class="form">
				<tr>
					<th style="width:100px"><label for="name">Name:</label></th>
					<td>
						<input type="text" name="name" style="width:310px" id="name" required >
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="address">Address:</label></th>
					<td>
						<textarea name="address" id="address" cols="40" rows="3"></textarea>
					</td>
				</tr> 
				<tr valign="top">
					<th scope="row"><label for="city">City:</label></th>
					<td>
						<input type="text" id="city" name="city" required >
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="state">State:</label></th>
					<td>
						<input type="text" id="state" name="state" required >
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="zip_code">Zip Code:</label></th>
					<td>
						<input type="text" name="zip_code" id="zip_code" required >
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="phone">Phone:</label></th>
					<td>
						<input type="text" name="phone" id="phone" required >
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">&nbsp;</th>
					<td>
					 <input type="submit" name="submit" value="Save" class="button button-primary">
					</td>
				</tr>
			
			</table>
		</form>
		</div>
		<?php 
		
	}
	
	function ns_edit_store(){
		global $wpdb;
		$store_id = (int) $_GET['id'];
		$store = $wpdb->get_results('SELECT * FROM wp_shope_stores where id='.$store_id);
		if( $_SERVER["REQUEST_METHOD"] == "POST" ){
			extract($_POST);
			$now = current_time('mysql', false);
			$data = array(
				'name'     => mysql_real_escape_string(trim($name)),
				'address'  => mysql_real_escape_string(trim($address)),
				'city'     => mysql_real_escape_string(trim($city)),
				'state'    => mysql_real_escape_string(trim($state)),
				'zip_code' => mysql_real_escape_string(trim($zip_code)),
				'phone'    => mysql_real_escape_string(trim($phone))
			); 
			if($wpdb->update('wp_shope_stores',$data, array('id' => $store_id))){
				header('location:?page=stores&updated=1');
			}
			 
		}
		?>
		<div class="wrap">
		<h2>Update Store</h2>
		<hr/>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			 
			<table class="form">
				<tr>
					<th style="width:100px"><label for="name">Name:</label></th>
					<td>
						<input type="text" name="name" style="width:310px" id="name" required value="<?php echo $store[0]->name; ?>">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="address">Address:</label></th>
					<td>
						<textarea name="address" id="address" cols="40" rows="3"><?php echo $store[0]->address; ?></textarea>
					</td>
				</tr> 
				<tr valign="top">
					<th scope="row"><label for="city">City:</label></th>
					<td>
						<input type="text" value="<?php echo $store[0]->city; ?>" id="city" name="city" required >
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="state">State:</label></th>
					<td>
						<input type="text" id="state" name="state" required value="<?php echo $store[0]->state; ?>" >
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="zip_code">Zip Code:</label></th>
					<td>
						<input type="text" name="zip_code" id="zip_code" required value="<?php echo $store[0]->zip_code; ?>">
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="phone">Phone:</label></th>
					<td>
						<input type="text" name="phone" id="phone" required value="<?php echo $store[0]->phone; ?>">
					</td>
				</tr>

				<tr valign="top">
					<th scope="row">&nbsp;</th>
					<td>
					 <input type="submit" name="submit" value="Update" class="button button-primary">
					</td>
				</tr>
			
			</table>
		</form>
		</div>
		<?php 
	}
	
	function ns_store_locations(){
		global $wpdb;
		$stores = null;
		if( $_SERVER["REQUEST_METHOD"] == "POST" ){
			
			if(!empty($_POST['postal-code'])){
				$postCode = mysql_real_escape_string(trim($_POST['postal-code']));
				$distance = mysql_real_escape_string(trim($_POST['distance']));
				$radius   = $distance;
				if($_POST['distance-type'] == 'mile'){
					$miles_val= "1.609344";
					$radius =  intval(intval($distance) * $miles_val);  
				}
				$url='http://api.geonames.org/findNearbyPostalCodes?postalcode='.$postCode.'&country=US&radius='.$radius.'&username=ravikant_nascenture&maxRows=50';
				$parsedXML = simplexml_load_file($url);
				 
				$postalCodes = array();
				if(!empty($parsedXML)){
					foreach($parsedXML->code as $val){
						$postalCodes[] = (string) $val->postalcode;
					}
					$postalCodes = implode(',',$postalCodes);
					$query = "SELECT * FROM wp_shope_stores where zip_code in($postalCodes)";
					$stores = $wpdb->get_results($query);
					 
				}
				
			}
		    
		}
		?>
		  
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<table class="form-table" id="mainTable">
				<tr valign="top">
					 
					<td>
					<label for="postal-code"><b>Postal code:</b></label><br/>
					<input type="text" id="postal-code" name="postal-code" required value="<?php echo isset($_POST['postal-code']) ? $_POST['postal-code']:'';  ?>" >
					</td>
				</tr>
				<tr valign="top">
					 
					<td>
					<!-- 
					<label for="distance"><b>Distance:</b></label><br/>
						<input type="text" id="distance" name="distance" required value="<?php echo isset($_POST['distance']) ? $_POST['distance']:'';  ?>">
					-->
					<?php $option = isset($_POST['distance']) ? $_POST['distance']:''; ?>
					<label for="distance"><b>Distance:</b></label><br/>
					<select name="distance">
						<option <?php if ($option == 10 ) echo 'selected'; ?> >10</option>
						<option <?php if ($option == 20 ) echo 'selected'; ?> >20</option>
						<option <?php if ($option == 30 ) echo 'selected'; ?> >30</option>
						<option <?php if ($option == 40 ) echo 'selected'; ?> >40</option>
						<option <?php if ($option == 50 ) echo 'selected'; ?> >50</option>
					</select>
					<?php $type = isset($_POST['distance-type']) ? $_POST['distance-type']:''; ?>
					<select name="distance-type">
						<option value="mile" <?php if ($type == 'mile' ) echo 'selected'; ?> >Miles</option>
						<option value="km" <?php if ($type == 'km' ) echo 'selected'; ?> >Kilometers</option>
					</select>
					</td>
				</tr>
				 
				<tr>
					<td>
					<input type="submit" name="submit" value="Search"> 
					</td>
				</tr>
			</table>
		</form>
		
		<div class="store-data">
			<table>
			<?php
			if($stores){
				foreach($stores as $store){
					echo '<tr><td>'.$store->name;
					echo '</td><td>'.$store->address;
					echo '</td><td>'.$store->city;
					echo '</td><td>'.$store->state;
					echo '</td><td>'.$store->zip_code;
					echo '</td><td>'.$store->phone;
					echo '</td></tr>';
				}	
			} else {
				echo '<tr><td colspan="3">There are no store.</td></tr> ';
			}
			?>
			</table>
		</div>
		<?php 
		
	}
	add_shortcode( 'store-locations', 'ns_store_locations' );
	
 
	function ns_store_activate() {
	   global $wpdb;  
		$sql = 'CREATE TABLE IF NOT EXISTS `wp_ns_shope_stores` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL,
			  `address` varchar(255) NOT NULL,
			  `city` varchar(100) NOT NULL,
			  `state` varchar(100) NOT NULL,
			  `zip_code` int(11) NOT NULL,
			  `phone` varchar(100) NOT NULL,
			  `created` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18;';
		
		$wpdb->query($sql);
	}
	register_activation_hook( __FILE__, 'ns_store_activate' );
	
	function ns_store_deactivate() {
		
		global $wpdb;
		$tables = "DROP TABLE wp_ns_shope_stores";
		$wpdb->query($tables);
		
	}
	register_deactivation_hook( __FILE__, 'ns_store_deactivate' );
	