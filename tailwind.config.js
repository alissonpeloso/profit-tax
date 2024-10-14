import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";
import colors from "tailwindcss/colors.js";

/** @type {import("tailwindcss").Config} */
export default {

    presets: [
        require("./vendor/wireui/wireui/tailwind.config.js")
    ],
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./vendor/laravel/jetstream/**/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./vendor/wireui/wireui/src/*.php",
        "./vendor/wireui/wireui/ts/**/*.ts",
        "./vendor/wireui/wireui/src/WireUi/**/*.php",
        "./vendor/wireui/wireui/src/Components/**/*.php"
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans]
            },
            colors: {
                primary: colors.indigo,
                secondary: colors.gray,
                gray: colors.gray,
                success: colors.green,
                danger: colors.red,
                warning: colors.yellow,
                info: colors.blue,
                light: colors.gray,
                dark: colors.dark,
                white: colors.white,
                black: colors.black,
            },
        }
    },

    plugins: [
        forms,
        typography,
        require('daisyui'),
    ]
};
