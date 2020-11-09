/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 3);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./resources/js/sort.js":
/*!******************************!*\
  !*** ./resources/js/sort.js ***!
  \******************************/
/*! no static exports found */
/***/ (function(module, exports) {

$(document).ready(init);

function init() {
  addSearchButtonListener();
  document.cookie = "nofbed=";
  document.cookie = "nofroom=";
}

function addSearchButtonListener() {
  $('#search-button').click(sendRequestSearch);
}

function sendRequestSearch() {
  var nofbed = $('#nofbed').val(); //prende il valore dall HTML

  var nofroom = $('#nofroom').val();
  document.cookie = "nofbed=".concat(nofbed);
  document.cookie = "nofroom=".concat(nofroom);
  var services = [];
  $("input[name='service[]']:checked").each(function () {
    services.push($(this).val());
  });
  service = services.join(); //trasforma array in stringa

  $.ajax({
    url: '/api/search',
    data: {
      'service': service
    },
    method: 'GET',
    success: function success(data) {
      if (data.length == 0) {
        $('#message').show(); //mostra la scirtta "non ci sono appartamenti corrispondenti" 

        var target = $('.blocco-flat');
        target.hide();
      } else {
        $('#message').hide(); //nasconde scritta

        var target = $('.blocco-flat');
        target.hide(); //nascondo i vari blocco flat

        $.each(data, function (index, flat) {
          var id = flat.id;
          var target = $('.blocco-flat[data-id="' + id + '"]'); //ciclo su tutti  blocco flat con data-id  uguale allíd del flat e faccio show

          target.show();
        });
      }
    },
    error: function error(err) {
      console.log('err', err);
    }
  });
}

/***/ }),

/***/ 3:
/*!************************************!*\
  !*** multi ./resources/js/sort.js ***!
  \************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! C:\Users\Gabriele\Desktop\bool\resources\js\sort.js */"./resources/js/sort.js");


/***/ })

/******/ });