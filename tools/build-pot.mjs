import fs from 'node:fs';
import path from 'node:path';

const themeRoot = path.resolve(import.meta.dirname, '..');
const excluded = new Set(['docs', 'languages', 'node_modules', 'vendor']);
const extensions = new Set(['.php', '.js']);
const entries = new Map();
const translationPattern = /\b(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*(['"])((?:\\.|(?!\1).)*)\1\s*,\s*['"]archdome-portal['"]/gs;

function visit(directory) {
  for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
    if (entry.isDirectory() && excluded.has(entry.name)) {
      continue;
    }

    const absolutePath = path.join(directory, entry.name);

    if (entry.isDirectory()) {
      visit(absolutePath);
      continue;
    }

    if (!extensions.has(path.extname(entry.name))) {
      continue;
    }

    const source = fs.readFileSync(absolutePath, 'utf8');
    const relativePath = path.relative(themeRoot, absolutePath).replaceAll('\\', '/');

    for (const match of source.matchAll(translationPattern)) {
      const message = match[2].replaceAll(`\\${match[1]}`, match[1]);
      const line = source.slice(0, match.index).split('\n').length;

      if (!entries.has(message)) {
        entries.set(message, []);
      }

      entries.get(message).push(`${relativePath}:${line}`);
    }
  }
}

function escapePot(value) {
  return value.replaceAll('\\', '\\\\').replaceAll('"', '\\"').replaceAll('\n', '\\n');
}

visit(themeRoot);

const header = `msgid ""\nmsgstr ""\n"Project-Id-Version: Archdome Portal 0.1.0\\n"\n"Report-Msgid-Bugs-To: \\n"\n"POT-Creation-Date: ${new Date().toISOString()}\\n"\n"MIME-Version: 1.0\\n"\n"Content-Type: text/plain; charset=UTF-8\\n"\n"Content-Transfer-Encoding: 8bit\\n"\n"X-Domain: archdome-portal\\n"\n`;
const body = [...entries.entries()]
  .sort(([first], [second]) => first.localeCompare(second))
  .map(([message, references]) => `\n#: ${references.join(' ')}\nmsgid "${escapePot(message)}"\nmsgstr ""\n`)
  .join('');

fs.writeFileSync(path.join(themeRoot, 'languages', 'archdome-portal.pot'), header + body, 'utf8');
console.log(`Extracted ${entries.size} strings to languages/archdome-portal.pot.`);
