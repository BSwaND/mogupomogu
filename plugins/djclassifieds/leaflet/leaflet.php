<?php
/**
 * @package DJ-Classifieds
 * @copyright Copyright (C) DJ-Extensions.com, All rights reserved.
 * @license http://www.gnu.org/licenses GNU/GPL
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 *
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

class plgDJClassifiedsLeaflet extends JPlugin {
	public function __construct(&$subject, $config) {
		parent::__construct ( $subject, $config );
		$this->loadLanguage ();
	}

	public function onIncludeMapsScripts(&$load_gm_script){	
	 	$document = JFactory::getDocument();

		$ver = $this->params->get('ver','1.3.4');
		$document->addStyleSheet('https://unpkg.com/leaflet@'.$ver.'/dist/leaflet.css');
		$document->addScript('https://unpkg.com/leaflet@'.$ver.'/dist/leaflet.js');
		
		$load_gm_script = 0;
		return true;
	}
	
	function onGeocoderGetLocation(&$use_gm, $address) {
		$use_gm = 0;		
		return $this->leafletGetLocation($address);
	}

	function onGeocoderGetLocationPostCode(&$use_gm, $post_code, $address) {
		$use_gm = 0;
		return $this->leafletGetLocation($address, $post_code);
	}

	function onGeocoderGetAddressLatLon(&$use_gm, $latlng) {
		$use_gm = 0;
		$latlng_arr = explode(',', $latlng);
		$resp = $this->leafletGetLocationReverse($latlng_arr[0], $latlng_arr[1]);

		if(isset($resp['display_name'])){
			return $resp['display_name'];
		}elseif(isset($resp['error'])){
			return $resp['error'];
		}else{
            return null;
        }
	}

	/*
	public function onItemEditForm($item, $par ,$subscr_id, $promotions, $categories, $types) {
		//$par->set('allow_user_lat_lng','0');
		return null;
	}
	*/
	public function onItemEditFormSections($item, $par, $subscr_id) {
		if($par->get('allow_user_lat_lng','0') == '0'){
			return null;
		}
		$isnew = !$item->id ? true : false;
		return $this->scriptMap($item, $par, $isnew);
	}

	/*
	public function onProfileEditFormSections($user, $custom_fields, $custom_values_c, $profile_image, $par){
		$par->set('allow_user_lat_lng','0');
		return null;
	}
	*/
	public function onProfileEditFormSections($profile, $custom_fields, $custom_values_c, $profile_image, $par){
		if($par->get('allow_user_lat_lng','0') == '0' || $par->get('profile_regions','0') == '0'){
			return null;
		}
		$isnew = false;
		return $this->scriptMap($profile, $par, $isnew);
	}

	public function onUserRegistrationForm(){
		$par = JComponentHelper::getParams('com_djclassifieds');
		if($par->get('allow_user_lat_lng','0') == '0' || $par->get('registration_regions','0') == '0'){
			return null;
		}
		$isnew = true;
		return $this->scriptMap(null, $par, $isnew);
	}

	function scriptMap($item, $par, $isnew){
		$document = JFactory::getDocument();
		if($par->get('places_in_address','0')){
			$document->addScript(JURI::base(true).'/plugins/djclassifieds/leaflet/assets/js/poi.jquery.js');
			$document->addStyleSheet(JURI::base(true).'/plugins/djclassifieds/leaflet/assets/css/poi.css');
		}

		$tile_data = $this->getLeafletTileProvider();
		$tile_data = isset($tile_data[0]) && is_array($tile_data[0]) ? $tile_data[0] : $tile_data;

		$lat = '';
		$lng = '';
		if(!empty($item->latitude) && !empty($item->longitude) && $item->latitude != '0.000000000000000' && $item->longitude != '0.000000000000000'){
			$lat = $item->latitude;
			$lng = $item->longitude;
		}else if(isset($_COOKIE["djcf_latlon"])) {
			$lat_lon = explode('_', $_COOKIE["djcf_latlon"]);
			$lat = $lat_lon[0];
			$lng = $lat_lon[1];
		}else{
			$loc_coord = $this->leafletGetLocation($par->get('map_lat_lng_address','England, London'));
			if($loc_coord){
				$lat = $loc_coord['lat'];
				$lng = $loc_coord['lng'];
			}
		}
		
		$content = '<script>
			var map;
			var marker;
			var my_lat;
			var my_lng;

			jQuery(document).ready(function(){
				my_lat = "'.$lat.'";
				my_lng = "'.$lng.'";
				var zoom = "'.$par->get('gm_zoom','10').'";
				var scrollWheelZoom = "'.($par->get('gm_scrollwheel','1') ? true : false).'";

				var mapOptions = {
					center: new L.LatLng(my_lat, my_lng),
					scrollWheelZoom: scrollWheelZoom,
					zoom: zoom
				};

				map = new L.Map("djmap", mapOptions);
				L.tileLayer("'.$tile_data[0].'", '.json_encode($tile_data[1]).').addTo(map);

				marker = new L.Marker([my_lat, my_lng], {draggable: true, autoPan: true});
				marker.on("moveend",function(e){
					my_lat = e.target._latlng.lat;
					my_lng = e.target._latlng.lng;
					jQuery("#latitude").val(my_lat);
					jQuery("#longitude").val(my_lng);
					// coord = new L.LatLng(my_lat, my_lng);
					// map.panTo(coord);
				});
				map.addLayer(marker);

				jQuery("#latitude").change(function(){
					my_lat = jQuery(this).val();
					coord = new L.LatLng(my_lat, my_lng);
					marker.setLatLng(coord);
					map.panTo(coord);
				});
				jQuery("#longitude").change(function(){
					my_lng = jQuery(this).val();
					coord = new L.LatLng(my_lat, my_lng);
					marker.setLatLng(coord);
					map.panTo(coord);
				});

				jQuery("#map_use_my_location").click(function(){
					if(navigator.geolocation){
						navigator.geolocation.getCurrentPosition(showDJPosition);
					}else{
						console.error("'.JText::_('COM_DJCLASSIFIEDS_GEOLOCATION_IS_NOT_SUPPORTED_BY_THIS_BROWSER').'");
					}
				});

				function showDJPosition(position){
					var exdate=new Date();
					exdate.setDate(exdate.getDate() + 1);
					var ll = position.coords.latitude+"_"+position.coords.longitude;
					document.cookie = "djcf_latlon=" + ll + "; expires=" + exdate.toUTCString();
					my_lat = position.coords.latitude;
					my_lng = position.coords.longitude;				
					jQuery("#latitude").val(my_lat);
					jQuery("#longitude").val(my_lng);
					coord = new L.LatLng(my_lat, my_lng);
					marker.setLatLng(coord);
					map.panTo(coord);

					jQuery.ajax({
						url: "index.php",
						type: "post",
						dataType: "json",
						data: {
							"option": "com_ajax",
							"group": "djclassifieds",
							"plugin": "leafletGetLocationReverse",
							"format": "json",
							"lat": my_lat,
							"lng": my_lng
						}
					}).done(function (response, textStatus, jqXHR){
						if(textStatus == "success" && response){
							if(response.display_name && jQuery("#address").length && !jQuery("#address").val()){
								jQuery("#address").val(response.display_name);
							}
							if(response.address && response.address.postcode && jQuery("#post_code").length && !jQuery("#post_code").val()){
								jQuery("#post_code").val(response.address.postcode);
							}
						}
					});
				}

				jQuery("#map_update_latlng").click(function(){
					updateLatLngFromAddress();
				});

				if("'.$isnew.'"){
					jQuery("#address").change(function(){
						updateLatLngFromAddress();
					});
				}

				function updateLatLngFromAddress(){
					var address_arr = [];
					jQuery("[name=\"regions[]\"]").each(function(){
						if(jQuery(this).val()){
							address_arr.push(jQuery(this).find("option:selected").text());
						}
					});
					if(jQuery("#address").val()){
						address_arr.push(jQuery("#address").val());
					}

					var address = address_arr.join(", ");
					var postcode = jQuery("post_code").val();

					jQuery.ajax({
						url: "index.php",
						type: "post",
						dataType: "json",
						data: {
							"option": "com_ajax",
							"group": "djclassifieds",
							"plugin": "leafletGetLocation",
							"format": "json",
							"address": address,
							"postcode": postcode
						}
					}).done(function (response, textStatus, jqXHR){
						if(textStatus == "success" && response){
							my_lat = response.lat;
							my_lng = response.lng;
							jQuery("#latitude").val(my_lat);
							jQuery("#longitude").val(my_lng);
							coord = new L.LatLng(my_lat, my_lng);
							marker.setLatLng(coord);
							map.panTo(coord);
						}else{
							jQuery("#mapalert").show();
							(function() {
								jQuery("#mapalert").hide();
							}).delay(5000); 
						}
					});
			  	}
			});

		</script>';

		return $content;
	}

	public function onPrepareDJClassifiedsSearchModule($params, $mod){
		$document = JFactory::getDocument();
		if($params->get('show_address','0')){
			$document->addScript(JURI::base(true).'/plugins/djclassifieds/leaflet/assets/js/poi.jquery.js');
			$document->addStyleSheet(JURI::base(true).'/plugins/djclassifieds/leaflet/assets/css/poi.css');
		}
	}

	public function onBeforeDJClassifiedsDisplayAdvertMap($item, $par, $view_type){
		$app = JFactory::getApplication();
		if(!$par->get('show_googlemap',1) && !$app->isAdmin()){
			return;
		}
		$par->set('show_googlemap',0);
		
		if($item->latitude=='0.000000000000000' && $item->longitude=='0.000000000000000'){
			if($app->isAdmin()){ // geocode only on front
				return;
			}

			if($address = $this->getFullAddress($item)){
				$lat_lng = $this->leafletGetLocation($address);

				if($lat_lng){
					$item->latitude = $lat_lng['lat'];
					$item->longitude = $lat_lng['lng'];
				}else{
					return false;
				}
			}else{
				return false;
			}
		}

		$tile_data = $this->getLeafletTileProvider(); //$dispatcher->trigger('getLeafletTileProvider', array());
		$tile_data = isset($tile_data[0]) && is_array($tile_data[0]) ? $tile_data[0] : $tile_data;

		if(empty($tile_data)){
			die('no Leaflet tile data found');
		}

		$use_gm = 0;
		$this->onIncludeMapsScripts($use_gm);

		$icon_img = ''; 
		$icon_size = ['',''];
     	if($par->get('gm_icon',1)==1 && file_exists(JPATH_ROOT.'/images/djcf_gmicon_'.$item->cat_id.'.png')){ 
     		$icon_size = getimagesize(JPATH_ROOT.'/images/djcf_gmicon_'.$item->cat_id.'.png');
     		$icon_img = JURI::base().'images/djcf_gmicon_'.$item->cat_id.'.png';             		
		}else if($par->get('gm_icon',1)==1 && file_exists(JPATH_ROOT.'/images/djcf_gmicon.png')){
			$icon_size = getimagesize(JPATH_ROOT.'/images/djcf_gmicon.png');
        	$icon_img = JURI::base()."images/djcf_gmicon.png";
        }elseif($par->get('gm_icon',1)==1){ 
        	$icon_size = getimagesize(JPATH_ROOT.'/components/com_djclassifieds/assets/images/djcf_gmicon.png');
        	$icon_img = JURI::base()."components/com_djclassifieds/assets/images/djcf_gmicon.png";
		}
		
		$img_on_start_html = '';
		if($par->get('gm_img_on_start', 0) && !$app->isAdmin()){
			$img_on_start_html = '<div class="show_map_outer"><img src="components/com_djclassifieds/assets/images/map_blank.png" alt="" /><button type="button" id="show_map_btn" class="button btn" >'.JText::_('COM_DJCLASSIFIEDS_SHOW_MAP').'</button></div>';
		}	
		$map_html = '<div id="map" style="width: 100%; max-width: 320px; height: 210px;">'.$img_on_start_html.'</div>';

		$show_lat_lng_html = '';
		if($par->get('show_lat_lng', 0) && !$app->isAdmin() && $item->latitude!='0.000000000000000' && $item->longitude!='0.000000000000000'){
			$show_lat_lng_html = '<div class="geo_coordinates">'.JText::_('COM_DJCLASSIFIEDS_GEOGRAPHIC_COORDINATES').': <span>('.rtrim($item->latitude,'0').', '.rtrim($item->longitude,'0').')</span></div>';
		}
		
		$map_info_html = '';
		if($par->get('show_lat_lng', 0) && !$app->isAdmin()){
			$map_info_html = '<div class="map_info">'.JText::_('COM_DJCLASSIFIEDS_MAP_ACCURACY').'</div>';
		}
		
		$drive_dir_html = '';
		if($par->get('show_gm_driving', 0) && !$app->isAdmin()){
			$drive_dir_html = '<form action="'.JRoute::_('index.php').'" method="post" class="gm_drive_dir" target="_blank">
				<label>'.JText::_('COM_DJCLASSIFIEDS_DRIVE_DIRECTIONS').'</label>
				<input type="hidden" name="option" value="com_ajax">
				<input type="hidden" name="group" value="djclassifieds">
				<input type="hidden" name="plugin" value="leafletDriveDirections">
				<input type="hidden" name="format" value="raw">
				<input type="hidden" name="lat" value="'.$item->latitude.'">
				<input type="hidden" name="lng" value="'.$item->longitude.'">
				<input type="text" class="inputbox" name="saddr" placeholder="'.JText::_('COM_DJCLASSIFIEDS_ENTER_ADDRESS').'">
				<input class="button" type="submit" value="'.JText::_('COM_DJCLASSIFIEDS_DIRECTIONS_SEARCH').'">						
			</form>';

			if(isset($_COOKIE["djcf_latlon"])){
				$lat_lng =  str_ireplace('_', ',', $_COOKIE["djcf_latlon"]);
				$drive_dir_html .= '<a class="gm_drive_dir_l" target="_blank" href="https://www.openstreetmap.org/directions?route='.$lat_lng.';'.$item->latitude.','.$item->longitude.'">'.JText::_('COM_DJCLASSIFIEDS_OR_USE_LOCALIZATION').'<span></span></a>';
			}else{
				$drive_dir_html .= '<span class="gm_drive_dir_l"><button class="button" onclick="getDJDriveLocation()" >'.JText::_('COM_DJCLASSIFIEDS_OR_USE_LOCALIZATION').'</button><span></span></span>';
				$drive_dir_html .= '<script>
				function getDJDriveLocation(){
					if(navigator.geolocation){
						navigator.geolocation.getCurrentPosition(showDJDrivePosition);
					}else{
						console.error("'.JText::_('COM_DJCLASSIFIEDS_GEOLOCATION_IS_NOT_SUPPORTED_BY_THIS_BROWSER').'");
					}
				}
				function showDJDrivePosition(position){
					var exdate=new Date();
					exdate.setDate(exdate.getDate() + 1);
					var ll = position.coords.latitude+"_"+position.coords.longitude;
					document.cookie = "djcf_latlon=" + ll + "; expires=" + exdate.toUTCString();					  	
					window.open("https://www.openstreetmap.org/directions?route="+position.coords.latitude+","+position.coords.longitude+";'.$item->latitude.','.$item->longitude.'");						  							  				 
				}
				</script>';
			}
		}

		$content = $map_html.$show_lat_lng_html.$map_info_html.$drive_dir_html.
		'<script>
			jQuery(document).ready(function(){
				if(jQuery("#show_map_btn").length){
					jQuery("#show_map_btn").click(function(){
						jQuery("#map").empty();
						mapInit();
					});
				}else{
					mapInit();
				}

				function mapInit(){
					var lat = "'.$item->latitude.'";
					var lng = "'.$item->longitude.'";
					var zoom = "'.$par->get('gm_zoom','10').'";
					var scrollWheelZoom = "'.($par->get('gm_scrollwheel','1') ? true : false).'";

					var myIcon = L.icon({
						iconUrl: "'.$icon_img.'",
						iconSize: ['.$icon_size[0].', '.$icon_size[1].'],
						iconAnchor: '.($icon_img ? '['.($icon_size[0]/2).', '.$icon_size[1].']' : 'null').'
					});

					var mapOptions = {
						center: new L.LatLng(lat, lng),
						scrollWheelZoom: scrollWheelZoom,
						//zoom: zoom,
						//layers: new L.TileLayer("https://a.tiles.mapbox.com/v3/mapbox.world-bright/{z}/{x}/{y}.png"),
						zoom: zoom
					};

					var map = new L.Map("map", mapOptions);

					L.tileLayer("'.$tile_data[0].'", '.json_encode($tile_data[1]).').addTo(map);

					//var marker = L.marker([lat, lng]).addTo(map);
					var marker = new L.Marker([lat, lng]'.($icon_img ? ', {icon: myIcon}' : '').');
					marker.bindPopup("<div style=\"width:200px\"><b>'.addslashes($item->name).'</b><br>'.addslashes(str_replace(array("\n","\r","\r\n"), '',$item->intro_desc)).'</div>"'.($icon_img ? ', {offset: L.point(0, -'.($icon_size[1]/2).')}' : '').');
					map.addLayer(marker);
					
					jQuery("a[href=\"#location\"]").on("shown",function(e){
						map.invalidateSize();
					});
				}
			});

		</script>';

		return $content;		
	}

	public function onBeforeDJClassifiedsDisplayProfileMap($profile, $par, $view_type){
		$item = new stdClass();
		$item->latitude = '';
		$item->longitude = '';
		$item->name = '';
		$item->intro_desc = '';

		if(is_array($profile)){ // front
			if(!empty($profile['details']->latitude) && !empty($profile['details']->longitude)){
				$item->latitude = $profile['details']->latitude;
				$item->longitude = $profile['details']->longitude;
				$item->name = $profile['name'];
				$item->intro_desc = !empty($profile['details']->address) ? $profile['details']->address : '';
			}
		}else{ // back-end
			$item->latitude = $profile->latitude;
			$item->longitude = $profile->longitude;
			$item->name = $profile->user_id;
			$item->intro_desc = $profile->address;
		}
		return $this->onBeforeDJClassifiedsDisplayAdvertMap($item, $par, $view_type);
	}

	public function getLeafletTileProvider(){
		$tile_provider = $this->params->get('tile_provider', 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
		$tile_options = $this->params->get('tile_options', '&#123;&quot;attribution&quot;: &quot;&#169; &lt;a target=&apos;_blank&apos; href=&apos;https://www.openstreetmap.org/copyright&apos;&gt;OpenStreetMap&lt;/a&gt;&quot;&#125;');
		$tile_options = json_decode($tile_options);
		//echo '<pre>';print_r($tile_options);echo '</pre>';
		return [$tile_provider, $tile_options];
	}

	function leafletGetLocation($address, $post_code = ''){
		$url = "https://nominatim.openstreetmap.org/search?format=json&q=".urlencode($address);

		if($post_code){
			$url .= "&postalcode=".urlencode($post_code);
		}

		$c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($c, CURLOPT_REFERER, JURI::root()); // nominatim requires providing HTTP REFERER
        
		$resp_json = curl_exec($c);
        curl_close($c);
			        	        
		$resp = json_decode($resp_json, true);

		if(isset($resp[0])){
			return array('lat' => $resp[0]['lat'], 'lng' => $resp[0]['lon']);
		}else{
            return null;
        }
	}

	function leafletGetLocationReverse($lat, $lng){
		$url = "https://nominatim.openstreetmap.org/reverse.php?format=json&lat=".$lat."&lon=".$lng;

		$c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($c, CURLOPT_REFERER, JURI::root()); // nominatim requires providing HTTP REFERER
        
		$resp_json = curl_exec($c);
        curl_close($c);
			        	        
		$resp = json_decode($resp_json, true);

		return $resp;
	}

	private function getFullAddress($item){
		$db = JFactory::getDBO();

		$address = '';
		if(!empty($item->region_id)){
			$select = "SELECT CONCAT_WS(', '";
			$from = "FROM #__djcf_regions r1 ";
			for($i=1; $i<5; $i++){
				$j=$i+1;
				$select .= ", r".$i.".name";
				$from .= "LEFT JOIN #__djcf_regions r".$j." ON r".$i.".parent_id=r".$j.".id AND (r".$j.".city=1 OR r".$j.".country=1) ";
			}
			$select .= ") ";
			$where = "WHERE r1.id=".$item->region_id." AND (r1.city=1 OR r1.country=1)";
			$query = $select.$from.$where;

			$db->setQuery($query);
			$location = $db->loadResult();

			$address .= $location ? $location : '';
		}
		if(!empty($item->address)){
			$address .= $address ? ', '.$item->address : $item->address;
		}
		return $address;
	}

	function onAjaxLeafletGetLocation(){
		$address = JRequest::getVar('address', '');
		$postcode = JRequest::getVar('postcode', '');
		$lat_lng = $this->leafletGetLocation($address, $postcode);
		echo json_encode($lat_lng);
		die();
	}

	function onAjaxLeafletGetLocationReverse(){
		$lat = JRequest::getVar('lat', '');
		$lng = JRequest::getVar('lng', '');
		$res = $this->leafletGetLocationReverse($lat, $lng);
		echo json_encode($res);
		die();
	}

	function onAjaxLeafletDriveDirections(){
		$app = JFactory::getApplication();
		$lat = JRequest::getVar('lat', '');
		$lng = JRequest::getVar('lng', '');
		$s_addr = JRequest::getVar('saddr', '');

		if($s_addr){
			$s_loc_coord = $this->leafletGetLocation($s_addr);
		}
		
		if(!empty($s_loc_coord) && $lat && $lng){
			$app->redirect('https://www.openstreetmap.org/directions?route='.$s_loc_coord['lat'].','.$s_loc_coord['lng'].';'.$lat.','.$lng);
		}elseif($lat && $lng){
			$app->redirect('https://www.openstreetmap.org/directions?route=0.000,0.000;'.$lat.','.$lng);
		}else{
			$app->redirect('https://www.openstreetmap.org/directions');
		}
	}

	function onAjaxLeafletGetPoi(){
		$val = JRequest::getVar('val', '');
		$url = "photon.komoot.de/api/?q=".urlencode($val)."&limit=5";

		if(isset($_COOKIE["djcf_latlon"])){
			$lat_lon = explode('_', $_COOKIE["djcf_latlon"]);
			if($lat_lon){
				$url .= "&lat=".$lat_lon[0]."&lon=".$lat_lon[1];
			}
		}

		$lang = JFactory::getLanguage();
		$tag_arr = explode('-',$lang->getTag());
		$supported_lang = array('de','en','it','fr');
		if(!empty($tag_arr[0]) && in_array($tag_arr[0], $supported_lang)){
			$url .= "&lang=".$tag_arr[0];
		}

		$c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, false);
        
		$resp_json = curl_exec($c);
		curl_close($c);
		if(JRequest::getVar('dev', '')){
			echo '<pre>';print_r(json_decode($resp_json));die();
		}
		echo $resp_json;
		die();
	}

}
