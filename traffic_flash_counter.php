<?php

/*
Plugin Name: Traffic flash counter
Plugin URI: http://donicounters.donimedia-servicetique.net/
Description: With this plugin , you can display a customizable Flash unique visitors counter , on your Wordpress website .
Version: 1.0.1
Author: David DONISA
Author URI: http://donicounters.donimedia-servicetique.net/
*/



	//  error_reporting(E_ALL);  //  For DEBUG purpose
	add_action("widgets_init", array('Traffic_flash_counter', 'register'));
	register_activation_hook( __FILE__, array('Traffic_flash_counter', 'activate'));
	register_deactivation_hook( __FILE__, array('Traffic_flash_counter', 'deactivate'));



	global $today, $yesterday, $total, $date, $days, $plugin_dir, $data, $plugin_prefix, $ip_file_path, $count_file_path;

	$ip = $_SERVER['REMOTE_ADDR'];


	$plugin_dir = basename(dirname(__FILE__));
	global $flash_component_width,$flash_component_height, $plugin_prefix;
		
	$ip_file_name = 'ip.db';
	$ip_file_path = WP_PLUGIN_DIR."/{$plugin_dir}/counter/$ip_file_name";
	$count_file_name = 'count.db';
	$count_file_path = WP_PLUGIN_DIR."/{$plugin_dir}/counter/$count_file_name";
	

	$plugin_prefix = 'counter_tfc';




	if (!is_file($ip_file_path)) {

		$file_ip = fopen($ip_file_path, 'wb');
		fclose($file_ip);

		$file_count = fopen($count_file_path, 'wb');
		fclose($file_count);	

		update_option( $plugin_prefix.'_ip_already_exists', '0' );
		$ip_already_exists = 0;	

	};	

	


	$file_ip = fopen($ip_file_path, 'rb');
	while (!feof($file_ip)) $line[]=fgets($file_ip,1024);
	for ($i=0; $i<(count($line)); $i++) {
		list($ip_x) = split("\n",$line[$i]);
		if ($ip == $ip_x) {

			update_option( $plugin_prefix.'_ip_already_exists', '1' );
			$ip_already_exists = 1;

		};
	};

	fclose($file_ip);





	if (!($ip_already_exists == 1)) {

		$file_ip2 = fopen($ip_file_path, 'ab');
		$line = "$ip\n";
		fwrite($file_ip2, $line, strlen($line));
		$file_count = fopen($count_file_path, 'rb');
		$data = '';
		while (!feof($file_count)) $data .= fread($file_count, 4096);
		fclose($file_count);
		list($today, $yesterday, $total, $date, $days) = split("%", $data);

		if ($date == date("Y m d")) $today++;
		else {
				$yesterday = $today;
				$today = 1;
				$days++;
				$date = date("Y m d");
		}

		$total++;
		$line = "$today%$yesterday%$total%$date%$days";
		
		$file_count2 = fopen($count_file_path, 'wb');
		fwrite($file_count2, $line, strlen($line));
		fclose($file_count2);
		fclose($file_ip2);

	};



class Traffic_flash_counter {

