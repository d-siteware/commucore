<!-- Styles from branding -->
<style>
    :root {
        /* Direkt FluxUI + Tailwind Variablen setzen */
        --accent: {{ setting('branding.light.accent') }};
        --accent-foreground: {{ setting('branding.light.accent_foreground') }};
        --accent-content: {{ setting('branding.light.accent_content') }};
        --primary: {{ setting('branding.light.primary') }};
        --secondary: {{ setting('branding.light.secondary') }};
        --brand: {{ setting('branding.light.brand') }};
        --bg: {{ setting('branding.light.bg') }};
        --text: {{ setting('branding.light.text') }};
        --positive: {{ setting('branding.light.positive') }};
        --negative: {{ setting('branding.light.negative') }};
        --storno: {{ setting('branding.light.storno') }};

        /* Für Tailwind */
        --color-accent: {{ setting('branding.light.accent') }};
        --color-accent-foreground: {{ setting('branding.light.accent_foreground') }};
        --color-accent-content: {{ setting('branding.light.accent_content') }};
        --color-primary: {{ setting('branding.light.primary') }};
        --color-secondary: {{ setting('branding.light.secondary') }};
        --color-brand: {{ setting('branding.light.brand') }};
        --color-bg: {{ setting('branding.light.bg') }};
        --color-text: {{ setting('branding.light.text') }};
        --color-positive: {{ setting('branding.light.positive') }};
        --color-negative: {{ setting('branding.light.negative') }};
        --color-storno: {{ setting('branding.light.storno') }};
    }

    .dark {
        --accent: {{ setting('branding.dark.accent') }};
        --accent-foreground: {{ setting('branding.dark.accent_foreground') }};
        --accent-content: {{ setting('branding.dark.accent_content') }};
        --primary: {{ setting('branding.dark.primary') }};
        --secondary: {{ setting('branding.dark.secondary') }};
        --brand: {{ setting('branding.dark.brand') }};
        --bg: {{ setting('branding.dark.bg') }};
        --text: {{ setting('branding.dark.text') }};
        --positive: {{ setting('branding.dark.positive') }};
        --negative: {{ setting('branding.dark.negative') }};
        --storno: {{ setting('branding.dark.storno') }};

        /* Für Tailwind */
        --color-accent: {{ setting('branding.dark.accent') }};
        --color-accent-foreground: {{ setting('branding.dark.accent_foreground') }};
        --color-accent-content: {{ setting('branding.dark.accent_content') }};
        --color-primary: {{ setting('branding.dark.primary') }};
        --color-secondary: {{ setting('branding.dark.secondary') }};
        --color-brand: {{ setting('branding.dark.brand') }};
        --color-bg: {{ setting('branding.dark.bg') }};
        --color-text: {{ setting('branding.dark.text') }};
        --color-positive: {{ setting('branding.dark.positive') }};
        --color-negative: {{ setting('branding.dark.negative') }};
        --color-storno: {{ setting('branding.dark.storno') }};
    }
</style>