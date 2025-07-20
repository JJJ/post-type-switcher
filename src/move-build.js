const fs = require('fs');
const path = require('path');

const src = path.resolve(__dirname, '../build/index.js');
const destDir = path.resolve(__dirname, '../assets/js');
const dest = path.join(destDir, 'block.js');

fs.mkdirSync(destDir, { recursive: true });
fs.copyFileSync(src, dest);

console.log('Moved index.js → assets/js/block.js');