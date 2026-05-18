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

const jsPieces = [
    ['datatables.net', 'js', 'jquery.dataTables.min.js'],
    ['datatables.net-bs5', 'js', 'dataTables.bootstrap5.min.js'],
    ['datatables.net-buttons', 'js', 'dataTables.buttons.min.js'],
    ['datatables.net-buttons-bs5', 'js', 'buttons.bootstrap5.min.js'],
];

let out = '';
out += '/*!\n';
out += ' * DataTables vendor bundle (built by scripts/copy-datatables.cjs)\n';
out += ' * datatables.net + datatables.net-bs5 + Buttons + buttons-bs5 styling.\n';
out += ' * Does not include JSZip/pdfmake (add buttons.html5 separately if needed).\n';
out += ' */\n';

for (const parts of jsPieces) {
    const file = path.join(nm, ...parts);
    if (!fs.existsSync(file)) {
        console.error('Missing file:', file);
        process.exit(1);
    }
    out += fs.readFileSync(file, 'utf8');
    out += '\n';
}

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
