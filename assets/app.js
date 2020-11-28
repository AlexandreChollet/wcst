import '@fortawesome/fontawesome-free/js/all.js';
import '@fortawesome/fontawesome-free/css/all.css';

import 'select2';                       // globally assign select2 fn to $ element
import 'select2/dist/css/select2.css';  // optional if you have css loader

var $ = require('jquery');
var b = require('bootstrap');
var se = require('datatables.net-bs4');
global.$ = global.jQuery = $;

$(document).ready(function() {
    $('.sortable').DataTable( {
        "lengthMenu": [ [15, 30, 100, -1], [15, 30, 100, "Toutes"] ],
        "searching": false,
        "language": {
            "lengthMenu": "Montrer _MENU_ entrées par page",
            "info": "Page _PAGE_ sur _PAGES_",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "paginate": {
                "next":       "Suivant",
                "previous":   "Précédent"
            }
        }
    });
});