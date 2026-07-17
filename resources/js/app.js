import './bootstrap';
import Sort from '@alpinejs/sort';
import.meta.glob([
    '../images/**',
    '../images/favicons/**',
])

// Alpine-Plugins bei Livewire registrieren (Livewire bündelt Alpine)
document.addEventListener('livewire:init', () => {
    window.Alpine.plugin(Sort);
});

window.addEventListener('branding-preview', e => {
    for (const [key, value] of Object.entries(e.detail)) {
        document.documentElement.style.setProperty(
            `--branding-${key}`,
            value
        );
        console.log(`Ändere --branding-${key}` )
    }
});



    document.addEventListener("navigate-to", function (event) {
        console.log("Event received:", event.detail); // Debugging
        const url = event.detail;

        if (window.Livewire && Livewire.navigate) {
            console.log("Navigating via Livewire.navigate:", url);
            Livewire.navigate(url);
        } else {
            console.log("Navigating via window.location.href:", url);
            window.location.href = url; // Fallback
        }
    });

