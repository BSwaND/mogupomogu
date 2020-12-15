jQuery(document).ready(function(){
    var char_min = 2;
    var timeout;
    var field_selector = '.dj_cf_search input[name="se_address"], .dj_cf_search input[name="p_se_address"], .dj-additem #address.poi, .djmod_map_places_search input.pac-input'; //'.osm-poi';

    jQuery(field_selector).each(function(){
        $this = jQuery(this);
        //console.log($this.attr('id'));
        var style = 'position:absolute;width:'+$this.outerWidth()+'px;left:'+$this.offset().left+'px;top:'+($this.offset().top+$this.outerHeight())+'px;';
        jQuery('body').append('<div class="osm-poi-autocomplete pac-container pac-logo" style="'+style+'" data-for="'+$this.attr('id')+'"></div>');
    });

    jQuery(field_selector).keyup(function(){
        $that = jQuery(this);
        var val = jQuery(this).val();

        if(val.length <= char_min){
            hideList(jQuery(this));
            return;
        }else{
            showList(jQuery(this));

            clearTimeout(timeout);
            timeout = setTimeout(function(){ // timeout to wait for typing
                getAjaxPoi(val, $that);
            }, '200');
        }
    });

    function getAjaxPoi(val, $that){
        jQuery.ajax({
            url: 'index.php',
            type: 'post',
            dataType: 'json',
            data: {
                'option': 'com_ajax',
                'group': 'djclassifieds',
                'plugin': 'leafletGetPoi',
                'format': 'raw',
                'val': val
            }
        }).done(function (response, textStatus, jqXHR){
            if(textStatus == 'success' && response){
                showPoiRes(response, $that);
            }else{
                console.error('osm poi error');
            }
        });
    }

    jQuery(field_selector).blur(function(e){
        hideList(jQuery(this));
    }).focus(function(e){
        var val = jQuery(this).val();
        
        if(val.length <= char_min){
            return;
        }else{
            showList(jQuery(this));
        }
    });

    function hideList($el){
        jQuery('.osm-poi-autocomplete[data-for="'+$el.attr('id')+'"]').hide();
    }
    function showList($el){
        var $list = jQuery('.osm-poi-autocomplete[data-for="'+$el.attr('id')+'"]');
        recalculatePosition($el, $list); // in case DOM has changed
        $list.show();
    }

    function recalculatePosition($el, $list){
        $list.css('left', $el.offset().left+'px');
        $list.css('top', ($el.offset().top+$el.outerHeight())+'px');
    }

    function showPoiRes(resp, $that){
        //console.log($that);
        var html = '';
        jQuery(resp.features).each(function(i, item){
            //console.log(item);
            var name = item.properties.name ? item.properties.name : '';
            var osm_value = item.properties.osm_value;
            var country = item.properties.country;
            var lat = item.geometry.coordinates[1];
            var lng = item.geometry.coordinates[0];

            var prop_to_omit = ['name','extent','osm_id','osm_key','osm_type','osm_value','country'];
            var desc_arr = [];
            for(var prop in item.properties){
                if(prop_to_omit.indexOf(prop)==-1){
                    desc_arr.push(item.properties[prop]);
                }
            }
            if(country){ // country first
                desc_arr.push(country);
            }
            var desc = desc_arr.join(', ');

            if(!name){
                name = desc;
                desc = '';
            }

            html += '<div class="pac-item" data-lat="'+lat+'" data-lng="'+lng+'" title="'+desc+' ('+osm_value+')'+'"><span class="pac-icon pac-icon-marker"></span><span class="pac-item-query"><span class="name">'+name+'</span></span><span>'+desc+'</span></div>';
        });

        jQuery('.osm-poi-autocomplete[data-for="'+$that.attr('id')+'"]').empty().append(html);

        jQuery('.osm-poi-autocomplete[data-for="'+$that.attr('id')+'"] .pac-item').mousedown(function(){
            var lat = jQuery(this).attr('data-lat');
            var lng = jQuery(this).attr('data-lng');
            $that.val(jQuery(this).find('.name').text());
            handlePoiAction($that, lat, lng)
        });

        function handlePoiAction($field, lat, lng){
            if($field.attr('id').indexOf('se_address') != -1){ // search module address
                //$field.val(jQuery(this).find('.name').text());
                $field.trigger('change'); // djajax plugin support
                $field.parent().find('input[name="se_lat"]').val(lat);
                $field.parent().find('input[name="se_lng"]').val(lng);
            }else if($field.attr('id').indexOf('pac-input') != -1){ // maps module
                var mod_id = $field.attr('id').match(/\d+/)[0];
                var map = window['map'+mod_id];
                if(map){
                    map.panTo(new L.LatLng(lat, lng));
                    map.setZoom(10);
                    //map.zoomOut(1, {animate: false});
                } 
            }else if($field.attr('id') == 'address'){ // add item view
                var map = window['map'];
                var marker = window['marker'];
                var my_lat = window['my_lat'];
                var my_lng = window['my_lng'];
                coord = new L.LatLng(lat, lng);
                if(map){
                    map.panTo(coord);
                    map.setZoom(10);
                }
                if(marker) marker.setLatLng(coord);
                if(my_lat) my_lat = lat;
                if(my_lng) my_lng = lng;
                jQuery('#latitude').val(lat);
                jQuery('#longitude').val(lng);

                if(jQuery("#post_code").length && !jQuery("#post_code").val()){
                    jQuery.ajax({
                        url: "index.php",
                        type: "post",
                        dataType: "json",
                        data: {
                            "option": "com_ajax",
                            "group": "djclassifieds",
                            "plugin": "leafletGetLocationReverse",
                            "format": "json",
                            "lat": lat,
                            "lng": lng
                        }
                    }).done(function (response, textStatus, jqXHR){
                        if(textStatus == "success" && response){
                            if(response.address && response.address.postcode){
                                jQuery("#post_code").val(response.address.postcode);
                            }
                        }
                    });
                }
            }
        }
    }
});

