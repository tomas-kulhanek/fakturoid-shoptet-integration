import './styles/app.scss';

import 'bootstrap';
import 'admin-lte'
import naja from 'naja';
import 'bootstrap-select';

import LiveFormValidation from 'live-form-validation-es6';

window.LiveForm = LiveFormValidation.LiveForm;
window.Nette = LiveFormValidation.Nette;

document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));
document.addEventListener('DOMContentLoaded', function () {
	window.Nette.init();
	window.LiveForm.setOptions({
		showMessageClassOnParent: false,
		controlErrorClass: 'is-invalid',
		controlValidClass: 'is-valid',
		messageErrorClass: 'error invalid-feedback',
		messageTag: 'span',
		showValid: true,
		messageErrorPrefix: '&nbsp;<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;'
	});
});

