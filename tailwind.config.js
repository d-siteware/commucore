import tailwindcss from "tailwindcss";

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/views/event_template/*.blade.php', // Main template
        './resources/views/components/**/*.blade.php',  // Flux components
    ],
    theme: {
        colors: {
            primary: 'var(--color-primary)',
            dark_primary: 'var(--color-dark-primary)',
            secondary: 'var(--color-secondary)',
            dark_secondary: 'var(--color-dark-secondary)',
        },
        extend: {
            backgroundImage: {
                'parchment': "url('/build/assets/parchment-DBxaA9Y_.svg)", // Adjust to your image path
            },
        },
    },
    plugins: [],
};
