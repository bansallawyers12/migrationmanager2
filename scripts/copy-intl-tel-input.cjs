/**
 * Copies intl-tel-input built assets into public/vendor/intl-tel-input so CSS urls
 * (../img/*.webp) resolve correctly. Run after upgrading the package:
 * npm run copy:intl-tel-input
 *
 * v28+ ships under dist/ and publishes intlTelInputWithUtils.min.js (libphonenumber
 * bundled — no separate utils script or dynamic import).
 */
'use strict';

const fs = require('fs');
const path = require('path');

const root = path.resolve(__dirname, '..');
const srcBase = path.join(root, 'node_modules', 'intl-tel-input', 'dist');
const outBase = path.join(root, 'public', 'vendor', 'intl-tel-input');

if (!fs.existsSync(srcBase)) {
    console.error(
        'intl-tel-input dist folder missing. Run npm install. Expected:',
        path.relative(root, srcBase),
    );
    process.exit(1);
}

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

/** [subdir, sourceFilename, destFilename] — dest defaults to sourceFilename */
const fileCopies = [
    ['js', 'intlTelInputWithUtils.min.js', 'intlTelInput.min.js'],
    ['css', 'intlTelInput.css', 'intlTelInput.css'],
];

for (const [dir, fromFile, toFile] of fileCopies) {
    const destName = toFile || fromFile;
    const from = path.join(srcBase, dir, fromFile);
    const to = path.join(outBase, dir, destName);
    if (!fs.existsSync(from)) {
        console.error('Missing file:', from);
        process.exit(1);
    }
    fs.mkdirSync(path.dirname(to), { recursive: true });
    let body = fs.readFileSync(from, 'utf8');
    if (destName.endsWith('.min.js')) {
        body = banner + body;
    }
    fs.writeFileSync(to, body, 'utf8');
    console.log('Wrote', path.relative(root, to));
}

const imgFrom = path.join(srcBase, 'img');
const imgTo = path.join(outBase, 'img');
if (!fs.existsSync(imgFrom)) {
    console.error('Missing dir:', imgFrom);
    process.exit(1);
}
fs.mkdirSync(imgTo, { recursive: true });
fs.cpSync(imgFrom, imgTo, { recursive: true });
console.log('Copied', path.relative(root, imgTo), '(dir)');

const staleUtils = path.join(outBase, 'js', 'utils.js');
if (fs.existsSync(staleUtils)) {
    fs.unlinkSync(staleUtils);
    console.log('Removed stale', path.relative(root, staleUtils));
}
