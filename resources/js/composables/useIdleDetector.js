import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

let globalInstance = null;
let isInit = false;

export function useIdleDetector() {
    if (globalInstance) {
        return globalInstance;
    }

    const idleTime = ref(0);
    const maxIdleTime = 3600; // 60 minutes = 3600 seconds idle timeout (increased from 30 min)
    const heartbeatInterval = 120; // Ping every 120 seconds (reduced frequency)
    const showWarning = ref(false);
    const warningTime = 3540; // Show warning after 59 minutes (60s before timeout)

    let idleTimer = null;
    let heartbeatTimer = null;
    let lastActivity = Date.now();

    const resetIdleTimer = () => {
        idleTime.value = 0;
        showWarning.value = false;
        lastActivity = Date.now();
    };

    const sendActivityPing = async () => {
        try {
            await axios.post('/api/activity/ping');
        } catch (error) {
            if (error.response?.status === 401) {
                handleSessionExpired();
            }
        }
    };

    const handleSessionExpired = () => {
        if (idleTimer) clearInterval(idleTimer);
        if (heartbeatTimer) clearInterval(heartbeatTimer);

        // Redirect to login with timeout message
        router.visit('/login', {
            method: 'get',
            data: {
                timeout: true,
                message: 'Your session has expired due to inactivity (60 minutes).'
            },
            onSuccess: () => {
                sessionStorage.clear();
            }
        });
    };

    const checkIdle = () => {
        const currentTime = Date.now();
        const timeSinceLastActivity = Math.floor((currentTime - lastActivity) / 1000);

        idleTime.value = timeSinceLastActivity;

        // Show warning at 59 minutes (1 minute before logout)
        if (timeSinceLastActivity >= warningTime && !showWarning.value) {
            showWarning.value = true;
        }

        // Logout at 60 minutes
        if (timeSinceLastActivity >= maxIdleTime) {
            handleSessionExpired();
        }
    };

    const handleActivity = () => {
        resetIdleTimer();
    };

    const handleVisibilityChange = () => {
        if (!document.hidden) {
            resetIdleTimer();
            sendActivityPing();
        }
    };

    const init = () => {
        if (isInit) {
            return;
        }

        isInit = true;

        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];

        events.forEach(event => {
            document.addEventListener(event, handleActivity, true);
        });

        document.addEventListener('visibilitychange', handleVisibilityChange);

        idleTimer = setInterval(checkIdle, 1000);

        heartbeatTimer = setInterval(() => {
            if (idleTime.value < maxIdleTime) {
                sendActivityPing();
            }
        }, heartbeatInterval * 1000);

        resetIdleTimer();
        sendActivityPing();

    };

    const cleanup = () => {
        if (!isInit) return;

        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];

        events.forEach(event => {
            document.removeEventListener(event, handleActivity, true);
        });

        document.removeEventListener('visibilitychange', handleVisibilityChange);

        if (idleTimer) clearInterval(idleTimer);
        if (heartbeatTimer) clearInterval(heartbeatTimer);

        isInit = false;
        globalInstance = null;
    };

    const unsubscribe = router.on('navigate', () => {
        resetIdleTimer();
    });

    // DON'T auto-init! Let caller decide when to init
    // This prevents init from running when user is not authenticated

    globalInstance = {
        idleTime,
        maxIdleTime,
        showWarning,
        timeRemaining: ref(() => Math.max(0, maxIdleTime - idleTime.value)),
        resetIdleTimer,
        init,  // Expose init for manual initialization
        cleanup: () => {
            cleanup();
            if (unsubscribe) unsubscribe();
        }
    };

    return globalInstance;
}
