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

function compactPhpAsset( source ) {
	const match = source.match(
		/^<\?php return array\(\s*'dependencies'\s*=>\s*array\((.*?)\),\s*'version'\s*=>\s*'([a-f0-9]+)'\s*\);\s*$/su
	);

	if ( ! match ) {
		throw new Error( 'The generated PHP asset metadata has an unexpected format.' );
	}

	const dependencySource = match[ 1 ];
	const dependencies = [ ...dependencySource.matchAll( /'([a-z0-9-]+)'/gu ) ].map(
		( dependency ) => dependency[ 1 ]
	);
	const remainder = dependencySource.replace( /'[a-z0-9-]+'/gu, '' ).replace( /[\s,]/gu, '' );

	if ( remainder !== '' ) {
		throw new Error( 'The generated PHP asset metadata contains an invalid dependency.' );
	}

	const quotedDependencies = dependencies.map( ( dependency ) => `'${ dependency }'` ).join( ', ' );

	return `<?php return array('dependencies' => array(${ quotedDependencies }), 'version' => '${ match[ 2 ] }');\n`;
}

fs.mkdirSync(destinationDirectory, { recursive: true });

for ( const [ source, destination ] of assets ) {
	const sourcePath = path.resolve(repositoryRoot, 'build', source);
	const destinationPath = path.join(destinationDirectory, destination);

	if ( source.endsWith( '.asset.php' ) ) {
		fs.writeFileSync(destinationPath, compactPhpAsset(fs.readFileSync(sourcePath, 'utf8')));
	} else {
		fs.copyFileSync(sourcePath, destinationPath);
	}
}

console.log('Copied block-editor build assets into assets/js.');
