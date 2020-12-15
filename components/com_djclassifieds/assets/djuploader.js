
/**
 * @version $Id: album.js 21 2013-11-06 08:14:17Z szymon $
 * @package DJ-MediaTools
 * @subpackage DJ-MediaTools galleryGrid layout
 * @copyright Copyright (C) 2012 DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Szymon Woronowski - szymon.woronowski@design-joomla.eu
 *
 */

function startUpload(up,files) {
	
	//up.settings.buttons.start = false;
	up.start();
	//console.log(up);
}

function injectUploaded(up,file,info,site_url,label_generator,upload_path) {
	upload_path = (typeof upload_path !== 'undefined') ?  upload_path : '/tmp/djupload';
	
	var response = JSON.decode(info.response); 
	if(response.error) {
		//console.log(file.status);
		file.status = plupload.FAILED;
		file.name += ' - ' + response.error.message;
		document.id(file.id).addClass('ui-state-error');
		document.id(file.id).getElement('td.plupload_file_name').appendText(' - ' + response.error.message);
		//up.removeFile(file);
		return false;
	}
	
	var img_caption = '';
	if(label_generator==1){
		img_caption = stripExt(file.name);
	}
	var html = '<img src="'+site_url+upload_path+'/'+file.target_name+'" alt="'+file.name+'" />';
	html += '	<div class="imgMask">';
	html += '	<input type="hidden" name="img_id[]" value="0">';
	html += '	<input type="hidden" name="img_image[]" value="'+file.target_name+';'+file.name+'">';
	html += '	<input type="hidden" name="img_rotate[]" class="input_rotate" value="0">';
	html += '	<input type="text" class="itemInput editTitle" name="img_caption[]" value="'+img_caption+'">';
	//html += '	<span class="delBtn"></span><span class="rotateBtn"></span></div>';
	html += '	<span class="delBtn"></span><span rel="'+upload_path+'/'+file.target_name+'" alt="'+file.name+'" class="rotateBtn"></span></div>';
	var item = new Element('div',{'class':'itemImage', html: html});
	initAdminItemEvents(item);
	// add uploaded image to the list and make it sortable
	item.inject(document.id('itemImages'), 'bottom');
	this.album.addItems(item);
	
	return true;
}

function injectFrontUploaded(up,file,info,site_url,label_generator,upload_path) {
	upload_path = (typeof upload_path !== 'undefined') ?  upload_path : '/tmp/djupload';
	
	var response = JSON.decode(info.response);
	
	if(!response){
		file.status = plupload.FAILED;
		file.name += " - File can't be uploaded";
		document.id(file.id).addClass('ui-state-error');
		document.id(file.id).getElement('td.plupload_file_name').appendText(" - File can't be uploaded");
		return false;
	}
	
	if(response.error) {
		//console.log(file.status);
		file.status = plupload.FAILED;
		file.name += ' - ' + response.error.message;
		document.id(file.id).addClass('ui-state-error');
		document.id(file.id).getElement('td.plupload_file_name').appendText(' - ' + response.error.message);
		//up.removeFile(file);
		return false;
	}
	
	var img_caption = '';
	if(label_generator==1){
		img_caption = stripExt(file.name);
	}	
	var html = '<img src="'+site_url+upload_path+'/'+file.target_name+'" alt="'+file.name+'" />';
	html += '	<div class="imgMask">';
	html += '	<input type="hidden" name="img_id[]" value="0">';
	html += '	<input type="hidden" name="img_image[]" value="'+file.target_name+';'+file.name+'">';
	html += '	<input type="hidden" name="img_rotate[]" class="input_rotate" value="0">';
	html += '	<input type="text" class="itemInput editTitle" name="img_caption[]" value="'+img_caption+'">';
	html += '	<span class="delBtn"></span><span rel="'+upload_path+'/'+file.target_name+'" alt="'+file.name+'" class="rotateBtn"></span></div>';
	var item = new Element('div',{'class':'itemImage', html: html});
	initItemEvents(item);
	// add uploaded image to the list and make it sortable
	item.inject(document.id('itemImages'), 'bottom');
	this.album.addItems(item);
	up.removeFile(file);
	
	 if (up.total.queued == 0)
     {
		 document.id('submit_button').set('disabled','');
     }
	
	
	return true;
}

function initItemEvents(item) {
	
	if(!item) return;
	item.getElement('.delBtn').addEvent('click',function(){
		item.set('tween',{duration:'short',transition:'expo:out'});
		item.tween('width',0);
		(function(){item.dispose();}).delay(250);
		this.deleted = item;
	});
	

	var img_rotate = 0;
	var image = item.getElement('img');
	var img_src_org =  image.getProperty('src');

	if(item.getElement('.rotateBtn')){
		item.getElement('.rotateBtn').addEvent('click',function(btn){
			img_src = this.getProperty('rel');				
			 
				console.log(img_src);
				//console.log(image);
				  var myRequest = new Request({
					    url: 'index.php',
					    method: 'post',				    
					    evalResponse: false,					
						data: {
					      'option': 'com_djclassifieds',
					      'view': 'additem',
					      'task': 'rotateImage',
						  'img_src': img_src					  
						  },
					    onSuccess: function(responseText){	
					    	image.removeProperty('src'); 
					    	img_rotate++;
							image.setProperty('src', img_src_org+'?r='+img_rotate);	 
							item.getElement('.input_rotate').set('value',img_rotate);											
							//el.innerHTML = responseText;																						
					         		 	
					    },
					    onFailure: function(xhr){
					        console.error(xhr);
					    }
					});
					myRequest.send();		
		});		
	}
	
	
	item.getElements('input').each(function(input){
		input.addEvent('focus',function(){
			item.addClass('active');
		});
		input.addEvent('blur',function(){
			item.removeClass('active');
		});
	});
}

