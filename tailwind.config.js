/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./app/**/*.{html,js,php}"],
  theme: {
    extend: {
      colors: {
        primary: {'10':'#0540F2', '20':'#03258C'},
        p_second: '#03258C',
        p_font: '#1B1818',
        s_font: '#ABA9A9',
        secondary: '#165FF2',
        bg_color: '#F2F2F2',
        secondary_bg: '#E5E5E5',
        icon_color: '#3574F2'
      },
    },
  },
  plugins: [],
}

