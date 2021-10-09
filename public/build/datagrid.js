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
/* harmony import */ var naja__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! naja */ "./node_modules/naja/dist/Naja.esm.js");
/* harmony import */ var bootstrap_select__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! bootstrap-select */ "./node_modules/bootstrap-select/dist/js/bootstrap-select.js");
/* harmony import */ var bootstrap_select__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(bootstrap_select__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var live_form_validation_es6__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! live-form-validation-es6 */ "./node_modules/live-form-validation-es6/live-form-validation.js");
/* harmony import */ var live_form_validation_es6__WEBPACK_IMPORTED_MODULE_6___default = /*#__PURE__*/__webpack_require__.n(live_form_validation_es6__WEBPACK_IMPORTED_MODULE_6__);
/* provided dependency */ var $ = __webpack_require__(/*! jquery */ "./node_modules/jquery/dist/jquery.js");







window.LiveForm = (live_form_validation_es6__WEBPACK_IMPORTED_MODULE_6___default().LiveForm);
window.Nette = (live_form_validation_es6__WEBPACK_IMPORTED_MODULE_6___default().Nette);
document.addEventListener('DOMContentLoaded', naja__WEBPACK_IMPORTED_MODULE_4__["default"].initialize.bind(naja__WEBPACK_IMPORTED_MODULE_4__["default"]));
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
  $('*[clipboard-copy-target-id]').on('click', function () {
    var eleId = $(this).attr('clipboard-copy-target-id');
    copyTextToClipboard($('#' + eleId).text());
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
/******/ __webpack_require__.O(0, ["vendors-node_modules_admin-lte_dist_js_adminlte_min_js-node_modules_bootstrap-select_dist_js_-449fba","vendors-node_modules_core-js_modules_es_array_find_js-node_modules_core-js_modules_es_string_-7feadc","assets_styles_bs-stepper_css-assets_styles_app_scss"], () => (__webpack_exec__("./assets/datagrid.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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dEM7QUFDQSxLQU5ELE1BTU87QUFDTjlDLE1BQUFBLENBQUMsQ0FBQyx5QkFBRCxDQUFELENBQTZCOEIsSUFBN0IsQ0FBa0MsVUFBVUMsQ0FBVixFQUFhO0FBQzlDL0IsUUFBQUEsQ0FBQyxDQUFDLElBQUQsQ0FBRCxDQUFRaUMsSUFBUixDQUFhLCtCQUFiLEVBQThDTSxJQUE5QztBQUNBdkMsUUFBQUEsQ0FBQyxDQUFDLElBQUQsQ0FBRCxDQUFRaUMsSUFBUixDQUFhLE9BQWIsRUFBc0JZLElBQXRCO0FBQ0EsT0FIRDtBQUlBN0MsTUFBQUEsQ0FBQyxDQUFDLHlCQUFELENBQUQsQ0FBNkIrQyxXQUE3QixDQUF5QyxxQkFBekM7QUFDQTtBQUNEOztBQUVESixFQUFBQSxTQUFTOztBQUVUM0QsRUFBQUEsTUFBTSxDQUFDZ0UsUUFBUCxHQUFrQixVQUFVQyxLQUFWLEVBQWlCO0FBQ2xDTixJQUFBQSxTQUFTO0FBQ1QsR0FGRDtBQUdBLENBaEREOzs7Ozs7Ozs7OztBQ05BOzs7Ozs7Ozs7Ozs7QUNBQSIsInNvdXJjZXMiOlsid2VicGFjazovLy8uL2Fzc2V0cy9hcHAuanMiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL2RhdGFncmlkLmpzIiwid2VicGFjazovLy8uL2Fzc2V0cy9zdHlsZXMvYXBwLnNjc3MiLCJ3ZWJwYWNrOi8vLy4vYXNzZXRzL3N0eWxlcy9kYXRhZ3JpZC5zY3NzIl0sInNvdXJjZXNDb250ZW50IjpbImltcG9ydCAnLi9zdHlsZXMvYXBwLnNjc3MnO1xuXG5pbXBvcnQgJ2Jvb3RzdHJhcCc7XG5pbXBvcnQgJ2FkbWluLWx0ZSdcbmltcG9ydCBuYWphIGZyb20gJ25hamEnO1xuaW1wb3J0ICdib290c3RyYXAtc2VsZWN0JztcblxuaW1wb3J0IExpdmVGb3JtVmFsaWRhdGlvbiBmcm9tICdsaXZlLWZvcm0tdmFsaWRhdGlvbi1lczYnO1xuXG53aW5kb3cuTGl2ZUZvcm0gPSBMaXZlRm9ybVZhbGlkYXRpb24uTGl2ZUZvcm07XG53aW5kb3cuTmV0dGUgPSBMaXZlRm9ybVZhbGlkYXRpb24uTmV0dGU7XG5cbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCBuYWphLmluaXRpYWxpemUuYmluZChuYWphKSk7XG5kb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdET01Db250ZW50TG9hZGVkJywgZnVuY3Rpb24gKCkge1xuXHR3aW5kb3cuTmV0dGUuaW5pdCgpO1xuXHR3aW5kb3cuTGl2ZUZvcm0uc2V0T3B0aW9ucyh7XG5cdFx0c2hvd01lc3NhZ2VDbGFzc09uUGFyZW50OiBmYWxzZSxcblx0XHRjb250cm9sRXJyb3JDbGFzczogJ2lzLWludmFsaWQnLFxuXHRcdGNvbnRyb2xWYWxpZENsYXNzOiAnaXMtdmFsaWQnLFxuXHRcdG1lc3NhZ2VFcnJvckNsYXNzOiAnZXJyb3IgaW52YWxpZC1mZWVkYmFjaycsXG5cdFx0bWVzc2FnZVRhZzogJ3NwYW4nLFxuXHRcdHNob3dWYWxpZDogdHJ1ZSxcblx0XHRtZXNzYWdlRXJyb3JQcmVmaXg6ICcmbmJzcDs8aSBjbGFzcz1cImZhIGZhLWV4Y2xhbWF0aW9uLWNpcmNsZVwiIGFyaWEtaGlkZGVuPVwidHJ1ZVwiPjwvaT4mbmJzcDsnXG5cdH0pO1xuXHQkKCcqW2NsaXBib2FyZC1jb3B5LXRhcmdldC1pZF0nKS5vbignY2xpY2snLCBmdW5jdGlvbiAoKSB7XG5cdFx0bGV0IGVsZUlkID0gJCh0aGlzKS5hdHRyKCdjbGlwYm9hcmQtY29weS10YXJnZXQtaWQnKTtcblx0XHRjb3B5VGV4dFRvQ2xpcGJvYXJkKCQoJyMnICsgZWxlSWQpLnRleHQoKSk7XG5cdH0pXG59KTtcblxuZnVuY3Rpb24gZmFsbGJhY2tDb3B5VGV4dFRvQ2xpcGJvYXJkKHRleHQpIHtcblx0dmFyIHRleHRBcmVhID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcInRleHRhcmVhXCIpO1xuXHR0ZXh0QXJlYS52YWx1ZSA9IHRleHQ7XG5cblx0Ly8gQXZvaWQgc2Nyb2xsaW5nIHRvIGJvdHRvbVxuXHR0ZXh0QXJlYS5zdHlsZS50b3AgPSBcIjBcIjtcblx0dGV4dEFyZWEuc3R5bGUubGVmdCA9IFwiMFwiO1xuXHR0ZXh0QXJlYS5zdHlsZS5wb3NpdGlvbiA9IFwiZml4ZWRcIjtcblxuXHRkb2N1bWVudC5ib2R5LmFwcGVuZENoaWxkKHRleHRBcmVhKTtcblx0dGV4dEFyZWEuZm9jdXMoKTtcblx0dGV4dEFyZWEuc2VsZWN0KCk7XG5cblx0dHJ5IHtcblx0XHR2YXIgc3VjY2Vzc2Z1bCA9IGRvY3VtZW50LmV4ZWNDb21tYW5kKCdjb3B5Jyk7XG5cdFx0dmFyIG1zZyA9IHN1Y2Nlc3NmdWwgPyAnc3VjY2Vzc2Z1bCcgOiAndW5zdWNjZXNzZnVsJztcblx0XHRjb25zb2xlLmxvZygnRmFsbGJhY2s6IENvcHlpbmcgdGV4dCBjb21tYW5kIHdhcyAnICsgbXNnKTtcblx0fSBjYXRjaCAoZXJyKSB7XG5cdFx0Y29uc29sZS5lcnJvcignRmFsbGJhY2s6IE9vcHMsIHVuYWJsZSB0byBjb3B5JywgZXJyKTtcblx0fVxuXG5cdGRvY3VtZW50LmJvZHkucmVtb3ZlQ2hpbGQodGV4dEFyZWEpO1xufVxuXG5mdW5jdGlvbiBjb3B5VGV4dFRvQ2xpcGJvYXJkKHRleHQpIHtcblx0aWYgKCFuYXZpZ2F0b3IuY2xpcGJvYXJkKSB7XG5cdFx0ZmFsbGJhY2tDb3B5VGV4dFRvQ2xpcGJvYXJkKHRleHQpO1xuXHRcdHJldHVybjtcblx0fVxuXHRuYXZpZ2F0b3IuY2xpcGJvYXJkLndyaXRlVGV4dCh0ZXh0KS50aGVuKGZ1bmN0aW9uICgpIHtcblx0XHRjb25zb2xlLmxvZygnQXN5bmM6IENvcHlpbmcgdG8gY2xpcGJvYXJkIHdhcyBzdWNjZXNzZnVsIScpO1xuXHR9LCBmdW5jdGlvbiAoZXJyKSB7XG5cdFx0Y29uc29sZS5lcnJvcignQXN5bmM6IENvdWxkIG5vdCBjb3B5IHRleHQ6ICcsIGVycik7XG5cdH0pO1xufVxuIiwiaW1wb3J0ICcuL3N0eWxlcy9kYXRhZ3JpZC5zY3NzJztcbmltcG9ydCAnLi9hcHAnO1xuaW1wb3J0ICd1YmxhYm9vLWRhdGFncmlkL2Fzc2V0cy9kYXRhZ3JpZCc7XG5pbXBvcnQgJ3VibGFib28tZGF0YWdyaWQvYXNzZXRzL2RhdGFncmlkLXNwaW5uZXJzJztcbmltcG9ydCAndWJsYWJvby1kYXRhZ3JpZC9hc3NldHMvZGF0YWdyaWQtaW5zdGFudC11cmwtcmVmcmVzaCc7XG5cbmRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCBmdW5jdGlvbiAoKSB7XG5cdCQoJy50YWJsZS1yZXNwb25zaXZlLXN0YWNrJykuZWFjaChmdW5jdGlvbiAoaSkge1xuXHRcdHZhciBpZCA9ICQodGhpcykuYXR0cignaWQnKTtcblx0XHQkKHRoaXMpLmZpbmQoXCJ0aGVhZCB0cjpudGgtY2hpbGQoMikgdGhcIikuZWFjaChmdW5jdGlvbiAoaSkge1xuXHRcdFx0bGV0IHRoZWFkVmFsdWUgPSAkKHRoaXMpLnRleHQoKTtcblxuXHRcdFx0aWYgKCQodGhpcykuZmluZCgnYScpLmxlbmd0aCA+IDApIHtcblx0XHRcdFx0dGhlYWRWYWx1ZSA9ICQodGhpcykuZmluZCgnYScpLmZpcnN0KCkudGV4dCgpO1xuXHRcdFx0fVxuXHRcdFx0dGhlYWRWYWx1ZSA9ICQudHJpbSh0aGVhZFZhbHVlKTtcblx0XHRcdGNvbnNvbGUubG9nKHRoZWFkVmFsdWUpO1xuXHRcdFx0aWYodGhlYWRWYWx1ZS5sZW5ndGg+MCkge1xuXHRcdFx0XHQkKCcjJyArIGlkICsgJyB0ZDpudGgtY2hpbGQoJyArIChpICsgMSkgKyAnKScpLnByZXBlbmQoJzxzcGFuIGNsYXNzPVwidGFibGUtcmVzcG9uc2l2ZS1zdGFjay10aGVhZFwiPicgKyB0aGVhZFZhbHVlICsgJzo8L3NwYW4+ICcpO1xuXHRcdFx0fWVsc2V7XG5cdFx0XHRcdCQoJyMnICsgaWQgKyAnIHRkOm50aC1jaGlsZCgnICsgKGkgKyAxKSArICcpJykucHJlcGVuZCgnPHNwYW4gY2xhc3M9XCJ0YWJsZS1yZXNwb25zaXZlLXN0YWNrLXRoZWFkXCI+PC9zcGFuPiAnKTtcblx0XHRcdH1cblx0XHRcdCQoJy50YWJsZS1yZXNwb25zaXZlLXN0YWNrLXRoZWFkJykuaGlkZSgpO1xuXG5cdFx0fSk7XG5cdH0pO1xuXG5cdCQoJy50YWJsZS1yZXNwb25zaXZlLXN0YWNrJykuZWFjaChmdW5jdGlvbiAoKSB7XG5cdFx0dmFyIHRoQ291bnQgPSAkKHRoaXMpLmZpbmQoXCJ0aFwiKS5sZW5ndGg7XG5cdFx0dmFyIHJvd0dyb3cgPSAxMDAgLyB0aENvdW50ICsgJyUnO1xuXHRcdCQodGhpcykuZmluZChcInRoLCB0ZFwiKS5jc3MoJ2ZsZXgtYmFzaXMnLCByb3dHcm93KTtcblx0fSk7XG5cblx0ZnVuY3Rpb24gZmxleFRhYmxlKCkge1xuXHRcdGlmICgkKHdpbmRvdykud2lkdGgoKSA8IDc2OCkge1xuXHRcdFx0JChcIi50YWJsZS1yZXNwb25zaXZlLXN0YWNrXCIpLmVhY2goZnVuY3Rpb24gKGkpIHtcblx0XHRcdFx0JCh0aGlzKS5maW5kKFwiLnRhYmxlLXJlc3BvbnNpdmUtc3RhY2stdGhlYWRcIikuc2hvdygpO1xuXHRcdFx0XHQkKHRoaXMpLmZpbmQoJ3RoZWFkJykuaGlkZSgpO1xuXHRcdFx0fSk7XG5cdFx0XHQkKFwiLnRhYmxlLXJlc3BvbnNpdmUtc3RhY2tcIikuYWRkQ2xhc3MoJ3RhYmxlLWlzLXJlc3BvbnNpdmUnKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0JChcIi50YWJsZS1yZXNwb25zaXZlLXN0YWNrXCIpLmVhY2goZnVuY3Rpb24gKGkpIHtcblx0XHRcdFx0JCh0aGlzKS5maW5kKFwiLnRhYmxlLXJlc3BvbnNpdmUtc3RhY2stdGhlYWRcIikuaGlkZSgpO1xuXHRcdFx0XHQkKHRoaXMpLmZpbmQoJ3RoZWFkJykuc2hvdygpO1xuXHRcdFx0fSk7XG5cdFx0XHQkKFwiLnRhYmxlLXJlc3BvbnNpdmUtc3RhY2tcIikucmVtb3ZlQ2xhc3MoJ3RhYmxlLWlzLXJlc3BvbnNpdmUnKTtcblx0XHR9XG5cdH1cblxuXHRmbGV4VGFibGUoKTtcblxuXHR3aW5kb3cub25yZXNpemUgPSBmdW5jdGlvbiAoZXZlbnQpIHtcblx0XHRmbGV4VGFibGUoKTtcblx0fTtcbn0pO1xuXG5cblxuIiwiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luXG5leHBvcnQge307IiwiLy8gZXh0cmFjdGVkIGJ5IG1pbmktY3NzLWV4dHJhY3QtcGx1Z2luXG5leHBvcnQge307Il0sIm5hbWVzIjpbIm5hamEiLCJMaXZlRm9ybVZhbGlkYXRpb24iLCJ3aW5kb3ciLCJMaXZlRm9ybSIsIk5ldHRlIiwiZG9jdW1lbnQiLCJhZGRFdmVudExpc3RlbmVyIiwiaW5pdGlhbGl6ZSIsImJpbmQiLCJpbml0Iiwic2V0T3B0aW9ucyIsInNob3dNZXNzYWdlQ2xhc3NPblBhcmVudCIsImNvbnRyb2xFcnJvckNsYXNzIiwiY29udHJvbFZhbGlkQ2xhc3MiLCJtZXNzYWdlRXJyb3JDbGFzcyIsIm1lc3NhZ2VUYWciLCJzaG93VmFsaWQiLCJtZXNzYWdlRXJyb3JQcmVmaXgiLCIkIiwib24iLCJlbGVJZCIsImF0dHIiLCJjb3B5VGV4dFRvQ2xpcGJvYXJkIiwidGV4dCIsImZhbGxiYWNrQ29weVRleHRUb0NsaXBib2FyZCIsInRleHRBcmVhIiwiY3JlYXRlRWxlbWVudCIsInZhbHVlIiwic3R5bGUiLCJ0b3AiLCJsZWZ0IiwicG9zaXRpb24iLCJib2R5IiwiYXBwZW5kQ2hpbGQiLCJmb2N1cyIsInNlbGVjdCIsInN1Y2Nlc3NmdWwiLCJleGVjQ29tbWFuZCIsIm1zZyIsImNvbnNvbGUiLCJsb2ciLCJlcnIiLCJlcnJvciIsInJlbW92ZUNoaWxkIiwibmF2aWdhdG9yIiwiY2xpcGJvYXJkIiwid3JpdGVUZXh0IiwidGhlbiIsImVhY2giLCJpIiwiaWQiLCJmaW5kIiwidGhlYWRWYWx1ZSIsImxlbmd0aCIsImZpcnN0IiwidHJpbSIsInByZXBlbmQiLCJoaWRlIiwidGhDb3VudCIsInJvd0dyb3ciLCJjc3MiLCJmbGV4VGFibGUiLCJ3aWR0aCIsInNob3ciLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwib25yZXNpemUiLCJldmVudCJdLCJzb3VyY2VSb290IjoiIn0=