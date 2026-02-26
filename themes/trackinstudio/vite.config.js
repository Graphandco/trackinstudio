import { defineConfig } from "vite";

export default defineConfig({
   build: {
      outDir: "assets/css",
      emptyOutDir: false,
      rollupOptions: {
         input: "src/scss/custom.scss",
         output: {
            assetFileNames: "custom.[ext]",
         },
      },
      minify: true,
      cssMinify: true,
   },
});
