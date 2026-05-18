/**
 * Syncs DataTables core + Bootstrap 5 + Buttons from node_modules into public/.
 * Re-run after changing datatables.* versions: npm run copy:datatables
 */
'use strict';

const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const nm = path.join(root, 'node_modules');
const pub = path.join(root, 'public');

const packages = [
    'datatables.net',
    'datatables.net-bs5',
    'datatables.net-buttons',
    'datatables.net-buttons-bs5',
];

const jsPieces = [
    ['datatables.net', 'js', 'jquery.dataTables.min.js'],
    ['datatables.net-bs5', 'js', 'dataTables.bootstrap5.min.js'],
    ['datatables.net-buttons', 'js', 'dataTables.buttons.min.js'],
    ['datatables.net-buttons-bs5', 'js', 'buttons.bootstrap5.min.js'],
];

function readPkgVersion(pkgName) {
    const p = path.join(nm, pkgName, 'package.json');
    if (!fs.existsSync(p)) {
        return '?';
    }
    try {
        return JSON.parse(fs.readFileSync(p, 'utf8')).version || '?';
    } catch {
        return '?';
    }
}

const versions = packages.map((name) => `${name}@${readPkgVersion(name)}`).join(', ');

const banner = `/*!
 * DataTables vendor bundle (built by scripts/copy-datatables.cjs)
 * ${versions}
 * Includes: core, BS5 integration, Buttons (for buttons.exportData()), buttons-BS5 styling.
 * Optional: add buttons.html5.min.js + JSZip/pdfmake if you need spreadsheet/PDF export buttons.
 */`;

const bodies = [];
for (const parts of jsPieces) {
    const file = path.join(nm, ...parts);
    if (!fs.existsSync(file)) {
        console.error('Missing file:', file);
        process.exit(1);
    }
    bodies.push(fs.readFileSync(file, 'utf8'));
}

// Semicolons between minified IIFEs avoid rare ASI edge cases; banner is not concatenated with ";\n".
const out = `${banner}\n${bodies.join('\n;\n')}\n`;

const outJs = path.join(pub, 'js', 'datatables.min.js');
fs.writeFileSync(outJs, out, 'utf8');
console.log('Wrote', path.relative(root, outJs));

const cssCopies = [
    [
        path.join(nm, 'datatables.net-bs5', 'css', 'dataTables.bootstrap5.min.css'),
        path.join(pub, 'css', 'dataTables.bootstrap5.min.css'),
    ],
    [
        path.join(nm, 'datatables.net-buttons-bs5', 'css', 'buttons.bootstrap5.min.css'),
        path.join(pub, 'css', 'buttons.bootstrap5.min.css'),
    ],
];

for (const [from, to] of cssCopies) {
    if (!fs.existsSync(from)) {
        console.error('Missing file:', from);
        process.exit(1);
    }
    fs.copyFileSync(from, to);
    console.log('Copied', path.relative(root, from), '->', path.relative(root, to));
}
