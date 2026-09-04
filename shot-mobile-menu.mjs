import { chromium } from '/Users/mmain/.npm/_npx/db89d7302a373f10/node_modules/playwright/index.mjs';
const browser = await chromium.launch();
const page = await browser.newPage({ viewport: { width: 390, height: 700 } });
await page.goto('https://ppid.test/', { ignoreHTTPSErrors: true, waitUntil: 'networkidle' });
await page.waitForTimeout(800);
await page.click('button[aria-label="Buka menu"]');
await page.waitForTimeout(500);
await page.screenshot({ path: process.argv[2] });
await browser.close();
