#!/usr/bin/env node

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const repositoryRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const destinationDirectory = path.resolve(repositoryRoot, 'assets/js');
const assets = new Map([
	[ 'index.js', 'block.js' ],
	[ 'index.asset.php', 'block.asset.php' ],
]);

fs.mkdirSync(destinationDirectory, { recursive: true });

for ( const [ source, destination ] of assets ) {
	fs.copyFileSync(
		path.resolve(repositoryRoot, 'build', source),
		path.join(destinationDirectory, destination)
	);
}

console.log('Copied block-editor build assets into assets/js.');
