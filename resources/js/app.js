import './bootstrap';

import Alpine from 'alpinejs';
import SignaturePad from 'signature_pad';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Make global
window.Alpine = Alpine;
window.SignaturePad = SignaturePad;
window.Pusher = Pusher;

Alpine.start();

/*
|--------------------------------------------------------------------------
| Laravel Echo + Reverb (PRODUCTION SAFE)
|--------------------------------------------------------------------------
| - Reverb uses Pusher JS protocol
| - Browser connects via wss (443)
| - Nginx proxies to Reverb (8080)
*/

if (import.meta.env.VITE_REVERB_APP_KEY) {
    try {
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,

            wsHost: import.meta.env.VITE_REVERB_HOST,
            wsPort: 443,
            wssPort: 443,

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

        console.log('✅ Laravel Echo initialized with Reverb');
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

window.FullCalendar = { Calendar };
window.FullCalendarPlugins = {
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin,
    listPlugin,
};
