"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["datagrid"],{

/***/ "./assets/app.js":
/*!***********************!*\
  !*** ./assets/app.js ***!
  \***********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_function_bind_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.function.bind.js */ "./node_modules/core-js/modules/es.function.bind.js");
/* harmony import */ var core_js_modules_es_function_bind_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_function_bind_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _styles_app_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./styles/app.scss */ "./assets/styles/app.scss");
/* harmony import */ var bootstrap__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! bootstrap */ "./node_modules/bootstrap/dist/js/bootstrap.js");
/* harmony import */ var bootstrap__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(bootstrap__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var admin_lte__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! admin-lte */ "./node_modules/admin-lte/dist/js/adminlte.min.js");
/* harmony import */ var admin_lte__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(admin_lte__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var bootstrap_datepicker_js_bootstrap_datepicker__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! bootstrap-datepicker/js/bootstrap-datepicker */ "./node_modules/bootstrap-datepicker/js/bootstrap-datepicker.js");
/* harmony import */ var bootstrap_datepicker_js_bootstrap_datepicker__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(bootstrap_datepicker_js_bootstrap_datepicker__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var naja__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! naja */ "./node_modules/naja/dist/Naja.esm.js");
/* harmony import */ var bootstrap_select__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! bootstrap-select */ "./node_modules/bootstrap-select/dist/js/bootstrap-select.js");
/* harmony import */ var bootstrap_select__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(bootstrap_select__WEBPACK_IMPORTED_MODULE_6__);
/* harmony import */ var live_form_validation_es6__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! live-form-validation-es6 */ "./node_modules/live-form-validation-es6/live-form-validation.js");
/* harmony import */ var live_form_validation_es6__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(live_form_validation_es6__WEBPACK_IMPORTED_MODULE_7__);
/* provided dependency */ var $ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");








window.LiveForm = (live_form_validation_es6__WEBPACK_IMPORTED_MODULE_7___default().LiveForm);
window.Nette = (live_form_validation_es6__WEBPACK_IMPORTED_MODULE_7___default().Nette);
document.addEventListener('DOMContentLoaded', naja__WEBPACK_IMPORTED_MODULE_5__["default"].initialize.bind(naja__WEBPACK_IMPORTED_MODULE_5__["default"]));
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
    var eleId = $(this).attr('clipboard-copy-target-id');
    copyTextToClipboard($('#' + eleId).text());
  });
  $('.numberLineId').on('change', function () {
    if ($(this).val().length > 0) {
      var checkboxInput = $('input.synchronize_proformaInvoices');
      checkboxInput.prop("checked", false);
    }
  });
  $('input.synchronize_proformaInvoices').on('change', function () {
    if ($(this).prop('checked') && $('.numberLineId').val().length > 0) {
      alert('Zálohové doklady nelze využívat, pokud máte jinou, než výchozí číselnou řadu.');
      $(this).prop("checked", false);
    }
  });
});

function fallbackCopyTextToClipboard(text) {
  var textArea = document.createElement("textarea");
  textArea.value = text; // Avoid scrolling to bottom

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

/***/ }),

/***/ "./assets/datagrid.js":
/*!****************************!*\
  !*** ./assets/datagrid.js ***!
  \****************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! core-js/modules/es.array.find.js */ "./node_modules/core-js/modules/es.array.find.js");
