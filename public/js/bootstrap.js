window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

window.$ = window.jQuery = require('jquery')

require('overlayscrollbars');
require('../../vendor/almasaeed2010/adminlte/dist/js/adminlte');
require('../../vendor/almasaeed2010/adminlte/plugins/bootstrap/js/bootstrap.bundle');

//Datatables
	window.JSZip = require('jszip');
	require('datatables.net-bs4');
	require('datatables.net-buttons-bs4');
	require('datatables.net-buttons/js/dataTables.buttons');
	require('datatables.net-buttons/js/buttons.colVis.js');
	require('datatables.net-buttons/js/buttons.html5.js');

//bootstrap-select
	require('bootstrap-select/dist/js/bootstrap-select');
//
//swwetalert2
	const swal = window.swal = require('sweetalert2');
//pace-js
	const Pace = require('pace-progress/pace');
//filesaverjs
	const filesaver = window.filesaver = require('filesaver.js-npm/FileSaver.js'); 
//xlsx (sheetjs)
	const XLSX = window.XLSX = require('xlsx/dist/xlsx.full.min');
//daterangepicker: https://github.com/dangrossman/daterangepicker
	const moment = window.moment = require("moment");
	require("bootstrap-daterangepicker");
