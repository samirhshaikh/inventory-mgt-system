const { colors } = require("tailwindcss/defaultTheme");

module.exports = {
    purge: {
        content: [
            "./resources/views/**/*.blade.php",
            "./resources/js/**/*.vue",
            // "./resources/sass/**/*.scss"
        ],
        safelist: [
            'generic_vs_select',
            'vs__search'
        ]
    },
    theme: {
        extend: {
            colors: {
                gray: {
                    ...colors.gray,
                    "600": "#3c3f44",
                    "700": "#33363d",
                    "800": "#2b2e33",
                    "900": "#1b1d21"
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
            "product-color": "#397D92",
            "product-color-lighter": "#b2ddea"
        }),

        borderColor: theme => ({
            ...theme("colors"),
            "product-color": "#397D92",
            "product-color-lighter": "#b2ddea"
        }),

        textColor: theme => ({
            ...theme("colors"),
            "product-color": "#397D92",
            "product-color-lighter": "#b2ddea"
        })
    },
    variants: {
        opacity: ["responsive", "hover", "focus"]
    },
    plugins: []
};
