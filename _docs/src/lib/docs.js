import { marked } from "marked";

/**
 * Normalisasi Path untuk GitHub Pages (Case-Sensitive Safe)
 */
export const normalizePath = (path) => path.replace(/\\/g, "/");

/**
 * Fetch & Render Markdown
 */
export async function fetchMarkdown(path) {
  const normalized = normalizePath(path);
  // Mencari file di folder /public/docs/
  const response = await fetch(`/docs/${normalized}`);

  if (!response.ok) throw new Error(`File ${normalized} not found`);

  const text = await response.text();
  return marked.parse(text);
}

/**
 * Enhance UI Elements (Code Blocks & Tables)
 */
export function decorateContent(container) {
  // 1. Wrap Tables for Responsiveness
  container.querySelectorAll("table").forEach((table) => {
    if (table.parentElement.classList.contains("table-wrapper")) return;
    const wrapper = document.createElement("div");
    wrapper.className =
      "table-wrapper overflow-x-auto my-6 border border-neutral-800 rounded-xl";
    table.parentNode.insertBefore(wrapper, table);
    wrapper.appendChild(table);
  });

  // 2. Add Mac-style Header & Copy Button to Code Blocks
  container.querySelectorAll("pre").forEach((pre) => {
    if (pre.querySelector(".code-header")) return;

    // Create Header
    const header = document.createElement("div");
    header.className =
      "flex items-center justify-between px-4 py-2 bg-[#0d0d0d] border-b border-neutral-800 rounded-t-xl";
    header.innerHTML = `
      <div class="flex gap-1.5">
        <span class="w-3 h-3 rounded-full bg-[#ff5f56]"></span>
        <span class="w-3 h-3 rounded-full bg-[#ffbd2e]"></span>
        <span class="w-3 h-3 rounded-full bg-[#27c93f]"></span>
      </div>
      <button class="copy-btn text-[10px] font-bold uppercase tracking-widest text-neutral-500 hover:text-emerald-500 transition">Copy</button>
    `;

    // Copy Logic
    header.querySelector(".copy-btn").onclick = async (e) => {
      const btn = e.target;
      const code = pre.querySelector("code").innerText;
      await navigator.clipboard.writeText(code);
      btn.innerText = "Copied!";
      setTimeout(() => (btn.innerText = "Copy"), 2000);
    };

    pre.prepend(header);
    pre.classList.add("!pt-0", "!rounded-t-none");
  });
}
