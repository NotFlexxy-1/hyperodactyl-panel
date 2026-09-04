const withOpacity = (variable) => ({ opacityValue }) =>
    opacityValue === undefined ? `rgb(var(${variable}))` : `rgb(var(${variable}) / ${opacityValue})`;

// Legacy grey scale kept for backwards compatibility with existing components.
const gray = {
    50: 'hsl(216, 33%, 97%)',
    100: 'hsl(214, 15%, 91%)',
    200: 'hsl(210, 16%, 82%)',
    300: 'hsl(211, 13%, 65%)',
    400: 'hsl(211, 10%, 53%)',
    500: 'hsl(211, 12%, 43%)',
    600: 'hsl(209, 14%, 37%)',
    700: 'hsl(209, 18%, 30%)',
    800: 'hsl(209, 20%, 25%)',
    900: 'hsl(210, 24%, 16%)',
};

const colors = require('tailwindcss/colors');

module.exports = {
    content: [
        './resources/scripts/**/*.{js,ts,tsx}',
    ],
    theme: {
        extend: {
            fontFamily: {
                header: ['"Cabinet Grotesk"', '"IBM Plex Sans"', '"Roboto"', 'system-ui', 'sans-serif'],
                sans: ['"Inter"', '"IBM Plex Sans"', 'system-ui', 'sans-serif'],
            },
            colors: {
                black: '#0a0d12',
                // Deprecated aliases kept so existing components keep compiling.
                primary: colors.blue,
                gray: gray,
                neutral: gray,
                cyan: colors.cyan,

                // === Hyperodactyl design tokens (CSS-variable driven, overridable at runtime) ===
                brand: {
                    DEFAULT: withOpacity('--hyper-brand'),
                    50: withOpacity('--hyper-brand-50'),
                    100: withOpacity('--hyper-brand-100'),
                    400: withOpacity('--hyper-brand-400'),
                    500: withOpacity('--hyper-brand'),
                    600: withOpacity('--hyper-brand-600'),
                    700: withOpacity('--hyper-brand-700'),
                },
                accent: withOpacity('--hyper-accent'),
                surface: {
                    0: withOpacity('--hyper-surface-0'),
                    1: withOpacity('--hyper-surface-1'),
                    2: withOpacity('--hyper-surface-2'),
                    3: withOpacity('--hyper-surface-3'),
                },
                border: {
                    DEFAULT: withOpacity('--hyper-border'),
                    strong: withOpacity('--hyper-border-strong'),
                },
                text: {
                    DEFAULT: withOpacity('--hyper-text'),
                    muted: withOpacity('--hyper-text-muted'),
                    subtle: withOpacity('--hyper-text-subtle'),
                },
                success: withOpacity('--hyper-success'),
                warning: withOpacity('--hyper-warning'),
                danger: withOpacity('--hyper-danger'),
                info: withOpacity('--hyper-info'),
            },
            fontSize: {
                '2xs': '0.625rem',
            },
            borderRadius: {
                xl: '0.875rem',
                '2xl': '1.25rem',
                '3xl': '1.75rem',
            },
            boxShadow: {
                soft: '0 1px 2px 0 rgb(0 0 0 / 0.35), 0 8px 24px -8px rgb(0 0 0 / 0.45)',
                card: '0 1px 1px rgb(0 0 0 / 0.2), 0 12px 32px -12px rgb(0 0 0 / 0.55)',
                glow: '0 0 0 1px rgb(var(--hyper-brand) / 0.4), 0 0 24px 0 rgb(var(--hyper-brand) / 0.35)',
                'glow-sm': '0 0 0 1px rgb(var(--hyper-brand) / 0.35), 0 0 12px 0 rgb(var(--hyper-brand) / 0.25)',
            },
            backgroundImage: {
                'brand-gradient': 'linear-gradient(135deg, rgb(var(--hyper-brand)) 0%, rgb(var(--hyper-accent)) 100%)',
                'surface-gradient': 'linear-gradient(180deg, rgb(var(--hyper-surface-2)) 0%, rgb(var(--hyper-surface-1)) 100%)',
                'noise-grid': 'radial-gradient(circle at 1px 1px, rgb(255 255 255 / 0.06) 1px, transparent 0)',
            },
            backdropBlur: {
                xs: '2px',
            },
            transitionDuration: {
                250: '250ms',
            },
            keyframes: {
                'fade-in': { from: { opacity: 0 }, to: { opacity: 1 } },
                'fade-in-up': { from: { opacity: 0, transform: 'translateY(6px)' }, to: { opacity: 1, transform: 'translateY(0)' } },
                'slide-in-right': { from: { opacity: 0, transform: 'translateX(16px)' }, to: { opacity: 1, transform: 'translateX(0)' } },
                'scale-in': { from: { opacity: 0, transform: 'scale(0.97)' }, to: { opacity: 1, transform: 'scale(1)' } },
                shimmer: { from: { backgroundPosition: '-200% 0' }, to: { backgroundPosition: '200% 0' } },
                'pulse-glow': {
                    '0%, 100%': { boxShadow: '0 0 0 0 rgb(var(--hyper-brand) / 0.4)' },
                    '50%': { boxShadow: '0 0 0 6px rgb(var(--hyper-brand) / 0)' },
                },
            },
            animation: {
                'fade-in': 'fade-in 200ms ease-out both',
                'fade-in-up': 'fade-in-up 250ms ease-out both',
                'slide-in-right': 'slide-in-right 220ms cubic-bezier(0.16, 1, 0.3, 1) both',
                'scale-in': 'scale-in 180ms cubic-bezier(0.16, 1, 0.3, 1) both',
                shimmer: 'shimmer 2s linear infinite',
                'pulse-glow': 'pulse-glow 2s ease-in-out infinite',
            },
            borderColor: theme => ({
                default: theme('colors.neutral.400', 'currentColor'),
            }),
        },
    },
    plugins: [
        require('@tailwindcss/line-clamp'),
        require('@tailwindcss/forms')({
            strategy: 'class',
        }),
    ]
};
