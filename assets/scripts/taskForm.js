import { French } from "flatpickr/dist/l10n/fr";

$(document).ready(function() {

    let workTimeSelect = $('select#task_workTime');
    let taskCategorySelect = $('select#task_taskCategory');
    let workTimeStart = workTimeSelect.find(':selected').data('date-start');
    let workTimeEnd = workTimeSelect.find(':selected').data('date-end');
    let workTimeIsTeleworked = workTimeSelect.find(':selected').data('is-teleworked');
    let dateTimePickers = $('input[type=datetime-local]');

    if(typeof workTimeStart == 'undefined' && typeof workTimeEnd == 'undefined') {
        dateTimePickers.prop('disabled', true);
    }
    else {
        workTimeSelect.prop("disabled", true);

        // Gestion des dateTime pickers
        dateTimePickers.flatpickr({
            allowInput: true,
            altInput: true,
            altFormat: "d/m/Y H:i",
            dateFormat: "Y-m-d H:i:ss",
            minDate: workTimeStart,
            maxDate: workTimeEnd,
            locale: French,
            enableTime: true
        });

        // Gestion des catégories
        $.each(taskCategorySelect.find('option'), function() {
            if(
                $(this).val() === ""
                || typeof $(this).data('is-physical') !== "undefined" && typeof $(this).data('is-remote') !== "undefined"
                || typeof $(this).data('is-remote') !== "undefined" && typeof workTimeIsTeleworked !== "undefined"
                || typeof $(this).data('is-physical') !== "undefined" && typeof workTimeIsTeleworked == "undefined") { }
            else { $(this).remove(); }
        });
    }

    workTimeSelect.on('change', function() {
        workTimeSelect.prop("disabled", true);

        // Gestion des dateTime pickers
        workTimeStart = $(this).find(':selected').data('date-start');
        workTimeEnd = $(this).find(':selected').data('date-end');
        dateTimePickers.prop("disabled", false);
        dateTimePickers.flatpickr({
            allowInput: true,
            altInput: true,
            altFormat: "d/m/Y H:i",
            dateFormat: "Y-m-d H:i:ss",
            minDate: workTimeStart,
            maxDate: workTimeEnd,
            locale: French,
            enableTime: true
        });

        // Gestion des catégories
        workTimeIsTeleworked = workTimeSelect.find(':selected').data('is-teleworked');
        taskCategorySelect.prop('disabled', false);
        $.each(taskCategorySelect.find('option'), function() {
            if(
                $(this).val() === ""
                || typeof $(this).data('is-physical') !== "undefined" && typeof $(this).data('is-remote') !== "undefined"
                || typeof $(this).data('is-remote') !== "undefined" && typeof workTimeIsTeleworked !== "undefined"
                || typeof $(this).data('is-physical') !== "undefined" && typeof workTimeIsTeleworked == "undefined") { }
            else { $(this).remove(); }
        });
    });
});