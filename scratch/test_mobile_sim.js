const { spawn } = require('child_process');
const http = require('http');

const edgePath = "C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe";

const edge = spawn(edgePath, [
  '--headless=new',
  '--remote-debugging-port=9223',
  '--window-size=375,812',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  'about:blank'
]);

setTimeout(async () => {
  try {
    const list = await new Promise((resolve, reject) => {
      http.get('http://127.0.0.1:9223/json/list', res => {
        let d = '';
        res.on('data', c => d += c);
        res.on('end', () => resolve(JSON.parse(d)));
      }).on('error', reject);
    });

    const wsUrl = list[0].webSocketDebuggerUrl;
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
      await send('Network.enable');
      await send('Network.setUserAgentOverride', {
        userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.5 Mobile/15E148 Safari/604.1'
      });
      await send('Emulation.setDeviceMetricsOverride', {
        width: 375,
        height: 812,
        deviceScaleFactor: 3,
        mobile: true
      });

      await send('Page.navigate', { url: 'http://localhost/expotree/event.php?id=1' });
      await new Promise(r => setTimeout(r, 2000));

      const evalRes = await send('Runtime.evaluate', {
        expression: `
          (() => {
            const docWidth = document.documentElement.scrollWidth;
            const bodyWidth = document.body.scrollWidth;
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
            return { docWidth, bodyWidth, winWidth, overflowingCount: overflowing.length, overflowing };
          })()
        `,
        returnByValue: true
      });

      console.log("MOBILE RESULT:", JSON.stringify(evalRes.result.value, null, 2));

      // Capture screenshot
      const scr = await send('Page.captureScreenshot', { format: 'png' });
      if (scr && scr.data) {
        require('fs').writeFileSync(__dirname + '/real_mobile_screenshot.png', Buffer.from(scr.data, 'base64'));
        console.log("Saved real mobile screenshot!");
      }

      ws.close();
      edge.kill();
      process.exit(0);
    };
  } catch (e) {
    console.error(e);
    edge.kill();
    process.exit(1);
  }
}, 1500);
