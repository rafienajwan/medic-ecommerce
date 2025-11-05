import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';

let globalInstance = null;
let isInit = false;

export function useIdleDetector() {
    if (globalInstance) {
        console.log('Returning existing idle detector');
        return globalInstance;
    }

    const idleTime = ref(0);
    const maxIdleTime = 1800; // 30 minutes (reasonable for e-commerce)
    const heartbeatInterval = 240; // Ping every 4 minutes

    let idleTimer = null;
    let heartbeatTimer = null;
    let lastActivity = Date.now();

    const resetIdleTimer = () => {
        idleTime.value = 0;
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

        // No alert - just redirect to login silently
        router.visit('/login', {
            method: 'get',
            onSuccess: () => {
                sessionStorage.clear();
            }
        });
    };

    const checkIdle = () => {
        const currentTime = Date.now();
        const timeSinceLastActivity = Math.floor((currentTime - lastActivity) / 1000);

        idleTime.value = timeSinceLastActivity;

        if (timeSinceLastActivity >= maxIdleTime) {
            handleSessionExpired();
        }
    };

    const handleActivity = () => {
        resetIdleTimer();
    };

    const init = () => {
        if (isInit) {
            console.log('Already initialized');
            return;
        }

        console.log('Initializing idle detector');
        isInit = true;

        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];

        events.forEach(event => {
            document.addEventListener(event, handleActivity, true);
        });

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                resetIdleTimer();
                sendActivityPing();
            }
        });

        idleTimer = setInterval(checkIdle, 1000);

        heartbeatTimer = setInterval(() => {
            if (idleTime.value < maxIdleTime) {
                sendActivityPing();
            }
        }, heartbeatInterval * 1000);

        resetIdleTimer();
        sendActivityPing();

        console.log('Idle detector ready');
    };

    const cleanup = () => {
        if (!isInit) return;

        console.log('Cleaning up idle detector');

        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];

        events.forEach(event => {
            document.removeEventListener(event, handleActivity, true);
        });

        document.removeEventListener('visibilitychange', handleActivity);

        if (idleTimer) clearInterval(idleTimer);
        if (heartbeatTimer) clearInterval(heartbeatTimer);

        isInit = false;
        globalInstance = null;
    };

    const unsubscribe = router.on('navigate', () => {
        console.log('Page navigation - resetting timer');
        resetIdleTimer();
    });

    // DON'T auto-init! Let caller decide when to init
    // This prevents init from running when user is not authenticated

    globalInstance = {
        idleTime,
        maxIdleTime,
        resetIdleTimer,
        init,  // Expose init for manual initialization
        cleanup: () => {
            cleanup();
            if (unsubscribe) unsubscribe();
        }
    };

    console.log('Creating NEW idle detector instance (NOT initialized yet)');

    return globalInstance;
}
