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


// require('popper.js');
import 'popper.js';

// require('bootstrap');
import 'bootstrap';

import 'select2';

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
//


// $('button#main_search').on('click', function(e)
// $('button#main_search').addEventListener('click', function(e)
// $('button#main_search').on('click', function(e)
// {
// 	console.log('===== showSpinner ====', e.target );
// 	return true;
	
// });

function showSpinner(){

	console.log('showSpinner');

	$('#search_button').append('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span class="sr-only">Rechercher...</span>');
	alert('showSpinner');
}



$(document).ready(function(){

	
	// Form/SentenceSearchType
	$('#sentence_search_books').select2({
		width: '100%',
		placeholder: 'parmi les oeuvres ...',
	});
	$('#sentence_search_authors').select2({
		width: '100%',
		placeholder: 'parmi les auteurs ...',
	});

	console.log('Document Ready !!');
	// alert();
});
