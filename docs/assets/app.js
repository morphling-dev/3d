window.addEventListener("alpine:init", function () {
  //
});

/**
 * Utility: Normalize a filesystem path (convert backslashes to forward slashes).
 * This does NOT lowercase everything—keeps original case since GitHub pages are case-sensitive.
 *
 * @param {string} path
 * @returns {string}
 */
function normalizePath(path) {
  return path.replace(/\\/g, "/");
}

/**
 * Sidebar navigation structure: describes all menu sections and their markdown paths.
 */
const navigation = [
  { label: "Introduction", path: "README.md" },
  { label: "Installation", path: "installation.md" },
  {
    label: "Architecture",
    items: [
      { label: "Overview", path: "architecture/overview.md" },
      { label: "Domain Layer", path: "architecture/domain.md" },
      { label: "Application Layer", path: "architecture/application.md" },
      {
        label: "Infrastructure Layer",
        path: "architecture/infrastructure.md",
      },
      { label: "Delivery Layer", path: "architecture/delivery.md" },
    ],
  },
  {
    label: "Commands",
    items: [
      { label: "Generators", path: "commands/generators.md" },
      { label: "Management", path: "commands/management.md" },
    ],
  },
  {
    label: "Features",
    items: [
      { label: "Auto Discovery", path: "features/auto-discovery.md" },
      { label: "Deep Linking", path: "features/deep-linking.md" },
    ],
  },
];

/**
 * Tries to detect the language for a given <pre> element for Prism highlighting.
 *
 * @param {HTMLElement} pre
 * @returns {string}
 */
function getCodeLang(pre) {
  const code = pre.querySelector("code");
  if (code) {
    const langClass = Array.from(code.classList).find((cls) =>
      cls.startsWith("language-"),
    );
    return langClass ? langClass.replace("language-", "") : "";
  }
  return "";
}

/**
 * Decorates all <pre> code blocks:
 *  - Adds fake Mac window controls
 *  - Adds a copy-to-clipboard button
 */
function enhanceCodeBlocks() {
  document.querySelectorAll("pre").forEach((pre) => {
    if (pre.querySelector(".copy-btn")) return;

    const codeHeader = document.createElement("div");
    codeHeader.className = "code-header";
    codeHeader.innerHTML =
      '<span class="dot red"></span>' +
      '<span class="dot yellow"></span>' +
      '<span class="dot green"></span>';

    const copyBtn = document.createElement("button");
    copyBtn.className = "copy-btn";
    copyBtn.type = "button";
    copyBtn.innerText = "Copy";
    copyBtn.addEventListener("click", async function (e) {
      e.preventDefault();
      const codeEl = pre.querySelector("code");
      if (!codeEl) return;
      const codeText = codeEl.innerText;
      try {
        await navigator.clipboard.writeText(codeText);
        copyBtn.innerText = "Copied!";
        copyBtn.classList.add("copied");
        setTimeout(() => {
          copyBtn.innerText = "Copy";
          copyBtn.classList.remove("copied");
        }, 1300);
      } catch (err) {
        copyBtn.innerText = "Error";
        setTimeout(() => (copyBtn.innerText = "Copy"), 1200);
      }
    });

    pre.insertBefore(codeHeader, pre.firstChild);
    pre.appendChild(copyBtn);
  });
}

/**
 * Wraps <table> elements inside a scrollable div if not already wrapped.
 */
function wrapTables() {
  const contentDiv = document.getElementById("content");
  if (!contentDiv) return;
  contentDiv.querySelectorAll("table").forEach((table) => {
    const parent = table.parentElement;
    if (
      parent &&
      parent.nodeName === "DIV" &&
      (parent.style.overflowX === "auto" ||
        parent.classList.contains("table-overflow-x"))
    ) {
      return;
    }
    const wrapper = document.createElement("div");
    wrapper.className = "table-overflow-x";
    wrapper.style.overflowX = "auto";
    wrapper.style.width = "100%";
    parent.replaceChild(wrapper, table);
    wrapper.appendChild(table);
  });
}

/**
 * Loads the markdown file, renders it to #content, adds enhancements, sets location.hash.
 *
 * @param {string} path - Path to the markdown file.
 * @param {boolean} pushHash - Update window.location.hash.
 */
