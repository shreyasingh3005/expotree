const { spawn } = require('child_process');
const http = require('http');
const fs = require('fs');
const path = require('path');

const tmpDir = path.join(__dirname, 'edge_tmp');
if (!fs.existsSync(tmpDir)) fs.mkdirSync(tmpDir, { recursive: true });

const edgePath = "C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe";

const edge = spawn(edgePath, [
  '--headless',
  '--remote-debugging-port=9222',
  `--user-data-dir=${tmpDir}`,
  '--window-size=375,812',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  'about:blank'
]);

setTimeout(async () => {
  try {
    const list = await new Promise((resolve, reject) => {
      http.get('http://127.0.0.1:9222/json/list', res => {
        let d = '';
        res.on('data', c => d += c);
        res.on('end', () => resolve(JSON.parse(d)));
      }).on('error', reject);
    });

    const target = list.find(t => t.type === 'page') || list[0];
    const wsUrl = target.webSocketDebuggerUrl;
    console.log("WebSocket URL:", wsUrl);

    // Native WebSocket in Node 21+
    const ws = new WebSocket(wsUrl);

    let id = 1;
    function send(method, params = {}) {
      return new Promise((resolve, reject) => {
        const reqId = id++;
        const timer = setTimeout(() => reject(new Error(`Timeout ${method}`)), 10000);
        const handler = (event) => {
          const msg = JSON.parse(event.data);
          if (msg.id === reqId) {
            clearTimeout(timer);
            ws.removeEventListener('message', handler);
            if (msg.error) reject(msg.error);
            else resolve(msg.result);
          }
        };
        ws.addEventListener('message', handler);
        ws.send(JSON.stringify({ id: reqId, method, params }));
      });
    }

    const viewports = [
      { name: 'iPhone SE (320px)', width: 320, height: 568 },
      { name: 'Galaxy S (360px)', width: 360, height: 740 },
      { name: 'iPhone 13 (375px)', width: 375, height: 812 },
      { name: 'Pixel 7 (412px)', width: 412, height: 915 },
      { name: 'iPad (768px)', width: 768, height: 1024 }
    ];

    ws.onopen = async () => {
      console.log("Connected to browser. Testing multiple mobile viewports...");
      
      for (const vp of viewports) {
        await send('Emulation.setDeviceMetricsOverride', {
          width: vp.width,
          height: vp.height,
          deviceScaleFactor: 2,
          mobile: true
        });

        await send('Page.navigate', { url: 'http://localhost/expotree/event.php?id=1' });
        await new Promise(r => setTimeout(r, 1200));

        const evalRes = await send('Runtime.evaluate', {
          expression: `
            (() => {
              const docWidth = document.documentElement.scrollWidth;
              const bodyWidth = document.body.scrollWidth;
              const winWidth = window.innerWidth;
              const bad = [];
              document.querySelectorAll('*').forEach(el => {
                if (el.closest('#mobile-nav-drawer') || el.id === 'mobile-nav-drawer') return;
                const rect = el.getBoundingClientRect();
                if (rect.right > winWidth + 1 || el.scrollWidth > winWidth + 1 || rect.width > winWidth + 1) {
                  bad.push({ tag: el.tagName, id: el.id, class: (el.className && typeof el.className === 'string') ? el.className.trim() : '', w: el.scrollWidth, right: Math.round(rect.right), rectWidth: Math.round(rect.width) });
                }
              });
              return { winWidth, docWidth, bodyWidth, badCount: bad.length, bad: bad.slice(0, 5) };
            })()
          `,
          returnByValue: true
        });

        console.log("=== " + vp.name + " ===");
        console.log(JSON.stringify(evalRes.result.value, null, 2));
      }

      // Capture screenshots at 375: scrolled down to check cards & form
      await send('Emulation.setDeviceMetricsOverride', { width: 375, height: 812, deviceScaleFactor: 2, mobile: true });
      await send('Page.navigate', { url: 'http://localhost/expotree/event.php?id=1' });
      await new Promise(r => setTimeout(r, 1000));
      
      // Scroll down to cards
      await send('Runtime.evaluate', { expression: 'window.scrollTo(0, 500)' });
      await new Promise(r => setTimeout(r, 300));
      const scr2 = await send('Page.captureScreenshot', { format: 'png' });
      fs.writeFileSync(path.join(__dirname, 'mobile_event_shot_mid.png'), Buffer.from(scr2.data, 'base64'));

      // Scroll down to booking form bottom
      await send('Runtime.evaluate', { expression: 'window.scrollTo(0, 3300)' });
      await new Promise(r => setTimeout(r, 400));
      const scr5 = await send('Page.captureScreenshot', { format: 'png' });
      fs.writeFileSync(path.join(__dirname, 'mobile_event_shot_submit.png'), Buffer.from(scr5.data, 'base64'));

      console.log("Saved submit screenshot!");

      ws.close();
      edge.kill();
      process.exit(0);
    };
  } catch (err) {
    console.error("CDP Error:", err);
    edge.kill();
    process.exit(1);
  }
}, 1500);
