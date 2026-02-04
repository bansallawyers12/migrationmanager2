import './bootstrap';

import Alpine from 'alpinejs';
import SignaturePad from 'signature_pad';
import Echo from 'laravel-echo';

// Make Alpine & SignaturePad globally available
window.Alpine = Alpine;
window.SignaturePad = SignaturePad;

Alpine.start();

/*
|--------------------------------------------------------------------------
| Laravel Echo + Reverb (PRODUCTION CONFIG)
|--------------------------------------------------------------------------
| - Uses wss only (SSL)
| - Browser connects to port 443
| - Nginx proxies /app -> Reverb (8080)
| - No Pusher dependency needed
*/

if (import.meta.env.VITE_REVERB_APP_KEY) {
    try {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,

            // Reverb public host (subdomain)
            wsHost: import.meta.env.VITE_REVERB_HOST,

            // Browser MUST use 443 in production
            wsPort: 443,
            wssPort: 443,

            // Force secure WebSocket
            forceTLS: true,
            enabledTransports: ['wss'],

            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content'),
                },
            },
        });

        console.log('✅ Laravel Echo initialized with Reverb (production)');
    } catch (error) {
        console.warn('⚠️ Failed to initialize Laravel Echo:', error);
        window.EchoDisabled = true;
    }
} else {
    window.EchoDisabled = true;
}

/*
|--------------------------------------------------------------------------
| FullCalendar v6
|--------------------------------------------------------------------------
*/

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';

// Make FullCalendar globally available for Blade templates
window.FullCalendar = { Calendar };
window.FullCalendarPlugins = {
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin,
    listPlugin,
};
