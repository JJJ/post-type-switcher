#!/usr/bin/env node

import { lstatSync, readFileSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { spawnSync } from 'node:child_process';
import { fileURLToPath } from 'node:url';

const repositoryRoot = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const assets = [
	'assets/js/block.js',
	'assets/js/block.asset.php',
];

const tracked = spawnSync(
	'git',
	['ls-files', '--error-unmatch', '--', ...assets],
	{ cwd: repositoryRoot, encoding: 'utf8' },
);

if (tracked.status !== 0) {
	console.error('Every generated block-editor asset must remain tracked by Git.');
	process.exit(1);
}

for (const asset of assets) {
	const status = lstatSync(resolve(repositoryRoot, asset));
	if (!status.isFile() || status.isSymbolicLink()) {
		console.error(`${asset} must be a regular tracked file.`);
		process.exit(1);
	}
}

const phpAsset = readFileSync(resolve(repositoryRoot, 'assets/js/block.asset.php'), 'utf8');
const compactPhpAssetPattern = /^<\?php return array\('dependencies' => array\((?:'[a-z0-9-]+'(?:, '[a-z0-9-]+')*)?\), 'version' => '[a-f0-9]+'\);\n$/u;

if (!compactPhpAssetPattern.test(phpAsset)) {
	console.error('The generated PHP asset metadata must use the deterministic compact format.');
	process.exit(1);
}

console.log('Generated block-editor assets remain tracked.');
