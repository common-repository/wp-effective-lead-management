<?php
/*
Plugin URI: http://www.best-hostings.in/wp-effective-lead-management/
Plugin Name: WP Effective Lead Management
Description: WP Effective Lead Management system is a lead Management solution for Wordpress websites to manage the leads or contact us Emails. This is kind of very tiny CRM for a sales person who follows up with leads. This best fits of someone who is looking for simple and effective solutuion. If you are small company with few sales guys, this works better, Doing a complete integration of other complex softwares to handle the leasds is a overkill sometime.Requires WordPress 2.7 or higher.
Version: 3.0.1
Author: best-hostings.in
Author URI: http://www.best-hostings.in/
License: GPL

WP Effective Lead Management - Manage Leads in WordPress, no need for any complex CRM
Version 3.0.1
Copyright (C) 2011 best-hostings.in
Released 2012-01-24
Contact Web Hosting at http://www.best-hostings.in/
*/

// +---------------------------------------------------------------------------+
// | WP hooks                                                                  |
// +---------------------------------------------------------------------------+

 /* WP actions */

register_activation_hook( __FILE__, 'install_tables' );
register_deactivation_hook( __FILE__, 'fstst_deactivate' );
add_action('admin_menu', 'fstst_addpages');
add_action('init', 'fstst_addcss');
add_action( 'admin_init', 'register_fstst_options' );
add_shortcode('WH-InsertNewVal', 'fstst_insertnew');
add_shortcode('WH-DisplayForm', 'fstst_newform');


function register_fstst_options() { // set options
  register_setting( 'fstst-option-group', 'fs_admng' );
  register_setting( 'fstst-option-group', 'fs_deldata' );
  register_setting( 'fstst-option-group', 'fs_copyright' );
}

function unregister_fstst_options() { //unset options
  unregister_setting( 'fstst-option-group', 'fs_admng' );
  unregister_setting( 'fstst-option-group', 'fs_copyright' );
}

function fstst_addcss() { // include style sheet
  	  wp_enqueue_style('fstst_css', '/' . PLUGINDIR . '/wp-effective-lead-management/css/wh-leadmanagement-style.css' );
}
// +---------------------------------------------------------------------------+
// | Create admin links                                                        |
// +---------------------------------------------------------------------------+

function fstst_addpages() {

	if (get_option('fs_admng') == '') { $fs_admng = 'update_plugins'; } else {$fs_admng = get_option('fs_admng'); }
    // Create top-level menu and appropriate sub-level menus:
	add_menu_page('LeadManagement', 'LeadManagement', $fs_admng, 'fstst_manage', 'fstst_adminpage');
	add_submenu_page('fstst_manage', 'Settings', 'Settings', $fs_admng, 'fsfstst_config', 'fstst_options_page');
}



// +---------------------------------------------------------------------------+
// | Create DatabaseTable  during  activation                                                        |
// +---------------------------------------------------------------------------+


function install_tables() {

   global $wpdb;

   $table_name = $wpdb->prefix . "adodislead";

      //create database table

	   $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . "(
		testid int( 15 ) NOT NULL AUTO_INCREMENT ,
		names text,
		companyname text,
		email text,
		phoneno float(50),
		country varchar(25),
		querysubject text,
		regdate datetime,
		sales_person varchar(30),
		lead_published 	int(11),
		PRIMARY KEY ( `testid`)
		) ";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	// insert default settings into wp_options
	$toptions = $wpdb->prefix ."options";
	$insert = "INSERT INTO ".$toptions.
		"(option_name, option_value) " .
		"VALUES ('fs_admng', 'update_plugins')";
	  $solu = $wpdb->query( $insert );
	$insert = "INSERT INTO ".$toptions.
		"(option_name, option_value) " .
		"VALUES ('fs_deldata', '')";
	  $solu = $wpdb->query( $insert );
	$insert = "INSERT INTO ".$toptions.
		"(option_name, option_value) " .
		"VALUES ('fs_copyright', '')";
	  $solu = $wpdb->query( $insert );
}


