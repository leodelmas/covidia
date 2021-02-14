import { French } from "flatpickr/dist/l10n/fr";

$(document).ready(function() {
    $('input[type=date]').flatpickr({
        allowInput: true,
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        locale: French
    });

    $('select[multiple=multiple]').select2();
});