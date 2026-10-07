const { spawn } = require('child_process');
const http = require('http');

const edgePath = "C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe";

const edge = spawn(edgePath, [
  '--headless',
  '--remote-debugging-port=9222',
  '--window-size=375,812',
  '--disable-gpu',
  '--no-first-run',
  '--no-default-browser-check'
]);

setTimeout(() => {
  http.get('http://127.0.0.1:9222/json/list', (res) => {
    let data = '';
    res.on('data', chunk => data += chunk);
    res.on('end', () => {
      const tabs = JSON.parse(data);
      const wsUrl = tabs[0]?.webSocketDebuggerUrl;
      console.log('Got WebSocket URL:', wsUrl ? 'YES' : 'NO');
      
      if (!wsUrl) {
        edge.kill();
        return;
      }
      
      const WebSocket = require('ws'); // If ws not installed, we can check native
    });
  }).on('error', (err) => {
    console.log('HTTP error:', err.message);
    edge.kill();
  });
}, 1500);
