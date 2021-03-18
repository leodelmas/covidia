import {French} from "flatpickr/dist/l10n/fr";

$(document).ready(function() {
    $('select[multiple=multiple]').select2();

    $('input[type=date]').flatpickr({
        allowInput: true,
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        locale: French,
    });

});