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



	jQuery('.login-box__link__out').click(function (){
		jQuery('#login-form').submit();
	})

	

//	Star Louder
	setTimeout(function (){
			let prelouder = document.querySelector('.prelouder');
			let bodyBlock = document.querySelector('body');
			//if (!prelouder) return;
			prelouder.classList.add('prelouder_none');
			bodyBlock.classList.add('body_visible');
		}
		, 200
	)


	//NOT LINK
	jQuery('.user-guest .bg-white a').click(function (e){
		if(location.pathname !='/profil-polzovatelya'
			& location.pathname != '/component/users/login'
			& location.pathname !=  '/kontakty'){
			e.preventDefault()
			alert("Для просмотра необходимо авторизоваться");
		}
	})

	
	jQuery('.popup-with-form').magnificPopup({
		type: 'inline',
		preloader: false,
		focus: '#name',
		callbacks: {
			beforeOpen: function() {
				if(jQuery(window).width() < 700) {
					this.st.focus = false;
				} else {
					this.st.focus = '#name';
				}
			}
		}
	});




	//

	jQuery('.drivers_block').click(function (){
		 jQuery(this).children('.drivers_block_text').fadeToggle(400) ;

	})

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