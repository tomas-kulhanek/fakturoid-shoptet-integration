"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["app"],{

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

/***/ "./assets/styles/app.scss":
/*!********************************!*\
  !*** ./assets/styles/app.scss ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_core-js_modules_es_function_bind_js-node_modules_live-form-validation-es-11302a","vendors-node_modules_admin-lte_dist_js_adminlte_min_js-node_modules_bootstrap-datepicker_js_b-11d938","assets_styles_bs-stepper_css-assets_styles_app_scss"], () => (__webpack_exec__("./assets/app.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJmaWxlIjoiYXBwLmpzIiwibWFwcGluZ3MiOiI7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBQUE7QUFFQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBRUE7QUFFQUUsTUFBTSxDQUFDQyxRQUFQLEdBQWtCRiwwRUFBbEI7QUFDQUMsTUFBTSxDQUFDRSxLQUFQLEdBQWVILHVFQUFmO0FBRUFJLFFBQVEsQ0FBQ0MsZ0JBQVQsQ0FBMEIsa0JBQTFCLEVBQThDTiw0REFBQSxDQUFxQkEsNENBQXJCLENBQTlDO0FBQ0FLLFFBQVEsQ0FBQ0MsZ0JBQVQsQ0FBMEIsa0JBQTFCLEVBQThDLFlBQVk7QUFDekRKLEVBQUFBLE1BQU0sQ0FBQ0UsS0FBUCxDQUFhSyxJQUFiO0FBQ0FQLEVBQUFBLE1BQU0sQ0FBQ0MsUUFBUCxDQUFnQk8sVUFBaEIsQ0FBMkI7QUFDMUJDLElBQUFBLGlCQUFpQixFQUFFLFlBRE87QUFFMUJDLElBQUFBLGlCQUFpQixFQUFFLFVBRk87QUFHMUJDLElBQUFBLGlCQUFpQixFQUFFLHdCQUhPO0FBSTFCQyxJQUFBQSxVQUFVLEVBQUUsTUFKYztBQUsxQkMsSUFBQUEsU0FBUyxFQUFFLElBTGU7QUFNMUJDLElBQUFBLGtCQUFrQixFQUFFO0FBTk0sR0FBM0I7QUFRQUMsRUFBQUEsQ0FBQyxDQUFDLDZCQUFELENBQUQsQ0FBaUNDLEVBQWpDLENBQW9DLE9BQXBDLEVBQTZDLFlBQVk7QUFDeEQsUUFBSUMsS0FBSyxHQUFHRixDQUFDLENBQUMsSUFBRCxDQUFELENBQVFHLElBQVIsQ0FBYSwwQkFBYixDQUFaO0FBQ0FDLElBQUFBLG1CQUFtQixDQUFDSixDQUFDLENBQUMsTUFBTUUsS0FBUCxDQUFELENBQWVHLElBQWYsRUFBRCxDQUFuQjtBQUNBLEdBSEQ7QUFJQUwsRUFBQUEsQ0FBQyxDQUFDLGVBQUQsQ0FBRCxDQUFtQkMsRUFBbkIsQ0FBc0IsUUFBdEIsRUFBZ0MsWUFBWTtBQUMzQyxRQUFJRCxDQUFDLENBQUMsSUFBRCxDQUFELENBQVFNLEdBQVIsR0FBY0MsTUFBZCxHQUF1QixDQUEzQixFQUE4QjtBQUM3QixVQUFJQyxhQUFhLEdBQUdSLENBQUMsQ0FBQyxvQ0FBRCxDQUFyQjtBQUNBUSxNQUFBQSxhQUFhLENBQUNDLElBQWQsQ0FBbUIsU0FBbkIsRUFBOEIsS0FBOUI7QUFDQTtBQUNELEdBTEQ7QUFNQVQsRUFBQUEsQ0FBQyxDQUFDLG9DQUFELENBQUQsQ0FBd0NDLEVBQXhDLENBQTJDLFFBQTNDLEVBQXFELFlBQVk7QUFDaEUsUUFBSUQsQ0FBQyxDQUFDLElBQUQsQ0FBRCxDQUFRUyxJQUFSLENBQWEsU0FBYixLQUEyQlQsQ0FBQyxDQUFDLGVBQUQsQ0FBRCxDQUFtQk0sR0FBbkIsR0FBeUJDLE1BQXpCLEdBQWtDLENBQWpFLEVBQW9FO0FBQ25FRyxNQUFBQSxLQUFLLENBQUMsK0VBQUQsQ0FBTDtBQUNBVixNQUFBQSxDQUFDLENBQUMsSUFBRCxDQUFELENBQVFTLElBQVIsQ0FBYSxTQUFiLEVBQXdCLEtBQXhCO0FBQ0E7QUFDRCxHQUxEO0FBTUEsQ0ExQkQ7O0FBNEJBLFNBQVNFLDJCQUFULENBQXFDTixJQUFyQyxFQUEyQztBQUMxQyxNQUFJTyxRQUFRLEdBQUd4QixRQUFRLENBQUN5QixhQUFULENBQXVCLFVBQXZCLENBQWY7QUFDQUQsRUFBQUEsUUFBUSxDQUFDRSxLQUFULEdBQWlCVCxJQUFqQixDQUYwQyxDQUkxQzs7QUFDQU8sRUFBQUEsUUFBUSxDQUFDRyxLQUFULENBQWVDLEdBQWYsR0FBcUIsR0FBckI7QUFDQUosRUFBQUEsUUFBUSxDQUFDRyxLQUFULENBQWVFLElBQWYsR0FBc0IsR0FBdEI7QUFDQUwsRUFBQUEsUUFBUSxDQUFDRyxLQUFULENBQWVHLFFBQWYsR0FBMEIsT0FBMUI7QUFFQTlCLEVBQUFBLFFBQVEsQ0FBQytCLElBQVQsQ0FBY0MsV0FBZCxDQUEwQlIsUUFBMUI7QUFDQUEsRUFBQUEsUUFBUSxDQUFDUyxLQUFUO0FBQ0FULEVBQUFBLFFBQVEsQ0FBQ1UsTUFBVDs7QUFFQSxNQUFJO0FBQ0gsUUFBSUMsVUFBVSxHQUFHbkMsUUFBUSxDQUFDb0MsV0FBVCxDQUFxQixNQUFyQixDQUFqQjtBQUNBLFFBQUlDLEdBQUcsR0FBR0YsVUFBVSxHQUFHLFlBQUgsR0FBa0IsY0FBdEM7QUFDQUcsSUFBQUEsT0FBTyxDQUFDQyxHQUFSLENBQVksd0NBQXdDRixHQUFwRDtBQUNBLEdBSkQsQ0FJRSxPQUFPRyxHQUFQLEVBQVk7QUFDYkYsSUFBQUEsT0FBTyxDQUFDRyxLQUFSLENBQWMsZ0NBQWQsRUFBZ0RELEdBQWhEO0FBQ0E7O0FBRUR4QyxFQUFBQSxRQUFRLENBQUMrQixJQUFULENBQWNXLFdBQWQsQ0FBMEJsQixRQUExQjtBQUNBOztBQUVELFNBQVNSLG1CQUFULENBQTZCQyxJQUE3QixFQUFtQztBQUNsQyxNQUFJLENBQUMwQixTQUFTLENBQUNDLFNBQWYsRUFBMEI7QUFDekJyQixJQUFBQSwyQkFBMkIsQ0FBQ04sSUFBRCxDQUEzQjtBQUNBO0FBQ0E7O0FBQ0QwQixFQUFBQSxTQUFTLENBQUNDLFNBQVYsQ0FBb0JDLFNBQXBCLENBQThCNUIsSUFBOUIsRUFBb0M2QixJQUFwQyxDQUF5QyxZQUFZO0FBQ3BEUixJQUFBQSxPQUFPLENBQUNDLEdBQVIsQ0FBWSw2Q0FBWjtBQUNBLEdBRkQsRUFFRyxVQUFVQyxHQUFWLEVBQWU7QUFDakJGLElBQUFBLE9BQU8sQ0FBQ0csS0FBUixDQUFjLDhCQUFkLEVBQThDRCxHQUE5QztBQUNBLEdBSkQ7QUFLQTs7Ozs7Ozs7Ozs7QUM1RUQiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvYXBwLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9zdHlsZXMvYXBwLnNjc3MiXSwic291cmNlc0NvbnRlbnQiOlsiaW1wb3J0ICcuL3N0eWxlcy9hcHAuc2Nzcyc7XG5cbmltcG9ydCAnYm9vdHN0cmFwJztcbmltcG9ydCAnYWRtaW4tbHRlJ1xuaW1wb3J0ICdib290c3RyYXAtZGF0ZXBpY2tlci9qcy9ib290c3RyYXAtZGF0ZXBpY2tlcic7XG5pbXBvcnQgbmFqYSBmcm9tICduYWphJztcbmltcG9ydCAnYm9vdHN0cmFwLXNlbGVjdCc7XG5cbmltcG9ydCBMaXZlRm9ybVZhbGlkYXRpb24gZnJvbSAnbGl2ZS1mb3JtLXZhbGlkYXRpb24tZXM2Jztcblxud2luZG93LkxpdmVGb3JtID0gTGl2ZUZvcm1WYWxpZGF0aW9uLkxpdmVGb3JtO1xud2luZG93Lk5ldHRlID0gTGl2ZUZvcm1WYWxpZGF0aW9uLk5ldHRlO1xuXG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdET01Db250ZW50TG9hZGVkJywgbmFqYS5pbml0aWFsaXplLmJpbmQobmFqYSkpO1xuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignRE9NQ29udGVudExvYWRlZCcsIGZ1bmN0aW9uICgpIHtcblx0d2luZG93Lk5ldHRlLmluaXQoKTtcblx0d2luZG93LkxpdmVGb3JtLnNldE9wdGlvbnMoe1xuXHRcdGNvbnRyb2xFcnJvckNsYXNzOiAnaXMtaW52YWxpZCcsXG5cdFx0Y29udHJvbFZhbGlkQ2xhc3M6ICdpcy12YWxpZCcsXG5cdFx0bWVzc2FnZUVycm9yQ2xhc3M6ICdlcnJvciBpbnZhbGlkLWZlZWRiYWNrJyxcblx0XHRtZXNzYWdlVGFnOiAnc3BhbicsXG5cdFx0c2hvd1ZhbGlkOiB0cnVlLFxuXHRcdG1lc3NhZ2VFcnJvclByZWZpeDogJyZuYnNwOzxpIGNsYXNzPVwiZmEgZmEtZXhjbGFtYXRpb24tY2lyY2xlXCIgYXJpYS1oaWRkZW49XCJ0cnVlXCI+PC9pPiZuYnNwOydcblx0fSk7XG5cdCQoJypbY2xpcGJvYXJkLWNvcHktdGFyZ2V0LWlkXScpLm9uKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcblx0XHRsZXQgZWxlSWQgPSAkKHRoaXMpLmF0dHIoJ2NsaXBib2FyZC1jb3B5LXRhcmdldC1pZCcpO1xuXHRcdGNvcHlUZXh0VG9DbGlwYm9hcmQoJCgnIycgKyBlbGVJZCkudGV4dCgpKTtcblx0fSlcblx0JCgnLm51bWJlckxpbmVJZCcpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbiAoKSB7XG5cdFx0aWYgKCQodGhpcykudmFsKCkubGVuZ3RoID4gMCkge1xuXHRcdFx0bGV0IGNoZWNrYm94SW5wdXQgPSAkKCdpbnB1dC5zeW5jaHJvbml6ZV9wcm9mb3JtYUludm9pY2VzJyk7XG5cdFx0XHRjaGVja2JveElucHV0LnByb3AoXCJjaGVja2VkXCIsIGZhbHNlKTtcblx0XHR9XG5cdH0pO1xuXHQkKCdpbnB1dC5zeW5jaHJvbml6ZV9wcm9mb3JtYUludm9pY2VzJykub24oJ2NoYW5nZScsIGZ1bmN0aW9uICgpIHtcblx0XHRpZiAoJCh0aGlzKS5wcm9wKCdjaGVja2VkJykgJiYgJCgnLm51bWJlckxpbmVJZCcpLnZhbCgpLmxlbmd0aCA+IDApIHtcblx0XHRcdGFsZXJ0KCdaw6Fsb2hvdsOpIGRva2xhZHkgbmVsemUgdnl1xb7DrXZhdCwgcG9rdWQgbcOhdGUgamlub3UsIG5lxb4gdsO9Y2hvesOtIMSNw61zZWxub3UgxZlhZHUuJyk7XG5cdFx0XHQkKHRoaXMpLnByb3AoXCJjaGVja2VkXCIsIGZhbHNlKTtcblx0XHR9XG5cdH0pO1xufSk7XG5cbmZ1bmN0aW9uIGZhbGxiYWNrQ29weVRleHRUb0NsaXBib2FyZCh0ZXh0KSB7XG5cdHZhciB0ZXh0QXJlYSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoXCJ0ZXh0YXJlYVwiKTtcblx0dGV4dEFyZWEudmFsdWUgPSB0ZXh0O1xuXG5cdC8vIEF2b2lkIHNjcm9sbGluZyB0byBib3R0b21cblx0dGV4dEFyZWEuc3R5bGUudG9wID0gXCIwXCI7XG5cdHRleHRBcmVhLnN0eWxlLmxlZnQgPSBcIjBcIjtcblx0dGV4dEFyZWEuc3R5bGUucG9zaXRpb24gPSBcImZpeGVkXCI7XG5cblx0ZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZCh0ZXh0QXJlYSk7XG5cdHRleHRBcmVhLmZvY3VzKCk7XG5cdHRleHRBcmVhLnNlbGVjdCgpO1xuXG5cdHRyeSB7XG5cdFx0dmFyIHN1Y2Nlc3NmdWwgPSBkb2N1bWVudC5leGVjQ29tbWFuZCgnY29weScpO1xuXHRcdHZhciBtc2cgPSBzdWNjZXNzZnVsID8gJ3N1Y2Nlc3NmdWwnIDogJ3Vuc3VjY2Vzc2Z1bCc7XG5cdFx0Y29uc29sZS5sb2coJ0ZhbGxiYWNrOiBDb3B5aW5nIHRleHQgY29tbWFuZCB3YXMgJyArIG1zZyk7XG5cdH0gY2F0Y2ggKGVycikge1xuXHRcdGNvbnNvbGUuZXJyb3IoJ0ZhbGxiYWNrOiBPb3BzLCB1bmFibGUgdG8gY29weScsIGVycik7XG5cdH1cblxuXHRkb2N1bWVudC5ib2R5LnJlbW92ZUNoaWxkKHRleHRBcmVhKTtcbn1cblxuZnVuY3Rpb24gY29weVRleHRUb0NsaXBib2FyZCh0ZXh0KSB7XG5cdGlmICghbmF2aWdhdG9yLmNsaXBib2FyZCkge1xuXHRcdGZhbGxiYWNrQ29weVRleHRUb0NsaXBib2FyZCh0ZXh0KTtcblx0XHRyZXR1cm47XG5cdH1cblx0bmF2aWdhdG9yLmNsaXBib2FyZC53cml0ZVRleHQodGV4dCkudGhlbihmdW5jdGlvbiAoKSB7XG5cdFx0Y29uc29sZS5sb2coJ0FzeW5jOiBDb3B5aW5nIHRvIGNsaXBib2FyZCB3YXMgc3VjY2Vzc2Z1bCEnKTtcblx0fSwgZnVuY3Rpb24gKGVycikge1xuXHRcdGNvbnNvbGUuZXJyb3IoJ0FzeW5jOiBDb3VsZCBub3QgY29weSB0ZXh0OiAnLCBlcnIpO1xuXHR9KTtcbn1cbiIsIi8vIGV4dHJhY3RlZCBieSBtaW5pLWNzcy1leHRyYWN0LXBsdWdpblxuZXhwb3J0IHt9OyJdLCJuYW1lcyI6WyJuYWphIiwiTGl2ZUZvcm1WYWxpZGF0aW9uIiwid2luZG93IiwiTGl2ZUZvcm0iLCJOZXR0ZSIsImRvY3VtZW50IiwiYWRkRXZlbnRMaXN0ZW5lciIsImluaXRpYWxpemUiLCJiaW5kIiwiaW5pdCIsInNldE9wdGlvbnMiLCJjb250cm9sRXJyb3JDbGFzcyIsImNvbnRyb2xWYWxpZENsYXNzIiwibWVzc2FnZUVycm9yQ2xhc3MiLCJtZXNzYWdlVGFnIiwic2hvd1ZhbGlkIiwibWVzc2FnZUVycm9yUHJlZml4IiwiJCIsIm9uIiwiZWxlSWQiLCJhdHRyIiwiY29weVRleHRUb0NsaXBib2FyZCIsInRleHQiLCJ2YWwiLCJsZW5ndGgiLCJjaGVja2JveElucHV0IiwicHJvcCIsImFsZXJ0IiwiZmFsbGJhY2tDb3B5VGV4dFRvQ2xpcGJvYXJkIiwidGV4dEFyZWEiLCJjcmVhdGVFbGVtZW50IiwidmFsdWUiLCJzdHlsZSIsInRvcCIsImxlZnQiLCJwb3NpdGlvbiIsImJvZHkiLCJhcHBlbmRDaGlsZCIsImZvY3VzIiwic2VsZWN0Iiwic3VjY2Vzc2Z1bCIsImV4ZWNDb21tYW5kIiwibXNnIiwiY29uc29sZSIsImxvZyIsImVyciIsImVycm9yIiwicmVtb3ZlQ2hpbGQiLCJuYXZpZ2F0b3IiLCJjbGlwYm9hcmQiLCJ3cml0ZVRleHQiLCJ0aGVuIl0sInNvdXJjZVJvb3QiOiIifQ==