function initAdminItemEvents(item) {
	
	if(!item) return;
	item.getElement('.delBtn').addEvent('click',function(){
		item.set('tween',{duration:'short',transition:'expo:out'});
		item.tween('width',0);
		(function(){item.dispose();}).delay(250);
		this.deleted = item;
	});
	

	var img_rotate = 0;
	var image = item.getElement('img');
	var img_src_org =  image.getProperty('src');

	if(item.getElement('.rotateBtn')){
		item.getElement('.rotateBtn').addEvent('click',function(btn){
			img_src = this.getProperty('rel');				
			 
				console.log(img_src);
				//console.log(image);
				  var myRequest = new Request({
					    url: 'index.php',
					    method: 'post',				    
					    evalResponse: false,					
						data: {
					      'option': 'com_djclassifieds',
					      'task': 'item.rotateImage',
						  'img_src': img_src					  
						  },
					    onSuccess: function(responseText){	
					    	image.removeProperty('src'); 
					    	img_rotate++;
							image.setProperty('src', img_src_org+'?r='+img_rotate);	 
							item.getElement('.input_rotate').set('value',img_rotate);	
							console.log('rotete');
							//el.innerHTML = responseText;																						
					         		 	
					    },
					    onFailure: function(xhr){
					        console.error(xhr);
					    }
					});
					myRequest.send();		
		});		
	}
	
	
	item.getElements('input').each(function(input){
		input.addEvent('focus',function(){
			item.addClass('active');
		});
		input.addEvent('blur',function(){
			item.removeClass('active');
		});
	});
}

function stripExt(filename) {
	
	var pattern = /\.[^.]+$/;
	return filename.replace(pattern, "");	
}

window.addEvent('domready', function(){

	this.album = new Sortables('itemImages',{
		clone: true,
		revert: {duration:'short',transition:'expo:out'},
		opacity: 0.3
	});
	
	if(document.getElement('.adminItemImages')){
		$$('.itemImage').each(function(item){
			initAdminItemEvents(item);
		});
	}else{
		$$('.itemImage').each(function(item){
			initItemEvents(item);
		});	
	}
	
	
});




function DJC2PlUploadStartUploadImage(up,files) {
	return DJC2PlUploadStartUpload(up, files, 'image');
}

function DJC2PlUploadStartUploadFile(up,files) {
	return DJC2PlUploadStartUpload(up, files, 'file');
}

function DJC2PlUploadStartUpload(up, files, prefix) {
	
	var wrapper = document.id('djc_uploader_'+prefix+'_items');
	var total = wrapper.getElements('.djc_uploader_item').length;
	var limit = parseInt(wrapper.getProperty('data-limit'));
	
	var limitreached = false;
	
	if (total + files.length >= limit && limit >= 0) {
		var remaining = limit - total;
		var toRemove = files.length - remaining;
		
		if (toRemove > 0 && files.length > 0){
			limitreached = true;
			for (var i = files.length-1; i >= 0; i--) {
				if (toRemove <= 0) {
					break;
				}
				up.removeFile(up.files[i]);					
				toRemove--;
			}		
		}					   				
	}
	
	if (limitreached) {
		alert(DJCatalog2UploaderVars.lang.limitreached);
	}
	
	up.start();
}

function DJCatalog2MUInitItemEvents(item) {
	
	if(!item) return;
	item.getElement('.djc_uploader_remove_btn').addEvent('click',function(){
		(function(){item.dispose();}).delay(50);
		return false;
	});
	item.getElements('input').each(function(input){
		input.addEvent('focus',function(){
			item.addClass('active');
		});
		input.addEvent('blur',function(){
			item.removeClass('active');
		});
	});
}

window.addEvent('domready', function(){
	this.DJCatalog2MUUploaders = [];
	
	var uploaders = $$('.djc_uploader');
	uploaders.each(function(element){
		id = element.id;
		if (id) {
			instance = document.id(document.body).getElement('#'+id + ' .djc_uploader_items');
			this.DJCatalog2MUUploaders[id] = new Sortables(instance,{
				clone: false,
				revert: false,
				opacity: 0.5,
				handle: '.sortable-handler'
			}); 
		}
	});
	
	$$('.djc_uploader_item').each(function(item){
		DJCatalog2MUInitItemEvents(item);
	});
});

