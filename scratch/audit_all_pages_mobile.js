const { spawn } = require('child_process');
const http = require('http');

const path = require('path');
const tmpDir = path.join(__dirname, 'edge_tmp_' + Date.now());

const edge = spawn('C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe', [
  '--headless',
  '--remote-debugging-port=9222',
  `--user-data-dir=${tmpDir}`,
  '--window-size=375,812',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  'about:blank'
]);

const pages = [
  'index.php',
  'upcoming-exhibitions.php',
  'book-a-stall.php',
  'list-your-event.php',
  'free-shopper-pass.php',
  'categories.php',
  'gallery.php',
  'about-us.php',
  'contact.php',
  'faq.php',
  'event.php?id=1'
];

async function connectToEdge() {
  for (let i = 0; i < 15; i++) {
    try {
      const list = await new Promise((resolve, reject) => {
        http.get('http://127.0.0.1:9222/json/list', r => {
          let d = ''; r.on('data', c => d += c); r.on('end', () => resolve(JSON.parse(d)));
        }).on('error', reject);
      });
      if (list && list.length) return list[0].webSocketDebuggerUrl;
    } catch (e) {
      await new Promise(r => setTimeout(r, 400));
    }
  }
  throw new Error("Could not connect to Edge on port 9222");
}

(async () => {
  try {
    const wsUrl = await connectToEdge();
    const ws = new WebSocket(wsUrl);
    let id = 1;
    const send = (method, params = {}) => new Promise(res => {
      const rId = id++;
      const h = (e) => {
        const m = JSON.parse(e.data);
        if (m.id === rId) {
          ws.removeEventListener('message', h);
          res(m.result);
        }
      };
      ws.addEventListener('message', h);
      ws.send(JSON.stringify({ id: rId, method, params }));
    });

    ws.onopen = async () => {
      console.log("Testing all pages on iPhone 13 (375x812)...");
      await send('Emulation.setDeviceMetricsOverride', { width: 375, height: 812, deviceScaleFactor: 2, mobile: true });

      for (const p of pages) {
        await send('Page.navigate', { url: `http://localhost/expotree/${p}` });
        await new Promise(r => setTimeout(r, 1000));

        const res = await send('Runtime.evaluate', {
          expression: `
            (() => {
              const docWidth = document.documentElement.scrollWidth;
              const winWidth = window.innerWidth;
              const bad = [];
              document.querySelectorAll('*').forEach(el => {
                if (el.closest('#mobile-nav-drawer') || el.id === 'mobile-nav-drawer') return;
                const rect = el.getBoundingClientRect();
                if (rect.right > winWidth + 1 || el.scrollWidth > winWidth + 1 || rect.width > winWidth + 1) {
                  bad.push({ tag: el.tagName, class: el.className, w: el.scrollWidth, right: Math.round(rect.right) });
                }
              });
              return { docWidth, winWidth, ok: docWidth <= winWidth + 1 && bad.length === 0, badCount: bad.length };
            })()
          `,
          returnByValue: true
        });

        const v = res.result.value;
        const status = v.ok ? '✅ PASS' : '❌ FAIL (docWidth=' + v.docWidth + ', bad=' + v.badCount + ')';
        console.log(`${p.padEnd(28)} : ${status}`);
      }

      ws.close();
      edge.kill();
      process.exit(0);
    };
  } catch (err) {
    console.error("Error:", err);
    edge.kill();
  }
})();
