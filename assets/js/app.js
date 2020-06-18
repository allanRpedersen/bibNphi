/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';
// require('../css/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// var $ = require('jquery');
import $ from 'jquery';
import jQuery from 'jquery';

// require('popper.js');
import 'popper.js';

// require('bootstrap');
import 'bootstrap';

import 'select2';

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
//
$(document).ready(function(){

	// Form/SentenceSearchType
	$('#sentence_search_books').select2();
	$('#sentence_search_authors').select2();

	console.log('Document Ready !!');
});
