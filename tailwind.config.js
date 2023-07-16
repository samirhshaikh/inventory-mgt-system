const { colors } = require("tailwindcss/defaultTheme");

module.exports = {
    prefix: "",
    important: false,
    separator: ":",
    content: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
        // "./resources/sass/**/*.scss"
        "node_modules/flowbite-vue/**/*.{js,jsx,ts,tsx}",
        "node_modules/flowbite/**/*.{js,jsx,ts,tsx}",
    ],
    safelist: ["generic_vs_select", "vs__search"],
    theme: {
        extend: {
            colors: {
                gray: {
                    ...colors.gray,
                    600: "#3c3f44",
                    700: "#33363d",
                    800: "#2b2e33",
                    900: "#1b1d21",
                },
            },
            width: {
                7: "1.75rem",
                9: "2.25rem",
                1: "2.75rem",
                28: "7rem",
                36: "9rem",
                44: "11rem",
                52: "13rem",
                60: "16rem",
                72: "18rem",
            },
            opacity: {
                10: ".1",
                20: ".2",
            },
        },

        backgroundColor: (theme) => ({
            ...theme("colors"),
            "product-color": "#397D92",
            "product-color-lighter": "#b2ddea",
        }),

        borderColor: (theme) => ({
            ...theme("colors"),
            "product-color": "#397D92",
            "product-color-lighter": "#b2ddea",
        }),

        textColor: (theme) => ({
            ...theme("colors"),
            "product-color": "#397D92",
            "product-color-lighter": "#b2ddea",
        }),
    },
    variants: {
        opacity: ["responsive", "hover", "focus"],
    },
    plugins: [require("daisyui")],

    daisyui: {
        themes: false, // true: all themes | false: only light + dark | array: specific themes like this ["light", "dark", "cupcake"]
        darkTheme: "dark", // name of one of the included themes for dark mode
        base: true, // applies background color and foreground color for root element by default
        styled: true, // include daisyUI colors and design decisions for all components
        utils: true, // adds responsive and modifier utility classes
        rtl: false, // rotate style direction from left-to-right to right-to-left. You also need to add dir="rtl" to your html tag and install `tailwindcss-flip` plugin for Tailwind CSS.
        prefix: "", // prefix for daisyUI classnames (components, modifiers and responsive class names. Not colors)
        logs: true, // Shows info about daisyUI version and used config in the console when building your CSS
    },
};
