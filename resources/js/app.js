import './bootstrap';

import Alpine from 'alpinejs';
import SignaturePad from 'signature_pad';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Alpine = Alpine;
window.SignaturePad = SignaturePad;

Alpine.start();

// Initialize Laravel Echo with Reverb (uses Pusher protocol)
// Reverb is Laravel's native WebSocket server
// If VITE_REVERB_APP_KEY is not set, Echo will not be initialized and polling fallback will be used
if (import.meta.env.VITE_REVERB_APP_KEY) {
    try {
        window.Pusher = Pusher;
        
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: import.meta.env.VITE_REVERB_APP_KEY,
            wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname,
            wsPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
            wssPort: import.meta.env.VITE_REVERB_PORT ?? 8080,
            forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'http') === 'https',
            enabledTransports: ['ws', 'wss'],
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                },
            },
        });
        
        console.log('✅ Laravel Echo initialized with Reverb');
    } catch (error) {
        console.warn('⚠️ Failed to initialize Laravel Echo:', error.message);
        // Set flag to indicate Echo is intentionally unavailable
        window.EchoDisabled = true;
    }
} else {
    // Set flag to indicate Echo is intentionally not configured (not an error)
    window.EchoDisabled = true;
}

// FullCalendar v6 - for new booking appointments system
// Import FullCalendar core and plugins
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';

// Note: FullCalendar v6 CSS is loaded locally in the blade template

// Make FullCalendar v6 available globally for blade templates
window.FullCalendar = { Calendar };
window.FullCalendarPlugins = {
    dayGridPlugin,
    timeGridPlugin,
    interactionPlugin,
    listPlugin
};