import flatpickr from "flatpickr";
import { French } from "flatpickr/dist/l10n/fr";
import $ from 'jquery';

flatpickr('input[type=datetime-local]', {
    enableTime: true,
    allowInput: true,
    altInput: true,
    altFormat: "d/m/Y H:i",
    dateFormat: "Y-m-d H:i:ss",
    locale: French
});

flatpickr('input[type=date]', {
    allowInput: true,
    altInput: true,
    altFormat: "d/m/Y",
    dateFormat: "Y-m-d",
    locale: French
});

$(document).ready(function() {
    $('select[multiple=multiple]').select2();
});