// We need bootstrap for bootstrap-webpack
// require('bootstrap');
const $ = require('jquery');

// create global $ and jQuery variables
global.$ = global.jQuery = $;

require('bootstrap-sass');
