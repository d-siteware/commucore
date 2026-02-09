<!-- Styles from branding -->
<style>
    :root {
        /* Direkt FluxUI + Tailwind Variablen setzen */
        --accent: {{ setting('branding.colors.light.accent') }};
        --accent-foreground: {{ setting('branding.colors.light.accent_foreground') }};
        --accent-content: {{ setting('branding.colors.light.accent_content') }};
        --primary: {{ setting('branding.colors.light.primary') }};
        --secondary: {{ setting('branding.colors.light.secondary') }};
        --brand: {{ setting('branding.colors.light.brand') }};
        --bg: {{ setting('branding.colors.light.bg') }};
        --text: {{ setting('branding.colors.light.text') }};
        --positive: {{ setting('branding.colors.light.positive') }};
        --negative: {{ setting('branding.colors.light.negative') }};
        --storno: {{ setting('branding.colors.light.storno') }};

        /* Für Tailwind */
        --color-accent: {{ setting('branding.colors.light.accent') }};
        --color-accent-foreground: {{ setting('branding.colors.light.accent_foreground') }};
        --color-accent-content: {{ setting('branding.colors.light.accent_content') }};
        --color-primary: {{ setting('branding.colors.light.primary') }};
        --color-secondary: {{ setting('branding.colors.light.secondary') }};
        --color-brand: {{ setting('branding.colors.light.brand') }};
        --color-bg: {{ setting('branding.colors.light.bg') }};
        --color-text: {{ setting('branding.colors.light.text') }};
        --color-positive: {{ setting('branding.colors.light.positive') }};
        --color-negative: {{ setting('branding.colors.light.negative') }};
        --color-storno: {{ setting('branding.colors.light.storno') }};
    }

    .dark {
        --accent: {{ setting('branding.colors.dark.accent') }};
        --accent-foreground: {{ setting('branding.colors.dark.accent_foreground') }};
        --accent-content: {{ setting('branding.colors.dark.accent_content') }};
        --primary: {{ setting('branding.colors.dark.primary') }};
        --secondary: {{ setting('branding.colors.dark.secondary') }};
        --brand: {{ setting('branding.colors.dark.brand') }};
        --bg: {{ setting('branding.colors.dark.bg') }};
        --text: {{ setting('branding.colors.dark.text') }};
        --positive: {{ setting('branding.colors.dark.positive') }};
        --negative: {{ setting('branding.colors.dark.negative') }};
        --storno: {{ setting('branding.colors.dark.storno') }};

        /* Für Tailwind */
        --color-accent: {{ setting('branding.colors.dark.accent') }};
        --color-accent-foreground: {{ setting('branding.colors.dark.accent_foreground') }};
        --color-accent-content: {{ setting('branding.colors.dark.accent_content') }};
        --color-primary: {{ setting('branding.colors.dark.primary') }};
        --color-secondary: {{ setting('branding.colors.dark.secondary') }};
        --color-brand: {{ setting('branding.colors.dark.brand') }};
        --color-bg: {{ setting('branding.colors.dark.bg') }};
        --color-text: {{ setting('branding.colors.dark.text') }};
        --color-positive: {{ setting('branding.colors.dark.positive') }};
        --color-negative: {{ setting('branding.colors.dark.negative') }};
        --color-storno: {{ setting('branding.colors.dark.storno') }};
    }
</style>