function fstst_Set() {
	if (current_user_can('update_plugins'))
	add_filter('plugin_action_links', 'add_settings_link', 10, 2 );
}


//+--------------------------------------------------------------------
/* add new lead form In Wordpress Site */
//+--------------------------------------------------------------------

function fstst_newform() {
?>
	<div class="wrap">
	<h2>Add New Lead</h2>
	<br />
	<div id="sfstest-form" class="form">
	<form name="addnew" method="post" id="test" onsubmit="return validateForm();" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<label for="name">Name:</label> <input name="names" type="text" required="required" size="45"><br/>
	<label for="companyname">Company Name:</label> <input name="companyname" type="text"  required="required" size="45"><br/>
	<label for="email">Email:</label> <input name="email" type="text" required="required" size="45" ><br/>
	<label for="phoneno">PhoneNo</label><input name="phoneno" required="required" type="text" ><br/>
	<label for="country">Country</label><input name="country" required="required" type="text" ><br/>
	<label for="querysubject">Requirements:</label> <textarea name="querysubject" cols="30" required="required" rows="5"></textarea><br/><br/>
	<input type="submit" name="fstst_addnew" value="Save Lead" /><br/>
	<input type="reset" name="reset"><br/>
	</form>
<?php
	   $copyright = get_option('fs_copyright');

if($copyright){
	 ?>
	<div style="text-align:center;font-size:10px;">Developed by <a href="http://www.best-hostings.in/">best web hosting</a></div>

<?php
 }
	?>
	</div>
	</div>
<?php }


//***************************************************************************************************
// +---------------------------------------------------------------------------+
// | Add New leads                                                       |
// +---------------------------------------------------------------------------+

/* insert lead into DB */
function fstst_insertnew() {
	global $wpdb;
	 if (isset($_POST['fstst_addnew'])) {
	 $table_name = $wpdb->prefix . "adodislead";
	 $names = $wpdb->escape($_POST['names']);
	 $companyname = $wpdb->escape($_POST['companyname']);
	 $email = $wpdb->escape($_POST['email']);

	/* if ( !is_email( $email ) ) {
      echo 'email address is valid.';
      }*/
	 $phoneno = $wpdb->escape($_POST['phoneno']);
	 $country = $wpdb->escape($_POST['country']);
	 $querysubject = $_POST['querysubject'];
     date_default_timezone_set('Asia/Calcutta');
	 $regdate = date('Y/m/d H:i:s');
	 $insert = "INSERT INTO " . $table_name .
	 " (names,companyname,email,phoneno,country,querysubject,regdate) " .
	 "VALUES ('$names','$companyname','$email','$phoneno','$country','$querysubject','$regdate')";
	 $results = $wpdb->query( $insert );

       if($regdate){

             $getdatafromclient =  $wpdb->get_row("SELECT testid FROM $table_name WHERE regdate = '$regdate'");

             $gettestid = $getdatafromclient->testid;
           }
		   if (isset($_POST['fstst_addnew'])) {
	    fstst_email($gettestid);
		}
		}
      }


		/*function is_email( $email )
		{
		echo "<script>alert('email is here')</script>";

		}*/



//+--------------------------------------------------------------------
//this function is to send an email to the members
//+--------------------------------------------------------------------

