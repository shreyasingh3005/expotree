const { spawn } = require('child_process');
const http = require('http');

const edgePath = "C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe";

const edge = spawn(edgePath, [
  '--headless=new',
  '--remote-debugging-port=9224',
  '--window-size=375,812',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check',
  'about:blank'
]);

setTimeout(async () => {
  try {
    const list = await new Promise((resolve, reject) => {
      http.get('http://127.0.0.1:9224/json/list', res => {
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
      await send('Page.enable');

      for (const page of ['index.php', 'upcoming-exhibitions.php', 'event.php?id=1']) {
        await send('Page.navigate', { url: 'http://localhost/expotree/' + page });
        await new Promise(r => setTimeout(r, 1500));
        const res = await send('Runtime.evaluate', {
          expression: `({
            url: window.location.href,
            winWidth: window.innerWidth,
            docWidth: document.documentElement.scrollWidth,
            bodyWidth: document.body.scrollWidth,
            viewportMeta: document.querySelector('meta[name="viewport"]')?.getAttribute('content')
          })`,
          returnByValue: true
        });
        console.log("PAGE CHECK:", res.result.value);
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
