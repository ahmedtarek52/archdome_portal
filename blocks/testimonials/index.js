(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { RichText, useBlockProps } = wp.blockEditor;
  const { Button, TextControl, TextareaControl } = wp.components;
  const { createElement: el } = wp.element;
  const { __ } = wp.i18n;
  registerBlockType('archdome/testimonials', { edit: ({ attributes, setAttributes }) => {
    const items = Array.isArray(attributes.items) ? attributes.items : [];
    const update = (index, key, value) => setAttributes({ items: items.map((item, current) => current === index ? { ...item, [key]: value } : item) });
    return el('section', useBlockProps({ className: 'archdome-testimonials-editor' }),
      el(RichText, { tagName: 'h2', placeholder: __('Testimonials heading', 'archdome-portal'), value: attributes.heading, allowedFormats: [], onChange: (value) => setAttributes({ heading: value }) }),
      items.map((item, index) => el('div', { className: 'archdome-testimonials-editor__item', key: index },
        el(TextareaControl, { label: __('Quotation', 'archdome-portal'), value: item.quote || '', onChange: (value) => update(index, 'quote', value) }),
        el(TextControl, { label: __('Name', 'archdome-portal'), value: item.name || '', onChange: (value) => update(index, 'name', value) }),
        el(TextControl, { label: __('Role or context', 'archdome-portal'), value: item.role || '', onChange: (value) => update(index, 'role', value) }),
        el(Button, { variant: 'link', isDestructive: true, onClick: () => setAttributes({ items: items.filter((unused, current) => current !== index) }) }, __('Remove testimonial', 'archdome-portal'))
      )),
      items.length < 6 ? el(Button, { variant: 'secondary', onClick: () => setAttributes({ items: [...items, { quote: '', name: '', role: '' }] }) }, __('Add testimonial', 'archdome-portal')) : null
    );
  }, save: () => null });
})();
