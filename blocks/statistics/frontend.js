(() => {
  'use strict';

  const digitPattern = /[0-9\u0660-\u0669\u06f0-\u06f9]/;
  const numeralPattern = /^([0-9\u0660-\u0669\u06f0-\u06f9]+(?:[,\u066c][0-9\u0660-\u0669]{3})*(?:[.\u066b][0-9\u0660-\u0669]+)?)(.*)$/u;
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const activeFrames = new Map();

  const toAscii = (value) => value.replace(/[\u0660-\u0669\u06f0-\u06f9]/g, (digit) => {
    const code = digit.charCodeAt(0);
    return String(code >= 0x6f0 ? code - 0x6f0 : code - 0x660);
  });

  const readCounter = (element) => {
    const original = element.textContent.trim();
    const match = original.match(numeralPattern);
    if (!match || digitPattern.test(match[2])) return null;

    const numeral = match[1];
    const decimalMark = numeral.includes('\u066b') ? '\u066b' : numeral.includes('.') ? '.' : '';
    const groupMark = numeral.includes('\u066c') ? '\u066c' : numeral.includes(',') ? ',' : '';
    const ascii = toAscii(numeral).replace(/[,\u066c]/g, '').replace('\u066b', '.');
    const target = Number(ascii);
    if (!Number.isFinite(target) || target <= 0 || target > 1000000000) return null;

    const decimals = decimalMark ? numeral.split(decimalMark)[1].length : 0;
    if (decimals > 2) return null;

    const firstDigit = numeral.charCodeAt(0);
    const digitBase = firstDigit >= 0x6f0 && firstDigit <= 0x6f9 ? 0x6f0 :
      firstDigit >= 0x660 && firstDigit <= 0x669 ? 0x660 : 0x30;

    return { element, original, target, decimals, decimalMark, groupMark, digitBase, suffix: match[2] };
  };

  const formatCounter = (counter, value) => {
    const parts = value.toFixed(counter.decimals).split('.');
    let integer = parts[0];
    if (counter.groupMark) integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, counter.groupMark);
    let display = integer + (parts[1] ? counter.decimalMark + parts[1] : '') + counter.suffix;
    if (counter.digitBase !== 0x30) {
      display = display.replace(/[0-9]/g, (digit) => String.fromCharCode(counter.digitBase + Number(digit)));
    }
    return display;
  };

  const startCounter = (counter) => {
    if (reducedMotion.matches) return;

    counter.element.setAttribute('aria-label', counter.original);
    const duration = 1200;
    let startedAt;

    const step = (now) => {
      if (startedAt === undefined) startedAt = now;
      const progress = Math.min((now - startedAt) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      counter.element.textContent = formatCounter(counter, counter.target * eased);

      if (progress < 1) {
        activeFrames.set(counter.element, window.requestAnimationFrame(step));
      } else {
        counter.element.textContent = counter.original;
        activeFrames.delete(counter.element);
      }
    };

    activeFrames.set(counter.element, window.requestAnimationFrame(step));
  };

  const init = () => {
    const sections = Array.from(document.querySelectorAll('.wp-block-archdome-statistics.archdome-statistics'));
    if (!sections.length || reducedMotion.matches) return;

    const counters = new Map(sections.map((section) => [
      section,
      Array.from(section.querySelectorAll('.archdome-statistics__item dd')).map(readCounter).filter(Boolean)
    ]));

    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          counters.get(entry.target).forEach(startCounter);
          observer.unobserve(entry.target);
        });
      }, { threshold: 0.1 });
      sections.forEach((section) => observer.observe(section));
    } else {
      counters.forEach((items) => items.forEach(startCounter));
    }

    reducedMotion.addEventListener('change', () => {
      if (!reducedMotion.matches) return;
      activeFrames.forEach((frame, element) => {
        window.cancelAnimationFrame(frame);
        const counter = Array.from(counters.values()).flat().find((item) => item.element === element);
        if (counter) element.textContent = counter.original;
      });
      activeFrames.clear();
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }
})();
