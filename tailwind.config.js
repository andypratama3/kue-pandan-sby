import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/**/*.js", // <-- Pastikan baris ini ada
    ],
    darkMode: "class",
    theme: {
        extend: {
            fontFamily: {
                sans: ["Plus Jakarta Sans", ...defaultTheme.fontFamily.sans],
                display: ["Sora", "Plus Jakarta Sans", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                greenlight: "#97b67d",
                greendark: "#2e5d32",
                bluedark: "#111c43",
                brand: "#6f8f5f",
                "brand-deep": "#3f5d3a",
                "brand-light": "#a9c19a",
                ink: "#1f2a33",
                mint: "#eef3ec",
                /* Balanced canvas surfaces — soft sage (light), slate-900 (dark) */
                canvas: "#f1f4ec",
                "canvas-dark": "#0f172a",
            },
        },
    },

    // Safelist yang komprehensif untuk mencegah penghapusan kelas penting
    safelist: [
        'hidden',
        'flex',
        'items-center',
        'justify-center',
        'sidebar-collapsed',
        '-translate-x-full',
        'translate-x-0',
        'w-20',
        'w-64',
        'rotate-180',
        {
            // Pola untuk semua warna yang Anda gunakan (termasuk hover & dark mode)
            pattern: /^(bg|text|border|ring|divide)-(red|green|blue|gray|slate|orange|yellow|amber|cyan|emerald|purple|indigo|rose|teal|pink|violet)-(50|100|200|300|400|500|600|700|800|900|950)$/,
            variants: ['hover', 'focus', 'active', 'dark'],
        },
        {
            // Pola untuk responsive design
            pattern: /^(sm|md|lg|xl|2xl):(hidden|block|flex|grid)$/,
        },
        'font-bold',
        'font-semibold',
        'rounded',
        'rounded-lg',
    ],

    plugins: [forms, typography],
};

