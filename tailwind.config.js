/** @type {import('tailwindcss').Config} */
export default {
    content: ["./resources/**/*.blade.php"],
    theme: {
        extend: {
            fontFamily: {
                'vazir': ['vazir'],
                'iranSans': ['iranSans'],
                'yekan': ['yekan'],

            },

            spacing: {
                '25-100': '.25rem',
                '20-100': '.20rem',
                '50-100': '50%',
            },
            height: {
                '70vh': '70vh',
                '28vh': '28vh',
                '80vh': '80vh',

            },
            screens: {
                'mini-sm': '375px'
            },
            width: {
                '33-50': 'calc(33.33333333% - 50px)',
            },
            fontSize: {
                'mini-base': ['.80rem', {
                    lineHeight: '1',
                    letterSpacing: '0',
                    fontWeight: '100',
                }],
                'mini-mini-base': ['.70rem', {
                    lineHeight: '1',
                    letterSpacing: '0',
                    fontWeight: '100',
                }]
            },
            colors: {
                'base-font-color': '#F3A30D',
                'base-bg-color': '#F1F7FF',
                'FFB01B': '#FFB01B',
                'DE9408': '#DE9408',
                'FFF5EA': '#FFF5EA',
                'FFC98B': '#FFC98B',
                'FFBEBE': '#FFBEBE',
                '8EBFFC': '#8EBFFC',
                'E5F1FF': '#E5F1FF',
                'F6EDFF': '#F6EDFF',
                'DBBBFF': '#DBBBFF',
                'F4F7FB': '#F4F7FB',
                '80C714': '#80C714',
                '268832': '#268832',
                'F5F5F5': '#F5F5F5',
                'DFEDFF': '#DFEDFF',
                'CCCCCC': '#CCCCCC',
                'EEEEEE': '#EEEEEE',
                black_blur: 'rgba(0,0,0, 0.7)',

            }

        },

    },
    plugins: [],
}




