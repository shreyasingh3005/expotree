const { spawn } = require('child_process');
const http = require('http');

const edgePath = "C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe";

const edge = spawn(edgePath, [
  '--headless',
  '--remote-debugging-port=9222',
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

    const wsUrl = list[0].webSocketDebuggerUrl;
    console.log("Connecting to:", wsUrl);
    const ws = new WebSocket(wsUrl);

    let id = 1;
    function send(method, params = {}) {
      return new Promise(resolve => {
        const reqId = id++;
        const handler = (event) => {
          const msg = JSON.parse(event.data);
          if (msg.id === reqId) {
            ws.removeEventListener('message', handler);
            resolve(msg.result);
          }
        };
        ws.addEventListener('message', handler);
        ws.send(JSON.stringify({ id: reqId, method, params }));
      });
    }

    ws.onopen = async () => {
      console.log("WebSocket connected. Setting DeviceMetricsOverride to 375x812 (mobile)...");
      await send('Emulation.setDeviceMetricsOverride', {
        width: 375,
        height: 812,
        deviceScaleFactor: 2,
        mobile: true
      });

      console.log("Navigating to http://localhost/expotree/event.php?id=1...");
      await send('Page.navigate', { url: 'http://localhost/expotree/event.php?id=1' });

      // Wait for page load
      await new Promise(r => setTimeout(r, 2000));

      console.log("Evaluating elements width...");
      const evalRes = await send('Runtime.evaluate', {
        expression: `
          (() => {
            const docWidth = document.documentElement.scrollWidth;
            const winWidth = window.innerWidth;
            const overflowing = [];
            document.querySelectorAll('*').forEach(el => {
              const rect = el.getBoundingClientRect();
              if (rect.right > winWidth + 1 || el.scrollWidth > winWidth + 1) {
                overflowing.push({
                  tag: el.tagName,
                  id: el.id,
                  class: el.className,
                  scrollWidth: el.scrollWidth,
                  rectRight: Math.round(rect.right),
                  rectWidth: Math.round(rect.width)
                });
              }
            });
            return { docWidth, winWidth, overflowing: overflowing.slice(0, 20) };
          })()
        `,
        returnByValue: true
      });

      console.log("RESULT:", JSON.stringify(evalRes.result.value, null, 2));

      // Capture screenshot to artifact / scratch
      const scr = await send('Page.captureScreenshot', { format: 'png' });
      if (scr && scr.data) {
        require('fs').writeFileSync(__dirname + '/mobile_event_shot.png', Buffer.from(scr.data, 'base64'));
        console.log("Saved mobile screenshot to scratch/mobile_event_shot.png");
      }

      ws.close();
      edge.kill();
      process.exit(0);
    };
  } catch (err) {
    console.error("Error:", err);
    edge.kill();
    process.exit(1);
  }
}, 1500);
