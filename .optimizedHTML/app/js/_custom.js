document.addEventListener("DOMContentLoaded", function() {

	jQuery('.form_search_dop-filter__control__btn').click(function (){
		jQuery('.form_search_dop-filter').slideToggle(300)
	})
	
	jQuery('.hamburger').click(function (){
		jQuery('.hamburger').toggleClass('is-active');
		jQuery('.header_info_nav').slideToggle(300)
	})

	new TabsNavigator({
		buttons: '.tab_btn',
		itemsBlock: '.adt_items',
		activeButton: 'active',
		displayBlockItem: 'tab_window__active'
	});


});


var TabsNavigator = function (element) {
	const buttons = document.querySelectorAll(element.buttons);
	const itemsBlock = document.querySelectorAll(element.itemsBlock);
	const activeButton = element.activeButton;
	const displayBlockItem = element.displayBlockItem;

	if(!itemsBlock[0]) return;
	itemsBlock[0].classList.add(displayBlockItem);

	for (let i=0; i <buttons.length;  i++){
		buttons[i].onclick = function(e){
			e.preventDefault();
			remuveClass(buttons,  activeButton);
			remuveClass(itemsBlock, displayBlockItem);
			this.classList.remove('qq');
			this.classList.add(activeButton);
			itemsBlock[i].classList.add(displayBlockItem);
		}
	}
	function remuveClass(el, className) {
		for (let x = 0; x < el.length;  x++){
			el[x].classList.remove(className);
		}
	}
};