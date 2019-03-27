document.addEventListener('DOMContentLoaded', function() {
	const fsElem = document.getElementById('js-first-screen');
	
	(function initFun() {
		if (fsElem) {
			let padTop = 100;

			if (window.innerWidth < 1200) {
				padTop = 60;
			}

			fsElem.style.height = (window.innerHeight - padTop) +'px';
		}
		
		// resize events
		window.removeEventListener('winResized', initFun);
		window.removeEventListener('winWidthResized', initFun);

		if (window.innerWidth > 1200) {
			window.addEventListener('winResized', initFun);
		} else {
			window.addEventListener('winWidthResized', initFun);
		}
	})();
	
	// toggle button
	Toggle.init('.js-toggle', '.js-document-toggle-off');
	
	Toggle.onChange = function(tgl, state) {
		
	}
	
	// popup
	Popup.init('.js-open-popup');
	MediaPopup.init('.js-open-media-popup');

	// menu
	if (window.innerWidth < 1000) {
		Menu.init('.menu__item_has-children', '.menu__sub-menu');
	}
	
	// mobile nav
	MobNav.init({
		openBtn: '.js-open-menu',
		closeBtn: '.js-close-menu',
		headerId: 'header',
		menuLinkSelector: '.menu a'
	});
	
	// // alert
	// new Alert({
	// 	content: '<div class="row alert__row row_col-middle row_sm-x-nw"><div class="col">На нашем веб-сайте используются файлы cookies, которые позволяют улучшить Ваше взаимодействие с сайтом. Когда вы посещаете данный веб-сайт, Вы даете согласие на использование файлов cookies.</div><div class="col"><button class="js-alert-close btn btn_be">Хорошо</button></div></div>',
	// 	showOnce: true
	// });
	
	// autocomplete data
	AutoComplete.setValuesData = function (val, fun) {
		fun([
			{val:"mc", value:"Mercury"},
			{val:"vn", value:"Venus"},
			{val:"eth", value:"Earth"},
			{val:"ms", value:"Mars"},
			{val:"mn", value:"Mandarin"},
			{val:"mk", value:"Marakuja"},
			{val:"mlk", value:"Milk"}
		]);
	}
	
	// submit form
	Form.init('.form');
	
	Form.onSubmit = function(form, callback) {
		switch (form.id) {
			case 'sorting-form':
			return true;
			
			case 'custom-form-2':
			case 'custom-form-3':
			case 'custom-form-4':
			var files = CustomFile.getFiles(form);
			
			console.log(files);
			return false;
			
			default:
			ajax({
				url: form.action,
				send: new FormData(form),
				success: function(response) {
					var response = JSON.parse(response);
					
					if (response.status == 'sent') {
						Popup.message('#message-popup', 'Форма отправлена');
						
						callback({clearForm: true, unlockSubmitButton: true});
					} else {
						console.log(response);
					}
				},
				error: function(response) {
					console.log(response);
				}
			});
			return false;
		}
	}
});