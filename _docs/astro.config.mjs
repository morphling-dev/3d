import { defineConfig } from "astro/config";
import tailwind from "@astrojs/tailwind";
import alpinejs from "@astrojs/alpinejs";

export default defineConfig({
  // Sesuaikan untuk GitHub Pages
  site: "https://indra-ranuh.github.io",
  base: "/laravel-3d",
  integrations: [tailwind(), alpinejs()],
});
