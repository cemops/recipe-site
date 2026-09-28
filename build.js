#!/usr/bin/env node
const fs = require("fs");
const path = require("path");
const ROOT = path.join(__dirname, "kirk");
const CATEGORIES = {
  appetizers: "Appetizers",
  entrees:    "Entrees",
  sides:      "Sides",
  soup_salad: "Soups & Salads",
  sauces:     "Sauces",
  desserts:   "Desserts",
  breakfast:  "Breakfast",
  jelayne:    "Jelayne's Recipes",
};
function isNotRecipe(cat, file) {
  if (!file.toLowerCase().endsWith(".html")) return true;
  if (file === cat + ".html") return true;
  if (file.toLowerCase() === "evernote_index.html") return true;
  return false;
}
function cleanFromFilename(file) {
  return file.replace(/^\*+/, "").replace(/\.html$/i, "").replace(/_/g, " ").replace(/\s+/g, " ").trim();
}
function extractTitle(html, file) {
  const m = html.match(/<title>([\s\S]*?)<\/title>/i);
  const t = m ? m[1].replace(/\s+/g, " ").trim() : "";
  return t || cleanFromFilename(file);
}
function extractText(html) {
  return html
    .replace(/<script[\s\S]*?<\/script>/gi, " ")
    .replace(/<style[\s\S]*?<\/style>/gi, " ")
    .replace(/<[^>]+>/g, " ")
    .replace(/&nbsp;/gi, " ").replace(/&amp;/gi, "&").replace(/&[a-z]+;/gi, " ")
    .replace(/\s+/g, " ").trim().slice(0, 3000);
}
function esc(s) {
  return String(s).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}
function categoryPage(label, items) {
  const lis = items.map(r => `      <li><a target="_blank" href="${r.url}">${esc(r.title)}</a></li>`).join("\n");
  return `<!DOCTYPE html>
<html lang="en">
<head>
<title>${esc(label)}</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-light-green.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
</head>
<body>

<div id="nav-placeholder"></div>
<script>$(function(){ $("#nav-placeholder").load("/css/nav_kirk.html"); });</script>

<div id="header-placeholder"></div>
<script>$(function(){ $("#header-placeholder").load("/css/header_kirk.html"); });</script>

<div class="w3-container w3-padding" style="margin-top: 25px;">
 <div class="w3-row">
  <div class="w3-col m2 w3-center"><br></div>
  <div class="w3-col m8 w3-container">
    <h2 class="w3-center">${esc(label)}</h2>
    <ul class="w3-theme-d1" id="fileList">
${lis}
    </ul>
  </div>
  <div class="w3-col m2 w3-center"><br></div>
 </div>
</div>

<div id="footer-placeholder"></div>
<script>$(function(){ $("#footer-placeholder").load("/css/footer_kirk.html"); });</script>

</body>
</html>
`;
}
const manifest = [];
let total = 0;
for (const [cat, label] of Object.entries(CATEGORIES)) {
  const dir = path.join(ROOT, cat);
  if (!fs.existsSync(dir)) { console.warn(`(skip) missing folder: kirk/${cat}`); continue; }
  const files = fs.readdirSync(dir).filter(f => !isNotRecipe(cat, f));
  const items = [];
  for (const file of files) {
    const html = fs.readFileSync(path.join(dir, file), "utf8");
    const title = extractTitle(html, file);
    const url = `/${cat}/${encodeURIComponent(file)}`;
    items.push({ title, url });
    manifest.push({ category: cat, categoryLabel: label, title, url, text: extractText(html) });
  }
  items.sort((a, b) => a.title.toLowerCase().localeCompare(b.title.toLowerCase()));
  fs.writeFileSync(path.join(dir, cat + ".html"), categoryPage(label, items));
  console.log(`${label}: ${items.length} recipes -> kirk/${cat}/${cat}.html`);
  total += items.length;
}
manifest.sort((a, b) => a.title.toLowerCase().localeCompare(b.title.toLowerCase()));
fs.writeFileSync(path.join(ROOT, "recipes.json"), JSON.stringify(manifest));
const kb = (fs.statSync(path.join(ROOT, "recipes.json")).size / 1024).toFixed(1);
console.log(`\nTotal: ${total} recipes across ${Object.keys(CATEGORIES).length} categories`);
console.log(`Wrote kirk/recipes.json (${kb} KB)`);
