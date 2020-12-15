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

JPluginHelper::importPlugin('djclassifieds');
$document = JFactory::getDocument();
$par = JComponentHelper::getParams('com_djclassifieds');
$dispatcher = JDispatcher::getInstance();

$tile_data = $dispatcher->trigger('getLeafletTileProvider', array());
$tile_data = isset($tile_data[0]) && is_array($tile_data[0]) ? $tile_data[0] : $tile_data;

if(empty($tile_data)){
    die('no Leaflet tile data found');
}

$layout = $params->get('layout', 'default');
if($layout == 'cluster'){
    $document->addStyleSheet('https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css');
    $document->addStyleSheet('https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css');
    $document->addScript('https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js');
}

$icon_img = ''; 
$icon_size = ['',''];
if($par->get('gm_icon',1)==1 && file_exists(JPATH_ROOT.'/images/djcf_gmicon.png')){
    $icon_size = getimagesize(JPATH_ROOT.'/images/djcf_gmicon.png');
    $icon_img = JURI::base()."images/djcf_gmicon.png";
}elseif($par->get('gm_icon',1)==1){ 
    $icon_size = getimagesize(JPATH_ROOT.'/components/com_djclassifieds/assets/images/djcf_gmicon.png');
    $icon_img = JURI::base()."components/com_djclassifieds/assets/images/djcf_gmicon.png";
}

$start_address = $params->get('start_address', 'England, London');
if($start_address){
    $start_latlng = $dispatcher->trigger('leafletGetLocation', array($start_address));
    $start_latlng = isset($start_latlng[0]) && is_array($start_latlng[0]) ? $start_latlng[0] : $start_latlng;
}

if($params->get('enable_places_search', '')){
    $document->addScript(JURI::base(true).'/plugins/djclassifieds/leaflet/assets/js/poi.jquery.js');
    $document->addStyleSheet(JURI::base(true).'/plugins/djclassifieds/leaflet/assets/css/poi.css');
}

?>

<div class="dj_cf_maps">
    <?php if($params->get('enable_places_search', '')){ ?>
		<div class="djmod_map_places_search">
			 <span id="user_pos<?php echo $module->id;?>" class="user_pos"></span>
			 <input id="pac-input<?php echo $module->id;?>" class="controls pac-input" type="text" placeholder="<?php echo JText::_('MOD_DJCLASSIFIEDS_MAPS_ENTER_LOCATION');?>">			  
			<div class="clear_both"></div>
		</div>
	<?php } ?>
    <div id='djmod_map<?php echo $module->id;?>' class="djmod_map" style='width: <?php echo $params->get('map_width');?>; height: <?php echo $params->get('map_height');?>; border: 1px solid #666; '>						  
    </div>
</div>