function fstst_email($gettestid){
    global $wpdb;
	$table_name = $wpdb->prefix . "users";
    $table_name1 = $wpdb->prefix . "adodislead";
    $tstlist = $wpdb->get_results("SELECT user_login,user_email FROM $table_name");
	$gettst2 = $wpdb->get_row("SELECT  names, companyname, email, phoneno, country FROM $table_name1 WHERE testid = $gettestid");

    $body ='
      <table class="wide_fat" id="table_align" width="100%" cellspacing="0" border="1">
      <tr>
      <th>ClientName</th>
      <td align="center"> '.$gettst2->names.' </td>
	  </tr>
      <tr>
      <th>CompanyName</th>
	  <td align="center"> '.$gettst2->companyname.' </td>
      </tr>
	  <tr>
      <th>E-mail</th>
	  <td align="center"> '.$gettst2->email.' </td>
      </tr>
	  <tr>
      <th>PhoneNo</th>
	  <td align="center"> '.$gettst2->phoneno.'</td>
      </tr>
	  <tr>
      <th>Country</th>
	  <td align="center"> '.$gettst2->country.' </td>
      </tr>
      </table>';
    $body = $body;
    $size = count($tstlist);
    $email = $_POST['email'];
    $subject = "You Got The New Lead Please Check it";
    $from = $email;
    $headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=iso-8859-2\r\nContent-Transfer-Encoding: 8bit\r\nX-Priority: 1\r\nX-MSMail-Priority: High\r\n";
	$headers .= "From: $from\r\n" . "Reply-To: $from\r\n";

     for($i=0; $i < $size; $i++)
     {
        $status =  mail($tstlist[$i]->user_email, $subject, $body, $headers);

     }

     if($status)
     {
     	   echo "<script>alert('Your Mail is sucessfully delivered')</script>";
     }

  }


// +---------------------------------------------------------------------------+
// | Manage Page - list all and show view/delete options                       |
// +---------------------------------------------------------------------------+

/* show list of leads */

function fstst_showlist() {

	  ?>
        <h3>A New Lead to Pick</h3>
	  <?php
	global $wpdb;
	$table_name = $wpdb->prefix . "adodislead";
	$tstlist = $wpdb->get_results("SELECT testid,names,companyname,email,phoneno,querysubject,regdate FROM $table_name ORDER BY regdate Desc");
     $i = 1;
	foreach ($tstlist as $tstlist2) {
		echo '<p>';
		echo "("; echo $i; echo ")";
		 echo '&nbsp;&nbsp;';
		echo "<b>"; echo ($tstlist2->names); echo "</b>";
	    echo '&nbsp;&nbsp;';
		echo "<i>"; echo ($tstlist2->companyname); echo "</i>";
		echo '&nbsp;&nbsp;';
	    echo "<b style='color:red;'>"; echo ($tstlist2->regdate); echo "</b>";
		echo '&nbsp;|&nbsp;';
		//echo "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI']; exit;
		echo '&nbsp;&nbsp;';
		echo '<a href="admin.php?page=fstst_manage&amp;mode=fststrem&amp;testid='.$tstlist2->testid.'" onClick="return confirm(\'Delete this lead?\')">Delete</a>';
		echo '&nbsp;&nbsp;';
		echo '<a href="admin.php?page=fstst_manage&amp;mode=fststedit1&amp;testid='.$tstlist2->testid.'">View</a>';
		echo '</p>';
		$i++;
	}
}



//+-------------------------------------------------------------------------------------
/* this is to view the leads*/
//+-------------------------------------------------------------------------------------

