import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { bunny } from "laravel-vite-plugin/fonts";

export default defineConfig({
  plugins: [
    laravel({
      input: ["resources/scss/app.scss", "resources/js/app.js"],
      refresh: true,
      fonts: [
        bunny("Inter", {
          weights: [400, 500, 600, 700, 800, 900],   
        }),
      ],
    }),
  ],
  server: {
    watch: {
      ignored: ["**/storage/framework/views/**"],
    },
  },
});
