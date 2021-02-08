import flatpickr from "flatpickr";
import { French } from "flatpickr/dist/l10n/fr";

flatpickr('input[type=datetime-local]', {
    enableTime: true,
    allowInput: true,
    altInput: true,
    altFormat: "d/m/Y H:i",
    dateFormat: "Y-m-d H:i:ss",
    locale: French
});