  function activate(){

		global $today, $yesterday, $total, $date, $days, $plugin_dir, $data;

    	$data = array( 
								'counter_tfc_title' => 'Your widget title',
								'counter_tfc_today_label' => 'Today :',
								'counter_tfc_yesterday_label' => 'Yesterday :',
								'counter_tfc_daily_average_label' => 'Daily average :',
								'counter_tfc_total_label' => 'Total :',
								'counter_tfc_flash_component_width' => '130' ,
								'counter_tfc_flash_component_height' => '200' ,
								'counter_tfc_flash_url_redirection' => 'http://donicounters.donimedia-servicetique.net'
							);

    	if ( ! get_option('counter_tfc_name')){
     	add_option('counter_tfc_name' , $data);
    	} else {
     	update_option('counter_tfc_name' , $data);
    	}
  }
  function deactivate(){

		global $plugin_prefix, $ip_file_path, $count_file_path;

    	delete_option('counter_tfc_name');
		delete_option($plugin_prefix.'_ip_already_exists');


		umask(0000);  
		chmod($ip_file_path,0777);  
		chmod($count_file_path,0777);  
		unlink ($ip_file_path);  
		unlink ($count_file_path);  

  }


function control(){

	global $today, $yesterday, $total, $date, $days, $plugin_dir, $data;

  $data = get_option('counter_tfc_name');
  ?>

  <p><label>Title <b>:</b> <input name="counter_tfc_title" type="text" size="30" value="<?php echo $data['counter_tfc_title']; ?>" /></label></p>

  <p><label>"Today" label <b>:</b> <input name="counter_tfc_today_label" type="text" size="30" value="<?php echo $data['counter_tfc_today_label']; ?>" /></label></p>
  <p><label>"Yesterday" label <b>:</b> <input name="counter_tfc_yesterday_label" type="text" size="30" value="<?php echo $data['counter_tfc_yesterday_label']; ?>" /></label></p>
  <p><label>"Daily average" label <b>:</b> <input name="counter_tfc_daily_average_label" type="text" size="30" value="<?php echo $data['counter_tfc_daily_average_label']; ?>" /></label></p>
  <p><label>"Total" label <b>:</b> <input name="counter_tfc_total_label" type="text" size="30" value="<?php echo $data['counter_tfc_total_label']; ?>" /></label></p>

  <p><label>Flash component width ( default : 130 ) <b>:</b> <input name="counter_tfc_flash_component_width" type="text" size="4" value="<?php echo $data['counter_tfc_flash_component_width']; ?>" /></label></p>
  <p><label>Flash component height ( default : 65 ) <b>:</b> <input name="counter_tfc_flash_component_height" type="text" size="4" value="<?php echo $data['counter_tfc_flash_component_height']; ?>" /></label></p>
  <p><label>Flash URL redirection <b>:</b> <input name="counter_tfc_flash_url_redirection" type="text" size="35" value="<?php echo $data['counter_tfc_flash_url_redirection']; ?>" /></label></p>


  <?php
   if (isset($_POST['counter_tfc_title'])){

    $data['counter_tfc_title'] = attribute_escape($_POST['counter_tfc_title']);

    $data['counter_tfc_today_label'] = attribute_escape($_POST['counter_tfc_today_label']);
    $data['counter_tfc_yesterday_label'] = attribute_escape($_POST['counter_tfc_yesterday_label']);
    $data['counter_tfc_daily_average_label'] = attribute_escape($_POST['counter_tfc_daily_average_label']);
    $data['counter_tfc_total_label'] = attribute_escape($_POST['counter_tfc_total_label']);

    $data['counter_tfc_flash_component_width'] = attribute_escape($_POST['counter_tfc_flash_component_width']);
    $data['counter_tfc_flash_component_height'] = attribute_escape($_POST['counter_tfc_flash_component_height']);
    $data['counter_tfc_flash_url_redirection'] = attribute_escape($_POST['counter_tfc_flash_url_redirection']);

    update_option('counter_tfc_name', $data);


  }
}


  function widget($args){

		global $today, $yesterday, $total, $date, $days, $plugin_dir, $data;

		$data = get_option('counter_tfc_name');

    	echo $args['before_widget'];
		echo $args['before_title'] .$data['counter_tfc_title']. $args['after_title'];

		$swf_code = '<center>';
		$swf_code .= '<object width="'.$data['counter_tfc_flash_component_width'].'" height="'.$data['counter_tfc_flash_component_height'].'">';
		$swf_code .= '<param name="movie" value="'.WP_PLUGIN_URL."/{$plugin_dir}/component/traffic_flash_counter.swf".'"></param>';
		$swf_code .= '<param name="scale" value="showall"></param>';
		$swf_code .= '<param name="salign" value="default"></param>';
		$swf_code .= '<param name="wmode" value="transparent"></param>';
		$swf_code .= '<param name="allowScriptAccess" value="sameDomain"></param>';
		$swf_code .= '<param name="allowFullScreen" value="true"></param>';
		$swf_code .= '<param name="sameDomain" value="true"></param>';
		$swf_code .= '<embed type="application/x-shockwave-flash" width="'.$data['counter_tfc_flash_component_width'].'" height="'.$data['counter_tfc_flash_component_height'].'" src="'.WP_PLUGIN_URL."/{$plugin_dir}/component/traffic_flash_counter.swf".'" scale="showall" salign="default" wmode="transparent" allowScriptAccess="sameDomain" allowFullScreen="true"';
		$swf_code .= '></embed>';
		$swf_code .= '</object>';
		$swf_code .= '</center>';

    echo $swf_code;

    echo $args['after_widget'];
  }


