import { French } from "flatpickr/dist/l10n/fr";

$(document).ready(function() {
    let datePickers = $('input[type=date]');
    let plannedWorkTimes = $('input#work_time_plannedWorkTimes').val();

    datePickers.flatpickr({
        allowInput: true,
        altInput: true,
        altFormat: "d/m/Y",
        dateFormat: "Y-m-d",
        locale: French,
        disable: JSON.parse(plannedWorkTimes)
    });
});