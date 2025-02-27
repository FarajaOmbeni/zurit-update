// composables/useAlert.js
import { ref } from "vue";

const alertState = ref(null);

export function useAlert() {
    const openAlert = (type, message, duration = 5000, autoClose = true) => {
        alertState.value = { type, message, duration, autoClose };
    };

    const clearAlert = () => {
        alertState.value = null;
    };

    return { alertState, openAlert, clearAlert };
}
