/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
// start the Stimulus application
import './bootstrap';
// the bootstrap module doesn't export/return anything
require('bootstrap');
