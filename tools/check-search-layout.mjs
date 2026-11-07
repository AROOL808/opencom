// Local synthetic preview only; launch a disposable Chromium profile on port 55440.
import fs from 'node:fs/promises';
import path from 'node:path';
import { pathToFileURL, fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const tabs = await (await fetch('http://127.0.0.1:55440/json')).json();
const tab = tabs.find(t => t.type === 'page');
if (!tab) throw new Error('No disposable preview page');
const ws = new WebSocket(tab.webSocketDebuggerUrl);
await new Promise((resolve, reject) => { ws.addEventListener('open', resolve, { once: true }); ws.addEventListener('error', reject, { once: true }); });
let id = 0;
const pending = new Map();
ws.addEventListener('message', event => {
    const message = JSON.parse(event.data);
    const handler = pending.get(message.id);
    if (handler) { pending.delete(message.id); message.error ? handler.reject(new Error(JSON.stringify(message.error))) : handler.resolve(message.result); }
});
function send(method, params = {}) {
    return new Promise((resolve, reject) => {
        const requestId = ++id;
        const timer = setTimeout(() => { pending.delete(requestId); reject(new Error('CDP timeout: ' + method)); }, 15000);
        pending.set(requestId, { resolve: result => { clearTimeout(timer); resolve(result); }, reject: error => { clearTimeout(timer); reject(error); } });
        ws.send(JSON.stringify({ id: requestId, method, params }));
    });
}
try {
    await send('Page.enable');
    await send('Page.navigate', { url: pathToFileURL(path.join(root, 'storage/framework/testing/search-preview.html')).href });
    for (const [name, width] of [['desktop', 1440], ['mobile', 390]]) {
        await send('Emulation.setDeviceMetricsOverride', { width, height: 1100, deviceScaleFactor: 1, mobile: width < 600 });
        await send('Runtime.evaluate', { expression: "new Promise(resolve => requestAnimationFrame(() => requestAnimationFrame(resolve)))", awaitPromise: true });
        const { result } = await send('Runtime.evaluate', { expression: 'JSON.stringify({viewport:innerWidth,width:document.documentElement.scrollWidth,height:document.documentElement.scrollHeight,background:getComputedStyle(document.body).backgroundColor})', returnByValue: true });
        const dimensions = JSON.parse(result.value);
        if (dimensions.width > width || dimensions.viewport !== width) throw new Error('Overflow: ' + JSON.stringify(dimensions));
        const screenshot = await send('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true, clip: { x: 0, y: 0, width, height: Math.min(1800, dimensions.height), scale: 1 } });
        await fs.writeFile(path.join(root, 'storage/framework/testing/search-' + name + '.png'), Buffer.from(screenshot.data, 'base64'));
        console.log(name, dimensions);
    }
} finally {
    await send('Browser.close').catch(() => {});
    ws.close();
}
