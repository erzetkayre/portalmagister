import { ref } from 'vue';

export function useAlert(timeout = 5000) {
    const showAlert = ref(false);
    const alertType = ref<'success' | 'error'>('success');
    const alertTitle = ref('');
    const alertDescription = ref('');

    const showSuccessAlert = (title: string, description: string) => {
        alertType.value = 'success';
        alertTitle.value = title;
        alertDescription.value = description;
        showAlert.value = true;
        setTimeout(() => showAlert.value = false, timeout);
    };

    const showErrorAlert = (title: string, description: string) => {
        alertType.value = 'error';
        alertTitle.value = title;
        alertDescription.value = description;
        showAlert.value = true;
        setTimeout(() => showAlert.value = false, timeout);
    };

    const closeAlert = () => {
        showAlert.value = false;
    };

    return {
        showAlert,
        alertType,
        alertTitle,
        alertDescription,
        showSuccessAlert,
        showErrorAlert,
        closeAlert
    };
}
