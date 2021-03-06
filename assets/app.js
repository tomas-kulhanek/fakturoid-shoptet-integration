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
	$('.numberLineId').on('change', function () {
		if ($(this).val().length > 0) {
			let checkboxInput = $('input.synchronize_proformaInvoices');
			checkboxInput.prop("checked", false);
		}
	});
	$('input.synchronize_proformaInvoices').on('change', function () {
		if ($(this).prop('checked') && $('.numberLineId').val().length > 0) {
			alert('Zálohové doklady nelze využívat, pokud máte jinou, než výchozí číselnou řadu.');
			$(this).prop("checked", false);
		}
	});
	$('.creditNoteNumberLineId').on('change', function () {
		let checkboxInput = $('input.synchronize_creditNotes');
		checkboxInput.prop("checked", $(this).val().length >= 1);
	});
	$('input.synchronize_creditNotes').on('change', function () {
		if ($(this).prop('checked') && $('.creditNoteNumberLineId').val().length < 1) {
			alert('Dobropisy lze synchronizovat jen v případě, že máte vyplněnou číselnou řadu pro dobropisy.');
			$(this).prop("checked", false);
		}
	});
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
