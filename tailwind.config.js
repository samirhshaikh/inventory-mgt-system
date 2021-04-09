const { colors } = require("tailwindcss/defaultTheme");

module.exports = {
    purge: [
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.vue",
        "./resources/sass/**/*.scss"
    ],
    theme: {
        extend: {
            colors: {
                gray: {
                    ...colors.gray,
                    "600": "#3b3e45",
                    "700": "#36393f",
                    "800": "#2f3136",
                    "900": "#202225"
                }
            },
            width: {
                '7': '1.75rem',
                '9': '2.25rem',
                '1': '2.75rem',
                '28': '7rem',
                '36': '9rem',
                '44': '11rem',
                '52': '13rem',
                '60': '16rem',
                '72': '18rem'
            },
            opacity: {
                '10': '.1',
                '20': '.2'
            }
        },

        backgroundColor: theme => ({
            ...theme("colors"),
            "product-color": "#1380B6",
            "product-color-lighter": "#c7dcea"
        }),

        borderColor: theme => ({
            ...theme("colors"),
            "product-color": "#1380B6",
            "product-color-lighter": "#c7dcea"
        }),

        textColor: theme => ({
            ...theme("colors"),
            "product-color": "#1380B6",
            "product-color-lighter": "#c7dcea"
        })
    },
    variants: {
        opacity: ["responsive", "hover", "focus"]
    },
    plugins: []
};