async function loadMarkdown(path, pushHash = true) {
  const contentDiv = document.getElementById("content");
  const normalized = normalizePath(path);
  const indexDir =
    window.location.pathname.replace(/\/index\.html$/, "/") || "./";

  contentDiv.innerHTML = `<div class="animate-pulse text-emerald-500/50 text-[10px] uppercase tracking-widest">Fetching ${normalized}...</div>`;
  try {
    const candidates = [
      normalized,
      `docs/${normalized}`,
      `${indexDir}${normalized}`,
    ];
    let response = null;
    for (const url of candidates) {
      response = await fetch(url);
      if (response.ok) break;
    }
    if (!response || !response.ok) {
      throw new Error("File not found");
    }

    const text = await response.text();
    contentDiv.innerHTML = marked.parse(text);

    if (window.Prism && typeof Prism.highlightAll === "function") {
      try {
        Prism.highlightAll();
      } catch (e) {
        console.warn("Prism highlight failed:", e);
      }
    }
    enhanceCodeBlocks();
    wrapTables();
    updateActiveLink(normalized);

    if (pushHash) {
      window.location.hash = encodeURIComponent(normalized);
    }
    window.scrollTo(0, 0);
  } catch (err) {
    console.log(err);
    contentDiv.innerHTML = `
              <div class="py-20">
                  <h1 class="text-6xl font-light text-white mb-4">404</h1>
                  <p class="text-neutral-500 mb-8">File <code class="text-emerald-500">${normalized}</code> not found.</p>
                  <div class="p-4 bg-red-500/10 border border-red-500/20 rounded-lg text-xs text-red-400">
                      Ensure <b>${normalized}</b> exists in the <b>docs/</b> folder
                  </div>
              </div>`;
  }
}

/**
 * Generates sidebar and mobile nav based on the navigation definition.
 * This function updates both desktop and mobile navs for consistency.
 */
function renderSidebar() {
  renderSidebarMenu("sidebar-nav");
  renderSidebarMenu("sidebar-nav-mobile");
}

/**
 * Helper to render the sidebar navigation to a specified nav element id.
 * @param {string} navId
 */
function renderSidebarMenu(navId) {
  const nav = document.getElementById(navId);
  if (!nav) return;
  let html = "";

  navigation.forEach((section) => {
    if (section.items) {
      html += `<div class="flex flex-col gap-3">
                  <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-600">${section.label}</span>
                  <div class="flex flex-col border-l border-neutral-900 ml-1">
                      ${section.items
                        .map(
                          (item) => `
                          <a href="#${encodeURIComponent(item.path)}" data-path="${item.path}" 
                              class="py-2 pl-4 text-sm transition hover:text-emerald-500 border-l border-transparent -ml-[1px]">
                              ${item.label}
                          </a>
                      `,
                        )
                        .join("")}
                  </div>
              </div>`;
    } else {
      html += `<a href="#${encodeURIComponent(section.path)}" data-path="${section.path}" 
                  class="text-sm font-medium transition hover:text-emerald-500 border-l border-transparent pl-4 -ml-[1px]">
                  ${section.label}
              </a>`;
    }
  });

  nav.innerHTML = html;

  nav.querySelectorAll("a[data-path]").forEach((a) => {
    a.addEventListener("click", function (e) {
      e.preventDefault();
      const path = this.getAttribute("data-path");
      console.log(window.Alpine.store.get("sidebarOpen"));

      // Fix: Use Alpine store for sidebarOpen, and always re-enable sidebar after click (mobile).
      if (navId === "sidebar-nav-mobile" && window.Alpine) {
        if (
          window.Alpine.store &&
          window.Alpine.store.get("sidebarOpen") !== undefined
        ) {
          window.Alpine.store("sidebarOpen", false); // close sidebar using Alpine's $store
        } else {
          // fallback for inline Alpine logic if store is not found
          const el = document.querySelector('[x-show="sidebarOpen"]');
          if (el) {
            el.style.display = "none";
          }
        }
        // Wait for sidebar to close before loading
        setTimeout(() => {
          loadMarkdown(path);
        }, 20);
      } else {
        loadMarkdown(path);
      }
    });
  });
}

/**
 * Highlights the currently active sidebar link in both desktop and mobile.
 * @param {string} path
 */
function updateActiveLink(path) {
  ["sidebar-nav", "sidebar-nav-mobile"].forEach((navId) => {
    document.querySelectorAll(`#${navId} a[data-path]`).forEach((a) => {
      if (normalizePath(a.getAttribute("data-path")) === normalizePath(path)) {
        a.classList.add("nav-active");
      } else {
        a.classList.remove("nav-active");
      }
    });
  });
}

/**
 * Resolves the current markdown file from hash fragment; defaults to README.md if not found.
 * @returns {string}
 */
function currentMarkdownFromHash() {
  let hash = decodeURIComponent(window.location.hash.replace(/^#/, ""));
  hash = hash.replace(/^\/+/, "");
  if (hash && !hash.endsWith(".md")) {
    hash = `${hash}.md`;
  }

  const allPaths = [];
  function collectPaths(items) {
    for (const i of items) {
      if (i.items) {
        collectPaths(i.items);
      } else {
        allPaths.push(normalizePath(i.path));
      }
    }
  }
  collectPaths(navigation);
  if (allPaths.includes(normalizePath(hash))) {
    return hash;
  }
  return "README.md";
}

// Listen for location hash changes and update content.
window.addEventListener("hashchange", () => {
  const md = currentMarkdownFromHash();
  loadMarkdown(md, false);
});

// On page load, render sidebars and load the initial markdown file.
window.addEventListener("DOMContentLoaded", () => {
  renderSidebar();
  loadMarkdown(currentMarkdownFromHash(), false);
});