  function register(){
    register_sidebar_widget('Traffic flash counter', array('Traffic_flash_counter', 'widget'));
    register_widget_control('Traffic flash counter', array('Traffic_flash_counter', 'control'));
  }
}




	$data = get_option('counter_tfc_name');


	$count_file_path = WP_PLUGIN_DIR."/{$plugin_dir}/counter/$count_file_name";
	$file_count = fopen($count_file_path, 'rb');
	$data_file = '';
	while (!feof($file_count)) $data_file .= fread($file_count, 4096);
	fclose($file_count);
	list($today, $yesterday, $total, $date, $days) = split("%", $data_file);





	//  ---------------------------- XML file generation for Flash component -------------------------


	//  The block of instructions below retrieves plugin parameters and options values stored in database :
	//  -------------------------------------------------------------------------------------------------

	$total_blocks = 4;

	$label_name = array();
	$counts_value = array();

	$label_name[0] = $data['counter_tfc_today_label'];
	$label_name[1] = $data['counter_tfc_yesterday_label'];
	$label_name[2] = $data['counter_tfc_daily_average_label'];
	$label_name[3] = $data['counter_tfc_total_label'];

	$counts_value[0] = $today;
	$counts_value[1] = $yesterday;
	$total_days = $days;
	$counts_value[3] = $total;

	if ( $total_days != 0 ) {

		$counts_value[2] = round(($counts_value[3]/$total_days),1);

	} else {

		$counts_value[2] = $counts_value[3];

	};
	
	$flash_url_redirection = $data['counter_tfc_flash_url_redirection'];


	//  The instruction below creates an Instance of DOMDocument class :

	$doc_xml = new DOMDocument();

	//  The instructions below defines the XML file version and encoding :

	$doc_xml->version = '1.0'; 
	$doc_xml->encoding = 'ISO-8859-1';



	$parameters_group = $doc_xml->createElement("parameters_group");		//  This instruction creates the root element and associates it to the XML document .
	$doc_xml->appendChild($parameters_group);								//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .


	$item = $doc_xml->createElement("item");						//  This instruction creates the "item" element and associates it to the XML document .
	$parameters_group->appendChild($item);						//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .
	$item->setAttribute('flashUrlRedirection', trim($flash_url_redirection));


	for ( $i = 0; $i <= ( $total_blocks - 1 ); $i++ ) {

		$item = $doc_xml->createElement("item");					//  This instruction creates the "item" element which contains a parameters data and associates it to the XML document .
		$parameters_group->appendChild($item);					//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .


		//   Attributes are assigned to the "item" element, knowing that each "item" element must be in the following form :
		//  <item labelName="label name" countsValue="0123456789" />

		$item->setAttribute('labelName', trim($label_name[$i]));
		$item->setAttribute('countsValue', trim($counts_value[$i]));

	}  //  For End



	//  The instruction below improves the XML document presentation :
	$doc_xml->formatOutput = true;

	//  The instruction below displays the XML document , only on the screen :
	//  echo $doc_xml->saveXML();

	//  The instruction below saves the XML document in a file whose name is in the following form : movieclip_parameters.xml
	$xml_file_path = WP_PLUGIN_DIR."/{$plugin_dir}/component/movieclip_parameters.xml";
	$doc_xml->save($xml_file_path);












?>