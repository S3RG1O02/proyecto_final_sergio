/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./**/*.php",
    "./index.php",
  ],
  theme: {
    extend: {
      colors: {
        'custom-blue': 'rgb(63, 72, 107)'
      },
      fontFamily: {
        // 'circular' es el nombre que asignarás a la clase que usarás en HTML
        circular: ['"CircularStd"', 'sans-serif'],
      },
    },
  },
}