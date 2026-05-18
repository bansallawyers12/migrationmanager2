/**
 * Copies intl-tel-input built assets into public/vendor/intl-tel-input so CSS urls
 * (../img/*.webp) resolve correctly. Run after upgrading the package:
 * npm run copy:intl-tel-input
 */
'use strict';

const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const build = path.join(root, 'node_modules', 'intl-tel-input', 'build');
const outBase = path.join(root, 'public', 'vendor', 'intl-tel-input');

function readVersion() {
    const p = path.join(root, 'node_modules', 'intl-tel-input', 'package.json');
    try {
        return JSON.parse(fs.readFileSync(p, 'utf8')).version || '?';
    } catch {
        return '?';
    }
}

const v = readVersion();
const banner = `/*! intl-tel-input@${v} — synced by scripts/copy-intl-tel-input.cjs */\n`;

const fileCopies = [
    ['js', 'intlTelInput.min.js'],
    ['js', 'utils.js'],
    ['css', 'intlTelInput.css'],
];

for (const [dir, file] of fileCopies) {
    const from = path.join(build, dir, file);
    const to = path.join(outBase, dir, file);
    if (!fs.existsSync(from)) {
        console.error('Missing file:', from);
        process.exit(1);
    }
    fs.mkdirSync(path.dirname(to), { recursive: true });
    let body = fs.readFileSync(from, 'utf8');
    if (file.endsWith('.min.js')) {
        body = banner + body;
    }
    fs.writeFileSync(to, body, 'utf8');
    console.log('Wrote', path.relative(root, to));
}

const imgFrom = path.join(build, 'img');
const imgTo = path.join(outBase, 'img');
if (!fs.existsSync(imgFrom)) {
    console.error('Missing dir:', imgFrom);
    process.exit(1);
}
fs.mkdirSync(imgTo, { recursive: true });
fs.cpSync(imgFrom, imgTo, { recursive: true });
console.log('Copied', path.relative(root, imgTo), '(dir)');
