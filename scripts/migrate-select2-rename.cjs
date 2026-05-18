'use strict';
/**
 * Rename Select2-named API/classes -> mmSelect / mm-select.
 * Run: node scripts/migrate-select2-rename.cjs
 */
const fs = require('fs');
const path = require('path');

const ROOT = path.resolve(__dirname, '..');
const SKIP_DIRS = new Set(['node_modules', '.git', 'vendor', 'storage', 'bootstrap/cache', '.cursor']);
const EXT = /\.(js|cjs|mjs|blade\.php|php|css|vue)$/i;

function walk(dir, out = []) {
  let entries;
  try {
    entries = fs.readdirSync(dir, { withFileTypes: true });
  } catch {
    return out;
  }
  for (const e of entries) {
    const p = path.join(dir, e.name);
    if (e.isDirectory()) {
      if (!SKIP_DIRS.has(e.name)) walk(p, out);
    } else if (EXT.test(e.name)) {
      if (e.name === 'migrate-select2-rename.cjs') continue;
      if (/^select2.*\.min\.(js|css)$/.test(e.name)) continue;
      out.push(p);
    }
  }
  return out;
}

function migrate(content) {
  let s = content;

  const steps = [
    [/\.data\(\s*['"]select2-initialized['"]/g, `.data('mm-select-initialized'`],
    [/select2resultrepositorystatistics/g, 'mm-result-repository-stats'],
    [/select2-result-repository__statistics/g, 'mm-result-repository__statistics'],
    [/select2-result-repository__description/g, 'mm-result-repository__description'],
    [/select2-result-repository__title/g, 'mm-result-repository__title'],
    [/select2-result-repository/g, 'mm-result-repository'],
    [/select2-result-product/g, 'mm-result-product'],
    [/select2-checklist-create-dropdown/g, 'mm-checklist-create-dropdown'],
    [/select2-contact-person/g, 'mm-select-contact-person'],
    [/select2-multiple/g, 'mm-select-multiple'],
    [/assigneeselect2/g, 'assignee-mm-select'],
    [/assineeselect2/g, 'assignee-mm-select'],
    [/\$\([ \t]*['"]\.listing-container \.select2['"][ \t]*\)/g, `$('.listing-container .mm-select')`],
    [/\$\([ \t]*["']\.listing-container \.select2["'][ \t]*\)/g, `$(".listing-container .mm-select")`],
    [/\$\([ \t]*['"]\.select2['"][ \t]*\)/g, `$('.mm-select')`],
    [/\$\([ \t]*["']\.select2["'][ \t]*\)/g, `$(".mm-select")`],
    [/typeof\s+\$\s*\.\s*fn\s*\.\s*select2\b/g, 'typeof $.fn.mmSelect'],
    [/\.on\(\s*(['"])select2:/g, '.on($1mmselect:'],
    [/\$\(\s*document\s*\)\s*\.on\(\s*(['"])select2:/g, '$(document).on($1mmselect:'],
    [/select2:select/g, 'mmselect:select'],
    [/select2:clear/g, 'mmselect:clear'],
    [/select2:open/g, 'mmselect:open'],
    [/select2:close/g, 'mmselect:close'],
    [/\.data\(\s*['"]select2['"]/g, `.data('mmSelect'`],
    [/hasClass\(\s*['"]select2-hidden-accessible['"]\s*\)/g, `hasClass('mm-select-initialized')`],
    [/removeClass\(\s*['"]select2-hidden-accessible['"]\s*\)/g, `removeClass('mm-select-initialized')`],
    [/addClass\(\s*['"]select2-hidden-accessible['"]\s*\)/g, `addClass('mm-select-initialized')`],
    [/\.select2\(/g, '.mmSelect('],
    [/\bform-control\s+select2\b/g, 'form-control mm-select'],
    [/\bmultiple\s+class="form-control select2"/g, 'multiple class="form-control mm-select"'],
    [/\bclass="form-control select2"/g, 'class="form-control mm-select"'],
    [/\bselect2\s+selecttemplate\b/g, 'mm-select selecttemplate'],
    [/\bselect2\s+visa-dropdown\b/g, 'mm-select visa-dropdown'],
    [/\bselect2\s+selectmattertemplate\b/g, 'mm-select selectmattertemplate'],
    [/\bselect2\s+checklist-field\b/g, 'mm-select checklist-field'],
    [/\bdata-select2-id=/g, 'data-mm-select-id='],
  ];

  for (const [re, rep] of steps) {
    s = s.replace(re, rep);
  }

  s = s.replace(/class=(["'])([^"']*)\1/g, (m, q, inner) => {
    if (!/\bselect2\b/.test(inner)) return m;
    const nextInner = inner
      .split(/\s+/)
      .map((tok) => (tok === 'select2' ? 'mm-select' : tok))
      .join(' ')
      .replace(/mm-select mm-select/g, 'mm-select');
    return 'class=' + q + nextInner + q;
  });

  s = s.replace(/mm-select\s+mm-select/g, 'mm-select');
  return s;
}

let changed = 0;
for (const file of walk(ROOT)) {
  const raw = fs.readFileSync(file, 'utf8');
  const next = migrate(raw);
  if (next !== raw) {
    fs.writeFileSync(file, next, 'utf8');
    changed++;
    console.log('updated', path.relative(ROOT, file));
  }
}
console.log('files changed:', changed);
