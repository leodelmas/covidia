import { French } from "flatpickr/dist/l10n/fr";

$(document).ready(function() {

    let workTimeSelect = $('select#task_workTime');
    let workTimeStart = workTimeSelect.find(':selected').data('date-start');
    let workTimeEnd = workTimeSelect.find(':selected').data('date-end');
    let dateTimePickers = $('input[type=datetime-local]');

    if(typeof workTimeStart == 'undefined' && typeof workTimeEnd == 'undefined') {
        dateTimePickers.prop('disabled', true);
    }
    else {
        workTimeSelect.prop("disabled", true);
        dateTimePickers.flatpickr({
            allowInput: true,
            altInput: true,
            altFormat: "d/m/Y H:i",
            dateFormat: "Y-m-d H:i:ss",
            minDate: workTimeStart,
            maxDate: workTimeEnd,
            locale: French
        });
    }

    workTimeSelect.on('change', function() {
        workTimeStart = $(this).find(':selected').data('date-start');
        workTimeEnd = $(this).find(':selected').data('date-end');
        dateTimePickers.prop("disabled", false);
        workTimeSelect.prop("disabled", true);
        dateTimePickers.flatpickr({
            allowInput: true,
            altInput: true,
            altFormat: "d/m/Y H:i",
            dateFormat: "Y-m-d H:i:ss",
            minDate: workTimeStart,
            maxDate: workTimeEnd,
            locale: French
        });
    });
});