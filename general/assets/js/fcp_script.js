var FCP = (function ($) {
	var elms    = {};
	var $elms   = {};
	var events  = {};
	var ajax    = {};
	var states  = {};
	var loading = {};
	var objects = {};

	function setup() {
		vex.dialog.buttons.YES.text = fcpObject.buttons.YES;
		vex.dialog.buttons.NO.text = fcpObject.buttons.NO;
		states.isRequesting = false;

		_setElements();
		_setValidations();
	}

	function _setElements() {
		elms.modal        = document.getElementById('reset-password');
		elms.form         = document.getElementById('reset-password-form');
		elms.submitButton = elms.form.querySelector('[name="submit"]');
		elms.oldPassword  = elms.form.querySelector('[name="old_password"]');
		elms.password     = elms.form.querySelector('[name="password"]');
		elms.repassword   = elms.form.querySelector('[name="repassword"]');

		$elms.modal       = $(elms.modal);
	}

	function _setValidations() {
		$(elms.form).validetta({
			validators: {
				regExp: {
					min1lowercase : {
						pattern : /[a-z]/,
						errorMessage : 'Minimaal 1 kleine letter'
					},
					min1uppercase : {
						pattern : /[A-Z]/,
						errorMessage : 'Minimaal 1 hoofdletter'
					},
					min1number : {
						pattern : /[0-9]/,
						errorMessage : 'Minimaal 1 cijfer'
					},
					min1special : {
						pattern : /[$@$!%*#?&]/,
						errorMessage : 'Minimaal 1 speciaal karakter'
					}
				}
			},
			realTime: true,
			bubblePosition: 'bottom',
			onValid: events.onValid
		}, fcpObject.errorMessages);
	}

	events.onValid = function (e) {
		e.preventDefault();
		objects.validetta = this;
		ajax.changePassword();
	};

	ajax.changePassword = function () {
		if (states.isRequesting) return;
		states.isRequesting = true;
		loading.start();

		var data = {
			action       : fcpObject.action,
			nonce        : fcpObject.nonce,
			old_password : elms.oldPassword.value,
			password     : elms.password.value,
			repassword   : elms.repassword.value
		};

		$.ajax({
			url     : fcpObject.ajaxUrl,
			type    : 'post',
			dataType: 'json',
			data    : data
		}).done(function (r) {
			_handleResponse(r);
			states.isRequesting = false;
			loading.stop();
		}).fail(function () {
			vex.dialog.alert('Request failed');
			states.isRequesting = false;
			loading.stop();
		})
	};

	function _handleResponse(r) {
		if (!r || !r.status) {
			vex.dialog.alert('Wrong request');
		} else {
			if (r.status === 'success') {
				$elms.modal.stop().fadeOut(250, function () {
					$elms.modal.remove();
				});

				vex.dialog.alert({
					unsafeMessage: r.feedback,
					afterClose: function () {
						location.reload(true);
					}
				});
			} else {
				if (r.errors) {
					r.errors.forEach(function (item, i) {
						var field = elms.form.querySelector('[name="' + item.field + '"]');
						objects.validetta.window.open.call( objects.validetta, field, item.message);
					});
				}
			}
		}
	}

	loading.start = function () {
		elms.submitButton.classList.add('is-loading');
	};

	loading.stop = function () {
		elms.submitButton.classList.remove('is-loading');
	};

	return {
		setup: setup
	};
})(jQuery);

FCP.setup();
