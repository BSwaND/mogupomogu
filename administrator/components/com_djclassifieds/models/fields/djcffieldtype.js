/**
 * @version 3.x
 * @package DJ-Catalog2
 * @copyright Copyright (C) 2013 DJ-Extensions.com, All rights reserved.
 * @license DJ-Extensions.com Proprietary Use License
 * @author url: http://dj-extensions.com
 * @author email contact@dj-extensions.com
 * @developer Michał Olczyk michal.olczyk@design-joomla.eu
 *
 */

var Djfieldtype = function(fieldtype, typeselector, fieldId, wrapperId, fieldName) {
	return this.initialize(fieldtype, typeselector, fieldId, wrapperId, fieldName);
};

(function ($){
Djfieldtype.prototype = {
		initialize : function(fieldtype, typeselector, fieldId, wrapperId, fieldName) {
			this.typeSelector = typeselector;
			this.fieldId = fieldId;
			//this.formWrapper = $('#DjcffieldOptions');
			this.formWrapper = $('#'+wrapperId);
			this.wrapperId = wrapperId;
			this.fieldName = fieldName;
			this.fieldtype = fieldtype;
			this.displayForm();
			
			var self = this;
			
			$('#type').change(function(evt) {
				self.changeType($(this).val());
			});
			self.changeType($('#type').val());
			
			$('#search_type').change(function(evt) {
				self.changeSearchType($(this).val());
			});
			self.changeSearchType($('#search_type').val());
			
			if (typeof ($('#' + this.typeSelector)) !== 'undefined') {
				
				$('#' + this.typeSelector).change(function(evt) {
					self.fieldtype = $('#' + self.typeSelector).val();
					self.displayForm();

				});
			}
		},
		displayForm : function() {
			var self = this;
			if (typeof (this.formWrapper) !== 'undefined') {
									
				var rows = this.formWrapper.find('tr');
				
				rows.each(function(ind, el){
					var row = $(el);
					
					row.on('moveDown', function(){
						self.moveDown(row);
					});
					row.on('moveUp', function(){
						self.moveUp(row);
					});
					
					var button = $(el).find('span.button-x');
					button.on('click', function(){
						row.remove();
					});
					
					var buttonDown = $(el).find('span.button-down');
					buttonDown.on('click', function(){
						row.trigger('moveDown');
					});
					
					var buttonUp = $(el).find('span.button-up');
					buttonUp.on('click', function(){
						row.trigger('moveUp');
					});
				});
							
						
			}
			
			/*var DjcffieldOptionsOuter = $('#'+this.wrapperId+'Outer');
			
			this.fieldtype = $('#type').val();
			
			if (!this.fieldtype || this.fieldtype =='empty') {
					DjcffieldOptionsOuter.css('display','none');
				
			} else {
				 if (this.fieldtype == 'select' || this.fieldtype == 'selectlist' || this.fieldtype == 'checkbox' || this.fieldtype == 'radio') {
					 DjcffieldOptionsOuter.css('display','block');					
				} else {					
					DjcffieldOptionsOuter.css('display','none');
				}
			}*/
		},
		appendOption : function() {
			var self = this;
			if (typeof ($('#'+this.wrapperId)) !== 'undefined') {
				var optionInput = $('<input />');
				var optionId = $('<input />');
				var optionPosition = $('<input />');
				
				var deleteButton = $('<span />');
				var upButton = $('<span />');
				var downButton = $('<span />');
				
				optionInput.attr('name', this.fieldName+'[option][]');
				optionInput.attr('type', 'text');
				optionInput.attr('class', 'input-medium required');
				
				var inputs = this.formWrapper.find('input');				
				var maxPos = 0;
				inputs.each(function(ind, el) {
					el = $(el);
					if (el.attr('name') == self.fieldName+'[position][]') {
						if (maxPos < parseInt(el.val())) {
							maxPos = parseInt(el.val());
						}
					}
				});
				
				optionPosition.attr('name', this.fieldName+'[position][]');
				optionPosition.attr('type', 'text');
				optionPosition.attr('size', '4');
				optionPosition.attr('class', 'input-mini');
				optionPosition.attr('value', parseInt(maxPos+1));
				
				optionId.attr('name', this.fieldName+'[id][]');
				optionId.attr('type', 'hidden');
				optionId.attr('value', '0');
				
				deleteButton.attr('class','btn button-x btn-mini');
				deleteButton.html('&nbsp;&nbsp;&minus;&nbsp;&nbsp;');
				
				downButton.attr('class','btn button-down btn-mini');
				downButton.html('&nbsp;&nbsp;&darr;&nbsp;&nbsp;');
				
				upButton.attr('class','btn button-up btn-mini');
				upButton.html('&nbsp;&nbsp;&nbsp;&uarr;&nbsp;&nbsp;&nbsp;');
				
				
				var optionInputCell = $('<td />');
				optionInputCell.append(optionId);
				optionInputCell.append(optionInput);
				
				var optionPositionCell = $('<td />');
				optionPositionCell.append(optionPosition);
				optionPositionCell.append(deleteButton);
				optionPositionCell.append(downButton);
				optionPositionCell.append(upButton);
				
				
				var optionRow = $('<tr />');
				optionRow.append(optionInputCell);				
				optionRow.append(optionPositionCell);
				
				deleteButton.on('click', function(){
					optionRow.remove();
				});
				
				downButton.on('click', function(){
					optionRow.trigger('moveDown');
				});
				
				upButton.on('click', function(){
					optionRow.trigger('moveUp');
				});
				
				optionRow.on('moveDown',function(){
					self.moveDown(optionRow);
				});
				optionRow.on('moveUp', function(){
					self.moveUp(optionRow);
				});
									
				$('#'+this.wrapperId).append(optionRow);
			}
		},
		moveDown:function(row) {
			var self = this;
			var tbody = $('#'+this.wrapperId);
			var rows = this.formWrapper.find('tbody tr');
			var count = rows.length;
			console.log(rows);
			rows.each(function(ind, el){
				if ($(row).is(el) && ind < count - 1) {
					//self.switchRows(row, rows[ind+1]);
					var tempOrder = $(row).find('input[name="'+this.fieldName+'[position][]"]').val();
					var newOrder = $(rows[ind+1]).find('input[name="'+this.fieldName+'[position][]"]').val();
					$(row).find('input[name="'+this.fieldName+'[position][]"]').val(newOrder);
					$(rows[ind+1]).find('input[name="'+this.fieldName+'[position][]"]').val(tempOrder);
					$(row).before($(rows[ind+1]));
				}
			});
		},
		moveUp:function(row) {
			var self = this;
			var tbody = $('#'+this.wrapperId);
			var rows = this.formWrapper.find('tbody tr');
			var count = rows.length;
			console.log(rows);
			rows.each(function(ind, el){
				if ($(row).is(el) && ind > 0) {
					//self.switchRows(row, rows[ind-1]);
					var tempOrder = $(row).find('input[name="'+this.fieldName+'[position][]"]').val();
					var newOrder = $(rows[ind-1]).find('input[name="'+this.fieldName+'[position][]"]').val();
					$(row).find('input[name="'+this.fieldName+'[position][]"]').val(newOrder);
					$(rows[ind-1]).find('input[name="'+this.fieldName+'[position][]"]').val(tempOrder);
					
					$(row).after($(rows[ind-1]));
				}
			});
		},
		changeType:function(type) {			
			var field_options_outer = $('#DjcffieldOptionsOuter');			
			if (type == 'select' || type == 'selectlist' || type == 'checkbox' || type == 'radio') {
				field_options_outer.css('display','block');
			} else {					
				field_options_outer.css('display','none');					
			}
		},
		changeSearchType:function(type) {			
			var field_options_outer = $('#DjcfsearchOptionsOuter');
			var field_options_outer2 = $('#DjcfsearchOptions2Outer');	
			
			if (type == 'select' || type == 'selectlist' || type == 'checkbox' || type == 'radio' || type == 'checkbox_accordion_o' || type == 'checkbox_accordion_c') {
				field_options_outer.css('display','block');
				field_options_outer2.css('display','none');	
			}else if (type == 'select_min_max' ) {
				field_options_outer.css('display','block');
				field_options_outer2.css('display','block');
			} else {					
				field_options_outer.css('display','none');
				field_options_outer2.css('display','none');
			}
		}
		/*,
		switchRows : function(row1, row2) {
			var inputs1 = $(row1).find('input');
			var inputs2 = $(row2).find('input');
			if (inputs1.length == inputs2.length) {
				for (var i=0; i < inputs1.length; i++) {
					if ($(inputs1[i]).attr('name') != 'fieldtype[position][]'){
						var temp = $(inputs1[i]).val();
						$(inputs1[i]).val($(inputs2[i]).val());
						$(inputs2[i]).val(temp);
					}
				}
			}
		}*/
};
})(jQuery);
