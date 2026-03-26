import {
  fetchMarkdown,
  decorateContent,
  normalizePath,
} from "../src/lib/docs.js";

document.addEventListener("alpine:init", () => {
  Alpine.store("docs", {
    currentPath: "README.md",
    sidebarOpen: false,

    async load(path) {
      const contentDiv = document.getElementById("content");
      this.currentPath = path;
      this.sidebarOpen = false; // Close mobile sidebar

      window.location.hash = encodeURIComponent(path);

      try {
        const html = await fetchMarkdown(path);
        contentDiv.innerHTML = html;

        // Prism & Decoration
        if (window.Prism) Prism.highlightAll();
        decorateContent(contentDiv);

        window.scrollTo({ top: 0, behavior: "smooth" });
      } catch (err) {
        contentDiv.innerHTML = `<div class="py-20 text-center">
                    <h1 class="text-4xl text-white font-bold">404</h1>
                    <p class="text-neutral-500">${err.message}</p>
                </div>`;
      }
    },
  });

  // Handler for Initial Load & Hash Change
  const handleRouting = () => {
    const hash =
      decodeURIComponent(window.location.hash.replace("#", "")) || "README.md";
    Alpine.store("docs").load(hash);
  };

  window.addEventListener("hashchange", handleRouting);
  window.addEventListener("DOMContentLoaded", handleRouting);
});