function fstst_edit1($testid){
	 global $wpdb;
	 $table_name = $wpdb->prefix . "adodislead";
	 $gettst2 = $wpdb->get_row("SELECT testid, names, companyname, email, phoneno, country, querysubject, sales_person FROM $table_name WHERE testid = $testid");
     $salesperson = $gettst2->sales_person;
     $username = wt_get_user_name();
     echo '<div class="total">';
     echo '<h3>View Lead</h3>';
     echo '<h3><a href="admin.php?page=fstst_manage">Back</a></h3>';
     echo '<table class="wide_fat" id="table_align" width="100%" cellspacing="0" border="1">';
     echo '<tr class="title_background">';
     echo '<th>Name</th>';echo '<th>CompanyName</th>';echo '<th>Email</th>';echo '<th>PhoneNo</th>';echo '<th>Country</th>';
     echo '</tr>';
     echo '&nbsp;&nbsp;';
     echo '<tr align="center;">';
     echo '<td>'.$gettst2->names.'</td>';
     echo '<td>'.$gettst2->companyname.'</td>';
     echo '<td>'.$gettst2->email.'</td>';
     echo '<td>'.$gettst2->phoneno.'</td>';
     echo '<td>'.$gettst2->country.'</td>';

    $test = strcmp($salesperson,$username);

    if($salesperson == "")
    {
     echo '<td>'; echo'<div class="pick_me_link" style="text-align:center;">'; echo '<a href="admin.php?page=fstst_manage&amp;mode=fststpickme&amp;testid='.$gettst2->testid.'">Pick Me</a>'; echo'</div>'; echo '</td>';
    }
    else
    {

    if(!$test)

      {
        echo '<td>'; echo'<div class="pick_me_link" style="text-align:center;">'; echo '<a href="admin.php?page=fstst_manage&amp;mode=fststpickme&amp;testid='.$gettst2->testid.'">U Can View</a>'; echo'</div>'; echo '</td>';
        echo '<td>'; echo'<div class="pick_me_link" style="text-align:center;">'; echo '<a href="admin.php?page=fstst_manage&amp;mode=fststunpickme&amp;testid='.$gettst2->testid.'">UnPick</a>'; echo'<div>'; echo '</td>';

      }
     else{

        echo '<td>'; echo'<div class="pick_me_link1" style="text-align:center; color:red; border:1px solid;">'; echo "Picked by"; echo'<br>'; echo $salesperson;  echo'</div>'; echo '</td>';

       }
      }
        echo '</tr>';
        echo '</table>';
        echo '</div>';
      }

//+------------------------------------------------------------------------------
//this function is for picking the lead from the list of client leads
//+------------------------------------------------------------------------------

function fstst_pickme($testid){
    global $wpdb;
	$table_name = $wpdb->prefix . "adodislead";
    $testid = $testid;
    $gettst2 = $wpdb->get_row("SELECT testid, names, companyname, email, phoneno, country, querysubject, sales_person FROM $table_name WHERE testid = $testid");
    $clientemail =  $gettst2->email;
    $username = wt_get_user_name();
    $useremail =  wt_get_user_email();
    $dbuname = $gettst2->sales_person;

      if($username == $dbuname)
      {
          echo '<div class="total">';
          echo '<h3><a href="admin.php?page=fstst_manage">Back</a></h3>';
          echo '<table class="wide_fat" id="table_align" width="100%" cellspacing="0" border="1">';
          echo '<tr class="title_background">';
          echo '<th>Name</th>';echo '<th>CompanyName</th>';echo '<th>Email</th>';echo '<th>PhoneNo</th>';echo '<th>Country</th>';echo '<th>Requirements</th>';
          echo '</tr>';
          echo '&nbsp;&nbsp;';
          echo '<tr align="center;">';
          echo '<td>'.$gettst2->names.'</td>';
          echo '<td>'.$gettst2->companyname.'</td>';
          echo '<td>'.$gettst2->email.'</td>';
          echo '<td>'.$gettst2->phoneno.'</td>';
          echo '<td>'.$gettst2->country.'</td>';
          echo '<td>'.$gettst2->querysubject.'</td>';
          echo '</tr>';
          echo '</table>';
          echo '</div>';
           }
            else
           {

            ?><div id="message" class="updated fade"><p><strong><?php _e('You cannot view the Requirements of this lead'); ?>.</strong></p></div><?php
              echo "<table width='100%' border='0' cellspacing='0' cellpadding='0'>";
        	  echo "<tr>";
        	  echo "<td>";
        	  echo "This Lead is already picked by $dbuname..";
        	  echo "</td>";
        	  echo "</tr>";
        	  echo "</table>";
           }
      }



