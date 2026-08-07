const { execFileSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const files = [];

function walk(dir) {
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    if (entry.name === 'node_modules') continue;
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) walk(full);
    if (entry.isFile() && entry.name.endsWith('.js')) files.push(full);
  }
}

walk(root);
for (const file of files) execFileSync(process.execPath, ['-c', file], { stdio: 'inherit' });
console.log('Syntax OK for ' + files.length + ' JavaScript files.');