/* harmony import */ var core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_array_find_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! core-js/modules/es.string.trim.js */ "./node_modules/core-js/modules/es.string.trim.js");
/* harmony import */ var core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(core_js_modules_es_string_trim_js__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _styles_datagrid_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./styles/datagrid.scss */ "./assets/styles/datagrid.scss");
/* harmony import */ var _app__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./app */ "./assets/app.js");
/* harmony import */ var ublaboo_datagrid_assets_datagrid__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ublaboo-datagrid/assets/datagrid */ "./node_modules/ublaboo-datagrid/assets/datagrid.js");
/* harmony import */ var ublaboo_datagrid_assets_datagrid__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(ublaboo_datagrid_assets_datagrid__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var ublaboo_datagrid_assets_datagrid_spinners__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ublaboo-datagrid/assets/datagrid-spinners */ "./node_modules/ublaboo-datagrid/assets/datagrid-spinners.js");
/* harmony import */ var ublaboo_datagrid_assets_datagrid_spinners__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(ublaboo_datagrid_assets_datagrid_spinners__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var ublaboo_datagrid_assets_datagrid_instant_url_refresh__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ublaboo-datagrid/assets/datagrid-instant-url-refresh */ "./node_modules/ublaboo-datagrid/assets/datagrid-instant-url-refresh.js");
/* harmony import */ var ublaboo_datagrid_assets_datagrid_instant_url_refresh__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(ublaboo_datagrid_assets_datagrid_instant_url_refresh__WEBPACK_IMPORTED_MODULE_6__);
/* provided dependency */ var $ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");







document.addEventListener('DOMContentLoaded', function () {
  $('.table-responsive-stack').each(function (i) {
    var id = $(this).attr('id');
    $(this).find("thead tr:nth-child(2) th").each(function (i) {
      var theadValue = $(this).text();

      if ($(this).find('a').length > 0) {
        theadValue = $(this).find('a').first().text();
      }

      theadValue = $.trim(theadValue);
      console.log(theadValue);

      if (theadValue.length > 0) {
        $('#' + id + ' td:nth-child(' + (i + 1) + ')').prepend('<span class="table-responsive-stack-thead">' + theadValue + ':</span> ');
      } else {
        $('#' + id + ' td:nth-child(' + (i + 1) + ')').prepend('<span class="table-responsive-stack-thead"></span> ');
      }

      $('.table-responsive-stack-thead').hide();
    });
  });
  $('.table-responsive-stack').each(function () {
    var thCount = $(this).find("th").length;
    var rowGrow = 100 / thCount + '%';
    $(this).find("th, td").css('flex-basis', rowGrow);
  });

  function flexTable() {
    if ($(window).width() < 768) {
      $(".table-responsive-stack").each(function (i) {
        $(this).find(".table-responsive-stack-thead").show();
        $(this).find('thead').hide();
      });
      $(".table-responsive-stack").addClass('table-is-responsive');
    } else {
      $(".table-responsive-stack").each(function (i) {
        $(this).find(".table-responsive-stack-thead").hide();
        $(this).find('thead').show();
      });
      $(".table-responsive-stack").removeClass('table-is-responsive');
    }
  }

  flexTable();

  window.onresize = function (event) {
    flexTable();
  };
});

/***/ }),