//+--------------------------------------------------------------------
/* send confirmation email to client */
//+--------------------------------------------------------------------

     function send_confir_email_to_client($test,$cilentname){

       $useremail =  wt_get_user_email();
       $username = wt_get_user_name();

      $body ='
      <table  width="100%" cellspacing="0" border="0">
	  <tr>
      <td>Hi</td>
      </tr>
	  <tr>
	  <td> '.$cilentname.',</td>
	  </tr>
	  <tr>
	  <td>*****************<td>
	  </tr>
	  <tr>
	  <td> Thank You for registering we will contact you with in 24 hours </td>
	  </tr>
	  <tr>
	  <td>*****************<td>
	  </tr>
	  <tr>
	  <td> With Regards </td>
	  </tr>
	  <tr>
	  <td> '.$username.' </td>
	  </tr>
      </table>';
      $subject = "Thank You For Registering";
      $to = $test;
      $headers = "MIME-Version: 1.0\r\n";
	  $headers .= "Content-type: text/html; charset=iso-8859-2\r\nContent-Transfer-Encoding: 8bit\r\nX-Priority: 1\r\nX-MSMail-Priority: High\r\n";
	  $headers .= "From: $useremail\r\n" . "Reply-To: $useremail\r\n";

        if(mail( $to, $subject, $body, $headers )){

           ?><div id="message" class="updated fade"><p><strong><?php _e('Mail is Sent'); ?>.</strong></p></div><?php

             }
          }


//+---------------------------------------------------------------
/* this function is to get login user name*/
//+----------------------------------------------------------------

        function wt_get_user_name()
        {
	      global $userdata;
	      get_currentuserinfo();
	      return $userdata->user_login;
        }



//+---------------------------------------------------------------
/* this function is to get login user email*/
//+----------------------------------------------------------------

       function wt_get_user_email()
        {
	      global $userdata;
	      get_currentuserinfo();
	      return $userdata->user_email;
        }



//+----------------------------------------------------------------
/* delete lead from DB */
//+----------------------------------------------------------------

function fstst_removetst($testid) {
	global $wpdb;
   $table_name = $wpdb->prefix . "adodislead";
   $result = $wpdb->get_row("SELECT sales_person FROM $table_name WHERE testid = '$testid'");
   $salesperson = $result->sales_person;
   $username = wt_get_user_name();

	if($salesperson == $username)
	{
	$table_name = $wpdb->prefix . "adodislead";
	$insert = "DELETE FROM " . $table_name .
	" WHERE testid = ".$testid ."";
	$results = $wpdb->query( $insert );
	return true;
	}

	else{
		?><div id="message" class="updated fade"><p><strong><?php _e('No Permission to Delete this lead'); ?>.</strong></p></div><?php
		return false;
	}
  }


//+-----------------------------------------------------------------
/*this function is to store the pick up member in to the database */
//+----------------------------------------------------------------
function lead_store_in_db($testid)
{
    global $wpdb;
    $testvalue = $testid;
	$table_name = $wpdb->prefix . "adodislead";
    $username = wt_get_user_name();
    $published = 1;
    $gettst2 = $wpdb->get_row("SELECT testid, names, companyname, email, phoneno, country, querysubject, sales_person FROM $table_name WHERE testid = $testid");
    $cilentname = $gettst2->names;
    $cilentemail = $gettst2->email;
    $useremail =  wt_get_user_email();
    $dbuname = $gettst2->sales_person;

    if(($username == $dbuname)||($dbuname == ""))
     {
     	if( $wpdb->query("UPDATE " .$table_name .
	     " SET sales_person = '$username', ".
	     " lead_published = '$published' ".
	     " WHERE testid = '$testvalue'"))
	      {
		    send_confir_email_to_client($cilentemail,$cilentname);
	      }

     }
}

//==========for unpicking========

function fstst_unpick($testid)
{
    global $wpdb;
    $table_name = $wpdb->prefix . "adodislead";
    $gettst2 = $wpdb->get_row("SELECT testid, names, companyname, email, phoneno, country, querysubject, sales_person FROM $table_name WHERE testid = $testid");
    $namenull = Null;
         //echo '<pre>'; print_r($gettst2);
      $location = "wp-admin/admin.php?page=fstst_manage";
      $status=301;
        if($wpdb->query("UPDATE " .$table_name .
	     " SET sales_person = '$namenull', ".
	     " lead_published = '$published' ".
	     " WHERE testid = '$testid'")){
	     	?><div id="message" class="updated fade"><p><strong><?php _e('Again Lead is Reset Back Anybody can pick this'); ?>.</strong></p></div><?php

                fstst_showlist();
	     }
       }

