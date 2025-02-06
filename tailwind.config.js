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
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Poppins','Segoe UI','sans-serif !important']
      },
      colors: {
        primary: {"50":"#eef4fc","100":"#dce9fa","200":"#bad3f4","300":"#97bdef","400":"#75a7e9","500":"#4591e1","600":"#3774d4","700":"#2957c7","800":"#1b3bba","900":"#0d1ead","950":"#000d91"},
        secondary: '#FBBC52',
        secondaryHover: '#F9B033'
      }
    },

  },
  plugins: [],
};