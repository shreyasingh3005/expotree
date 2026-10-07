const { execSync } = require('child_process');
const http = require('http');
const fs = require('fs');

// Launch headless edge with remote debugging
const edgePath = "C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe";

console.log("Checking page elements in mobile viewport (375px)...");

// Read rendered HTML and check for obvious fixed widths or overflow
const html = fs.readFileSync(__dirname + '/rendered_event.html', 'utf8');

// Check style patterns in html
const inlineWidths = html.match(/style="[^"]*width:[^"]*"/gi) || [];
console.log("Found inline styles with width:", inlineWidths.length);
inlineWidths.forEach(w => {
  if (!w.includes('100%') && !w.includes('auto')) {
    console.log("  Potential culprit:", w);
  }
});
