import { ref } from 'vue';

const notifications = ref([]);
let notificationId = 0;

export function useNotification() {
    const addNotification = (message, type = 'success', duration = 3000) => {
        const id = ++notificationId;
        const notification = {
            id,
            message,
            type, // 'success', 'error', 'warning', 'info'
            show: true
        };

        notifications.value.push(notification);

        // Auto remove after duration
        setTimeout(() => {
            removeNotification(id);
        }, duration);
    };

    const removeNotification = (id) => {
        const index = notifications.value.findIndex(n => n.id === id);
        if (index > -1) {
            notifications.value.splice(index, 1);
        }
    };

    const success = (message, duration) => addNotification(message, 'success', duration);
    const error = (message, duration) => addNotification(message, 'error', duration);
    const warning = (message, duration) => addNotification(message, 'warning', duration);
    const info = (message, duration) => addNotification(message, 'info', duration);

    return {
        notifications,
        addNotification,
        removeNotification,
        success,
        error,
        warning,
        info
    };
}