/***/ "./assets/styles/app.scss":
/*!********************************!*\
  !*** ./assets/styles/app.scss ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./assets/styles/datagrid.scss":
/*!*************************************!*\
  !*** ./assets/styles/datagrid.scss ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_core-js_modules_es_function_bind_js-node_modules_live-form-validation-es-11302a","vendors-node_modules_admin-lte_dist_js_adminlte_min_js-node_modules_bootstrap-datepicker_js_b-11d938","vendors-node_modules_core-js_modules_es_array_find_js-node_modules_core-js_modules_es_string_-7feadc","assets_styles_bs-stepper_css-assets_styles_app_scss"], () => (__webpack_exec__("./assets/datagrid.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QixVQUFVQyxLQUFWLEVBQWlCO0FBQ2xDTixJQUFBQSxTQUFTO0FBQ1QsR0FGRDtBQUdBLENBaEREOzs7Ozs7Ozs7OztBQ05BOzs7Ozs7Ozs7Ozs7QUNBQSIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL2Fzc2V0cy9hcHAuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2RhdGFncmlkLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9zdHlsZXMvYXBwLnNjc3MiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3N0eWxlcy9kYXRhZ3JpZC5zY3NzIl0sInNvdXJjZXNDb250ZW50IjpbImltcG9ydCAnLi9zdHlsZXMvYXBwLnNjc3MnO1xuXG5pbXBvcnQgJ2Jvb3RzdHJhcCc7XG5pbXBvcnQgJ2FkbWluLWx0ZSdcbmltcG9ydCAnYm9vdHN0cmFwLWRhdGVwaWNrZXIvanMvYm9vdHN0cmFwLWRhdGVwaWNrZXInO1xuaW1wb3J0IG5hamEgZnJvbSAnbmFqYSc7XG5pbXBvcnQgJ2Jvb3RzdHJhcC1zZWxlY3QnO1xuXG5pbXBvcnQgTGl2ZUZvcm1WYWxpZGF0aW9uIGZyb20gJ2xpdmUtZm9ybS12YWxpZGF0aW9uLWVzNic7XG5cbndpbmRvdy5MaXZlRm9ybSA9IExpdmVGb3JtVmFsaWRhdGlvbi5MaXZlRm9ybTtcbndpbmRvdy5OZXR0ZSA9IExpdmVGb3JtVmFsaWRhdGlvbi5OZXR0ZTtcblxuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignRE9NQ29udGVudExvYWRlZCcsIG5hamEuaW5pdGlhbGl6ZS5iaW5kKG5hamEpKTtcbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCBmdW5jdGlvbiAoKSB7XG5cdHdpbmRvdy5OZXR0ZS5pbml0KCk7XG5cdHdpbmRvdy5MaXZlRm9ybS5zZXRPcHRpb25zKHtcblx0XHRjb250cm9sRXJyb3JDbGFzczogJ2lzLWludmFsaWQnLFxuXHRcdGNvbnRyb2xWYWxpZENsYXNzOiAnaXMtdmFsaWQnLFxuXHRcdG1lc3NhZ2VFcnJvckNsYXNzOiAnZXJyb3IgaW52YWxpZC1mZWVkYmFjaycsXG5cdFx0bWVzc2FnZVRhZzogJ3NwYW4nLFxuXHRcdHNob3dWYWxpZDogdHJ1ZSxcblx0XHRtZXNzYWdlRXJyb3JQcmVmaXg6ICcmbmJzcDs8aSBjbGFzcz1cImZhIGZhLWV4Y2xhbWF0aW9uLWNpcmNsZVwiIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPjwvaT4mbmJzcDsnXG5cdH0pO1xuXHQkKCcqW2NsaXBib2FyZC1jb3B5LXRhcmdldC1pZF0nKS5vbignY2xpY2snLCBmdW5jdGlvbiAoKSB7XG5cdFx0bGV0IGVsZUlkID0gJCh0aGlzKS5hdHRyKCdjbGlwYm9hcmQtY29weS10YXJnZXQtaWQnKTtcblx0XHRjb3B5VGV4dFRvQ2xpcGJvYXJkKCQoJyMnICsgZWxlSWQpLnRleHQoKSk7XG5cdH0pXG5cdCQoJy5udW1iZXJMaW5lSWQnKS5vbignY2hhbmdlJywgZnVuY3Rpb24gKCkge1xuXHRcdGlmICgkKHRoaXMpLnZhbCgpLmxlbmd0aCA+IDApIHtcblx0XHRcdGxldCBjaGVja2JveElucHV0ID0gJCgnaW5wdXQuc3luY2hyb25pemVfcHJvZm9ybWFJbnZvaWNlcycpO1xuXHRcdFx0Y2hlY2tib3hJbnB1dC5wcm9wKFwiY2hlY2tlZFwiLCBmYWxzZSk7XG5cdFx0fVxuXHR9KTtcblx0JCgnaW5wdXQuc3luY2hyb25pemVfcHJvZm9ybWFJbnZvaWNlcycpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XG5cdFx0aWYgKCQodGhpcykucHJvcCgnY2hlY2tlZCcpICYmICQoJy5udW1iZXJMaW5lSWQnKS52YWwoKS5sZW5ndGggPiAwKSB7XG5cdFx0XHRhbGVydCgnWsOhbG9ob3bDqSBkb2tsYWR5IG5lbHplIHZ5dcW+w612YXQsIHBva3VkIG3DoXRlIGppbm91LCBuZcW+IHbDvWNob3rDrSDEjcOtc2Vsbm91IMWZYWR1LicpO1xuXHRcdFx0JCh0aGlzKS5wcm9wKFwiY2hlY2tlZFwiLCBmYWxzZSk7XG5cdFx0fVxuXHR9KTtcbn0pO1xuXG5mdW5jdGlvbiBmYWxsYmFja0NvcHlUZXh0VG9DbGlwYm9hcmQodGV4dCkge1xuXHR2YXIgdGV4dEFyZWEgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KFwidGV4dGFyZWFcIik7XG5cdHRleHRBcmVhLnZhbHVlID0gdGV4dDtcblxuXHQvLyBBdm9pZCBzY3JvbGxpbmcgdG8gYm90dG9tXG5cdHRleHRBcmVhLnN0eWxlLnRvcCA9IFwiMFwiO1xuXHR0ZXh0QXJlYS5zdHlsZS5sZWZ0ID0gXCIwXCI7XG5cdHRleHRBcmVhLnN0eWxlLnBvc2l0aW9uID0gXCJmaXhlZFwiO1xuXG5cdGRvY3VtZW50LmJvZHkuYXBwZW5kQ2hpbGQodGV4dEFyZWEpO1xuXHR0ZXh0QXJlYS5mb2N1cygpO1xuXHR0ZXh0QXJlYS5zZWxlY3QoKTtcblxuXHR0cnkge1xuXHRcdHZhciBzdWNjZXNzZnVsID0gZG9jdW1lbnQuZXhlY0NvbW1hbmQoJ2NvcHknKTtcblx0XHR2YXIgbXNnID0gc3VjY2Vzc2Z1bCA/ICdzdWNjZXNzZnVsJyA6ICd1bnN1Y2Nlc3NmdWwnO1xuXHRcdGNvbnNvbGUubG9nKCdGYWxsYmFjazogQ29weWluZyB0ZXh0IGNvbW1hbmQgd2FzICcgKyBtc2cpO1xuXHR9IGNhdGNoIChlcnIpIHtcblx0XHRjb25zb2xlLmVycm9yKCdGYWxsYmFjazogT29wcywgdW5hYmxlIHRvIGNvcHknLCBlcnIpO1xuXHR9XG5cblx0ZG9jdW1lbnQuYm9keS5yZW1vdmVDaGlsZCh0ZXh0QXJlYSk7XG59XG5cbmZ1bmN0aW9uIGNvcHlUZXh0VG9DbGlwYm9hcmQodGV4dCkge1xuXHRpZiAoIW5hdmlnYXRvci5jbGlwYm9hcmQpIHtcblx0XHRmYWxsYmFja0NvcHlUZXh0VG9DbGlwYm9hcmQodGV4dCk7XG5cdFx0cmV0dXJuO1xuXHR9XG5cdG5hdmlnYXRvci5jbGlwYm9hcmQud3JpdGVUZXh0KHRleHQpLnRoZW4oZnVuY3Rpb24gKCkge1xuXHRcdGNvbnNvbGUubG9nKCdBc3luYzogQ29weWluZyB0byBjbGlwYm9hcmQgd2FzIHN1Y2Nlc3NmdWwhJyk7XG5cdH0sIGZ1bmN0aW9uIChlcnIpIHtcblx0XHRjb25zb2xlLmVycm9yKCdBc3luYzogQ291bGQgbm90IGNvcHkgdGV4dDogJywgZXJyKTtcblx0fSk7XG59XG4iLCJpbXBvcnQgJy4vc3R5bGVzL2RhdGFncmlkLnNjc3MnO1xuaW1wb3J0ICcuL2FwcCc7XG5pbXBvcnQgJ3VibGFib28tZGF0YWdyaWQvYXNzZXRzL2RhdGFncmlkJztcbmltcG9ydCAndWJsYWJvby1kYXRhZ3JpZC9hc3NldHMvZGF0YWdyaWQtc3Bpbm5lcnMnO1xuaW1wb3J0ICd1YmxhYm9vLWRhdGFncmlkL2Fzc2V0cy9kYXRhZ3JpZC1pbnN0YW50LXVybC1yZWZyZXNoJztcblxuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignRE9NQ29udGVudExvYWRlZCcsIGZ1bmN0aW9uICgpIHtcblx0JCgnLnRhYmxlLXJlc3BvbnNpdmUtc3RhY2snKS5lYWNoKGZ1bmN0aW9uIChpKSB7XG5cdFx0dmFyIGlkID0gJCh0aGlzKS5hdHRyKCdpZCcpO1xuXHRcdCQodGhpcykuZmluZChcInRoZWFkIHRyOm50aC1jaGlsZCgyKSB0aFwiKS5lYWNoKGZ1bmN0aW9uIChpKSB7XG5cdFx0XHRsZXQgdGhlYWRWYWx1ZSA9ICQodGhpcykudGV4dCgpO1xuXG5cdFx0XHRpZiAoJCh0aGlzKS5maW5kKCdhJykubGVuZ3RoID4gMCkge1xuXHRcdFx0XHR0aGVhZFZhbHVlID0gJCh0aGlzKS5maW5kKCdhJykuZmlyc3QoKS50ZXh0KCk7XG5cdFx0XHR9XG5cdFx0XHR0aGVhZFZhbHVlID0gJC50cmltKHRoZWFkVmFsdWUpO1xuXHRcdFx0Y29uc29sZS5sb2codGhlYWRWYWx1ZSk7XG5cdFx0XHRpZih0aGVhZFZhbHVlLmxlbmd0aD4wKSB7XG5cdFx0XHRcdCQoJyMnICsgaWQgKyAnIHRkOm50aC1jaGlsZCgnICsgKGkgKyAxKSArICcpJykucHJlcGVuZCgnPHNwYW4gY2xhc3M9XCJ0YWJsZS1yZXNwb25zaXZlLXN0YWNrLXRoZWFkXCI+JyArIHRoZWFkVmFsdWUgKyAnOjwvc3Bhbj4gJyk7XG5cdFx0XHR9ZWxzZXtcblx0XHRcdFx0JCgnIycgKyBpZCArICcgdGQ6bnRoLWNoaWxkKCcgKyAoaSArIDEpICsgJyknKS5wcmVwZW5kKCc8c3BhbiBjbGFzcz1cInRhYmxlLXJlc3BvbnNpdmUtc3RhY2stdGhlYWRcIj48L3NwYW4+ICcpO1xuXHRcdFx0fVxuXHRcdFx0JCgnLnRhYmxlLXJlc3BvbnNpdmUtc3RhY2stdGhlYWQnKS5oaWRlKCk7XG5cblx0XHR9KTtcblx0fSk7XG5cblx0JCgnLnRhYmxlLXJlc3BvbnNpdmUtc3RhY2snKS5lYWNoKGZ1bmN0aW9uICgpIHtcblx0XHR2YXIgdGhDb3VudCA9ICQodGhpcykuZmluZChcInRoXCIpLmxlbmd0aDtcblx0XHR2YXIgcm93R3JvdyA9IDEwMCAvIHRoQ291bnQgKyAnJSc7XG5cdFx0JCh0aGlzKS5maW5kKFwidGgsIHRkXCIpLmNzcygnZmxleC1iYXNpcycsIHJvd0dyb3cpO1xuXHR9KTtcblxuXHRmdW5jdGlvbiBmbGV4VGFibGUoKSB7XG5cdFx0aWYgKCQod2luZG93KS53aWR0aCgpIDwgNzY4KSB7XG5cdFx0XHQkKFwiLnRhYmxlLXJlc3BvbnNpdmUtc3RhY2tcIikuZWFjaChmdW5jdGlvbiAoaSkge1xuXHRcdFx0XHQkKHRoaXMpLmZpbmQoXCIudGFibGUtcmVzcG9uc2l2ZS1zdGFjay10aGVhZFwiKS5zaG93KCk7XG5cdFx0XHRcdCQodGhpcykuZmluZCgndGhlYWQnKS5oaWRlKCk7XG5cdFx0XHR9KTtcblx0XHRcdCQoXCIudGFibGUtcmVzcG9uc2l2ZS1zdGFja1wiKS5hZGRDbGFzcygndGFibGUtaXMtcmVzcG9uc2l2ZScpO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHQkKFwiLnRhYmxlLXJlc3BvbnNpdmUtc3RhY2tcIikuZWFjaChmdW5jdGlvbiAoaSkge1xuXHRcdFx0XHQkKHRoaXMpLmZpbmQoXCIudGFibGUtcmVzcG9uc2l2ZS1zdGFjay10aGVhZFwiKS5oaWRlKCk7XG5cdFx0XHRcdCQodGhpcykuZmluZCgndGhlYWQnKS5zaG93KCk7XG5cdFx0XHR9KTtcblx0XHRcdCQoXCIudGFibGUtcmVzcG9uc2l2ZS1zdGFja1wiKS5yZW1vdmVDbGFzcygndGFibGUtaXMtcmVzcG9uc2l2ZScpO1xuXHRcdH1cblx0fVxuXG5cdGZsZXhUYWJsZSgpO1xuXG5cdHdpbmRvdy5vbnJlc2l6ZSA9IGZ1bmN0aW9uIChldmVudCkge1xuXHRcdGZsZXhUYWJsZSgpO1xuXHR9O1xufSk7XG5cblxuXG4iLCIvLyBleHRyYWN0ZWQgYnkgbWluaS1jc3MtZXh0cmFjdC1wbHVnaW5cbmV4cG9ydCB7fTsiLCIvLyBleHRyYWN0ZWQgYnkgbWluaS1jc3MtZXh0cmFjdC1wbHVnaW5cbmV4cG9ydCB7fTsiXSwibmFtZXMiOlsibmFqYSIsIkxpdmVGb3JtVmFsaWRhdGlvbiIsIndpbmRvdyIsIkxpdmVGb3JtIiwiTmV0dGUiLCJkb2N1bWVudCIsImFkZEV2ZW50TGlzdGVuZXIiLCJpbml0aWFsaXplIiwiYmluZCIsImluaXQiLCJzZXRPcHRpb25zIiwiY29udHJvbEVycm9yQ2xhc3MiLCJjb250cm9sVmFsaWRDbGFzcyIsIm1lc3NhZ2VFcnJvckNsYXNzIiwibWVzc2FnZVRhZyIsInNob3dWYWxpZCIsIm1lc3NhZ2VFcnJvclByZWZpeCIsIiQiLCJvbiIsImVsZUlkIiwiYXR0ciIsImNvcHlUZXh0VG9DbGlwYm9hcmQiLCJ0ZXh0IiwidmFsIiwibGVuZ3RoIiwiY2hlY2tib3hJbnB1dCIsInByb3AiLCJhbGVydCIsImZhbGxiYWNrQ29weVRleHRUb0NsaXBib2FyZCIsInRleHRBcmVhIiwiY3JlYXRlRWxlbWVudCIsInZhbHVlIiwic3R5bGUiLCJ0b3AiLCJsZWZ0IiwicG9zaXRpb24iLCJib2R5IiwiYXBwZW5kQ2hpbGQiLCJmb2N1cyIsInNlbGVjdCIsInN1Y2Nlc3NmdWwiLCJleGVjQ29tbWFuZCIsIm1zZyIsImNvbnNvbGUiLCJsb2ciLCJlcnIiLCJlcnJvciIsInJlbW92ZUNoaWxkIiwibmF2aWdhdG9yIiwiY2xpcGJvYXJkIiwid3JpdGVUZXh0IiwidGhlbiIsImVhY2giLCJpIiwiaWQiLCJmaW5kIiwidGhlYWRWYWx1ZSIsImZpcnN0IiwidHJpbSIsInByZXBlbmQiLCJoaWRlIiwidGhDb3VudCIsInJvd0dyb3ciLCJjc3MiLCJmbGV4VGFibGUiLCJ3aWR0aCIsInNob3ciLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwib25yZXNpemUiLCJldmVudCJdLCJzb3VyY2VSb290IjoiIn0=