//==================================
function fstst_options_page(){
?>
   <div class="wrap">


	<?php if ($_REQUEST['updated']=='true') { ?>
	<div id="message" class="updated fade"><p><strong>Settings Updated</strong></p></div>
	<?php  } ?>

	<h2>LeadManagement Settings</h2>

    <form method="post" action="options.php">

     <?php wp_nonce_field('update-options'); ?>

	 <?php settings_fields( 'fstst-option-group' ); ?>

     <table>

     <tr>
	<td>
	     show copyright information
	</td>
	<td><?php
	$fs_copy = get_option('fs_copyright');


	if ($fs_copy == '1') { ?>
	<input type="checkbox" name="fs_copyright" value="1" checked />
	<?php } else { ?>
	<input type="checkbox" name="fs_copyright" value="1" />
	<?php } ?>
	</td>
	</tr>
	<tr>

     <tr valign="top">

	<td>Removes Database table when deactivating plugin</td>

	<td>
	<?php $fs_deldata = get_option('fs_deldata');

	if ($fs_deldata == 'yes') { ?>
	<input type="checkbox" name="fs_deldata" value="yes" checked /> (this will result in all data being deleted!)
	<?php } else { ?>
	<input type="checkbox" name="fs_deldata" value="yes" /> (this will result in all data being deleted!)
	<?php } ?>
	</td>
	</tr>
	</table>
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="page_options" value="fs_admng,fs_deldata" />
	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
	</form>
	</div>
    <?php
     }


  //+-----------------------------------------------------------------------
    /* admin page display */
  //+----------------------------------------------------------------------

 function fstst_adminpage() {
	   global $wpdb;
     ?>
	      <div class="wrap">
	   <?php
	      echo '<h2>Lead Management Page</h2>'; ?>
       <?php
		if (isset($_POST['fstst_addnew'])) {
			fstst_insertnew(); ?>

	   <div id="message" class="updated fade"><p><strong><?php _e('Lead Added'); ?>.</strong></p></div><?php
		}
		if ($_REQUEST['mode']=='fststrem') {
			$status = fstst_removetst($_REQUEST['testid']);
			if($status){

				?><div id="message" class="updated fade"><p><strong><?php _e('Lead Deleted'); ?>.</strong></p></div><?php
			}
			else{
				?><div id="message" class="updated fade"><p><strong><?php _e('Lead Not Deleted'); ?>.</strong></p></div><?php
			}
		}

		if ($_REQUEST['mode']=='fststedit1') {
			     fstst_edit1($_REQUEST['testid']);
                 exit;
		}

		if ($_REQUEST['mode']=='fststpickme') {
                lead_store_in_db($_REQUEST['testid']);
			    fstst_pickme($_REQUEST['testid']);
                    exit;
		}

		if ($_REQUEST['mode']=='fststunpickme') {
               fstst_unpick($_REQUEST['testid']);
                   exit;
		}

			fstst_showlist(); // show adodislead
		?>
	</div>
	<div class="wrap"><?php fstst_newform() // show form to add new Leads ?>
	</div>

  <?php }


// +---------------------------------------------------------------------------+
// | Uninstall plugin                                                          |
// +---------------------------------------------------------------------------+

function fstst_deactivate () {
	global $wpdb;
	$table_name = $wpdb->prefix . "adodislead";
	$fs_deldata = get_option('fs_deldata');
	if ($fs_deldata == 'yes') {
		$wpdb->query("DROP TABLE {$table_name}");
		delete_option("fs_deldata");
		delete_option("fs_copyright");
 	}

    delete_option("fstst_version");
	unregister_fstst_options();
}

?>
