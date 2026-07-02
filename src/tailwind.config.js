/** @type {import('tailwindcss').Config} */
const colors = require("tailwindcss/colors");
const defaultTheme = require("tailwindcss/defaultTheme");
const preset = require("./vendor/filament/support/tailwind.config.preset"); // ⬅️ BARU

function withOpacityValue(variable) {
    return ({ opacityValue }) => {
        if (opacityValue === undefined) {
            return `rgb(var(${variable}))`;
        }
        return `rgb(var(${variable}) / ${opacityValue})`;
    };
}

export default {
    presets: [preset], // ⬅️ BARU
    content: [
        "./resources/**/*.blade.php",
        "./app/Filament/**/*.php",
        "./resources/views/filament/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
    ],
    darkMode: "class",
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
    ],
}
