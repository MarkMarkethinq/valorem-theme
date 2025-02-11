module.exports = {
  darkMode: 'false',
  content: [
    "./*.php",
    "./templates/**/*.php",
    "./template-parts/**/*.php",
    "./assets/js/**/*.js",
    "./includes/popups/**/*.php",
    "./includes/acf-fields/**/*.php",
    "./resources/blocks/*.php",
    "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter','Segoe UI','sans-serif !important']
      },
      colors: {
        primary: {"50":"#eff6ff","100":"#dbeafe","200":"#bfdbfe","300":"#93c5fd","400":"#60a5fa","500":"#3b82f6","600":"#2563eb","700":"#1d4ed8","800":"#1e40af","900":"#263a57","950":"#172554"}
      }
    },

  },
  plugins: [
    require('flowbite/plugin')
  ],
};