/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.{html,js,php}"],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Karla", "sans-serif"],
        serif: ["Lora", "serif"],
        display: ["Lilita One", "sans-serif"],
      },
    },
  },
  plugins: [require("@tailwindcss/forms")],
};
