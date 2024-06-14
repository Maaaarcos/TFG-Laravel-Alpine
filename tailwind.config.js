/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        borgoña: {
          claro: '#a62030', // Tono más claro
          predeterminado: '#800020', // Tono base
          oscuro: '#4c000d', // Tono más oscuro
          saturado: '#9d001e', // Tono más saturado
          insaturado: '#600017', // Tono menos saturado
          personalizado: '#b35266', // Tono personalizado
      },

        verde: {
          claro: '#00b933', // Tono más claro
          predeterminado: '#008012', // Tono base
          oscuro: '#005c00', // Tono más oscuro
          saturado: '#00991a', // Tono más saturado
          insaturado: '#006609', // Tono menos saturado
          personalizado: '#00a326', // Tono personalizado
      },

      rojo: {
        claro: '#cc3333', // Tono más claro
        predeterminado: '#800000', // Tono base
        oscuro: '#660000', // Tono más oscuro
        saturado: '#cc0000', // Tono más saturado
        insaturado: '#990000', // Tono menos saturado
        personalizado: '#b30000', // Tono personalizado
      },
      
      blanco: {
          predeterminado: '#ffffff', // Tono base
          floral: '#F5F5DC', // Tono más suavizado
      },

      gris: {
        claro: '#757575', // Tono más claro
        predeterminado: '#4F4F4F', // Tono base
        oscuro: '#333333', // Tono más oscuro
        saturado: '#5F5F5F', // Tono más saturado
        insaturado: '#404040', // Tono menos saturado
        personalizado: '#444444', // Tono personalizado
      },


    },
  },
  plugins: [],
}
}
