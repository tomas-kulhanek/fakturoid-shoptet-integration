import './styles/app.scss';

import 'bootstrap';
import 'admin-lte'
import 'bootstrap-datepicker/js/bootstrap-datepicker';
import naja from 'naja';
import 'bootstrap-select';

import LiveFormValidation from 'live-form-validation-es6';

window.LiveForm = LiveFormValidation.LiveForm;
window.Nette = LiveFormValidation.Nette;

document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));
document.addEventListener('DOMContentLoaded', function () {
	window.Nette.init();
	window.LiveForm.setOptions({
		controlErrorClass: 'is-invalid',
		controlValidClass: 'is-valid',
		messageErrorClass: 'error invalid-feedback',
		messageTag: 'span',
		showValid: true,
		messageErrorPrefix: '&nbsp;<i class="fa fa-exclamation-circle" aria-hidden="true"></i>&nbsp;'
	});
	$('*[clipboard-copy-target-id]').on('click', function () {
		let eleId = $(this).attr('clipboard-copy-target-id');
		copyTextToClipboard($('#' + eleId).text());
	})
});

function fallbackCopyTextToClipboard(text) {
	var textArea = document.createElement("textarea");
	textArea.value = text;

	// Avoid scrolling to bottom
	textArea.style.top = "0";
	textArea.style.left = "0";
	textArea.style.position = "fixed";

	document.body.appendChild(textArea);
	textArea.focus();
	textArea.select();

	try {
		var successful = document.execCommand('copy');
		var msg = successful ? 'successful' : 'unsuccessful';
		console.log('Fallback: Copying text command was ' + msg);
	} catch (err) {
		console.error('Fallback: Oops, unable to copy', err);
	}

	document.body.removeChild(textArea);
}

function copyTextToClipboard(text) {
	if (!navigator.clipboard) {
		fallbackCopyTextToClipboard(text);
		return;
	}
	navigator.clipboard.writeText(text).then(function () {
		console.log('Async: Copying to clipboard was successful!');
	}, function (err) {
		console.error('Async: Could not copy text: ', err);
	});
}