<script>

    var map<?php echo $module->id; ?>;

    jQuery(document).ready(function(){

        var zoom = <?php echo $params->get('start_zoom','10'); ?>;
        if('<?php echo $layout; ?>' == 'cluster'){
            var markers = new L.markerClusterGroup();
        }else{
            var markers = new L.featureGroup();
        }

        var scrollWheelZoom = <?php echo $params->get('enable_scrolling', 'true'); ?>;
        var zoomControl = <?php echo $params->get('enable_zoom', 'true'); ?>;

        var mapOptions = {
            center: new L.LatLng('<?php echo !empty($start_latlng['lat']) ? $start_latlng['lat'] : ''; ?>', '<?php echo !empty($start_latlng['lng']) ? $start_latlng['lng'] : ''; ?>'),
            zoom: zoom,
            scrollWheelZoom: scrollWheelZoom,
            zoomControl: zoomControl
        };

        map<?php echo $module->id;?> = new L.Map('djmod_map<?php echo $module->id;?>', mapOptions);

        var markers_count = 0;

        L.tileLayer("<?php echo $tile_data[0]; ?>", 
            <?php echo json_encode($tile_data[1]); ?>
        ).addTo(map<?php echo $module->id;?>);

        <?php foreach($items as $item){ ?>

            <?php 
                if($par->get('gm_icon',1)==1 && file_exists(JPATH_ROOT.'/images/djcf_gmicon_'.$item->cat_id.'.png')){ 
                    $icon_size = getimagesize(JPATH_ROOT.'/images/djcf_gmicon_'.$item->cat_id.'.png');
                    $icon_img = JURI::base().'images/djcf_gmicon_'.$item->cat_id.'.png';             		
                }
            ?>

            var myIcon = L.icon({
                iconUrl: '<?php echo $icon_img; ?>',
                iconSize: ['<?php echo $icon_size[0]; ?>', '<?php echo $icon_size[1]; ?>'],
                iconAnchor: <?php echo $icon_img ? '['.($icon_size[0]/2).', '.$icon_size[1].']' : 'null'; ?>
            });

            <?php
                $marker_txt = '<div style="width:200px">';
                $marker_txt .= '<a style="text-decoration:none !important;" href="'.JRoute::_(DJClassifiedsSEO::getItemRoute($item->id.':'.$item->alias,$item->cat_id.':'.$item->c_alias,$item->region_id.':'.$item->r_name)).' ">';
                    if(count($item->images)){																		
                        $marker_txt .= '<img style="margin: 0 5px 5px 0" width="60px" src="'.JURI::base().$item->images[0]->thumb_s.'" /> ';									
                    }									
                    $marker_txt .= '<strong>'.addslashes($item->name).'</strong><br />';
                    $marker_txt .= '<span style="color:#333333">'.addslashes(str_replace(array("\n","\r","\r\n"), '',$item->intro_desc)).'</span>';
                $marker_txt .='</a>';
                $marker_txt .='</div>';
            ?>

            var lat = '<?php echo $item->latitude; ?>';
            var lng = '<?php echo $item->longitude; ?>';

            var marker = new L.Marker([lat, lng]<?php if($icon_img) echo ', {icon: myIcon}'; ?>);
            marker.bindPopup('<?php echo $marker_txt; ?>'<?php echo ($icon_img ? ', {offset: L.point(0, -'.($icon_size[1]/2).')}' : ''); ?>);
            //map<?php echo $module->id;?>.addLayer(marker);

            markers.addLayer(marker);
            markers_count++;

        <?php } ?>

        if(markers_count){
            map<?php echo $module->id;?>.addLayer(markers);
            <?php if($params->get('fit_to_items', '0')){ ?>
                map<?php echo $module->id;?>.fitBounds(markers.getBounds());
                map<?php echo $module->id;?>.zoomOut(1, {animate: false});
            <?php } ?>
        }

        <?php if($params->get('start_geoloc','0')==1){ ?>
                if(navigator.geolocation){
                    navigator.geolocation.getCurrentPosition(modSearchShowDJPosition<?php echo $module->id;?>,
                        function(error){
                            console.log(error);         			
                    }, {
                        timeout: 30000, enableHighAccuracy: true, maximumAge: 90000
                    });
                } 	
                function modSearchShowDJPosition<?php echo $module->id;?>(position){
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    map<?php echo $module->id;?>.panTo(new L.LatLng(lat, lng));
                }
        <?php } ?>

        <?php if($params->get('enable_places_search', '')){ ?>

                jQuery('#user_pos<?php echo $module->id;?>').click(function(){					
                    if(navigator.geolocation){
                        navigator.geolocation.getCurrentPosition(modSearchShowDJPosition2<?php echo $module->id;?>);
                    }
				});
				 
				function modSearchShowDJPosition2<?php echo $module->id;?>(position){
				  	var exdate=new Date();
				  	exdate.setDate(exdate.getDate() + 1);
					var ll = position.coords.latitude+'_'+position.coords.longitude;
                    document.cookie = "djcf_latlon=" + ll + "; expires=" + exdate.toUTCString();

                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    map<?php echo $module->id;?>.panTo(new L.LatLng(lat, lng));			  	
			  	}
		<?php }?>

    });

</script>

