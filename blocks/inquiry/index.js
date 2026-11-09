(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { RichText, useBlockProps } = wp.blockEditor;
  const { TextControl, TextareaControl } = wp.components;
  const { createElement: el } = wp.element;
  const { __ } = wp.i18n;
  registerBlockType('archdome/inquiry', { edit: ({ attributes, setAttributes }) => el('section', useBlockProps({ className: 'archdome-inquiry-editor' }),
    el(RichText, { tagName: 'h2', placeholder: __('Inquiry heading', 'archdome-portal'), value: attributes.heading, allowedFormats: [], onChange: (value) => setAttributes({ heading: value }) }),
    el(TextareaControl, { label: __('Supporting text', 'archdome-portal'), value: attributes.text, onChange: (value) => setAttributes({ text: value }) }),
    el(TextControl, { label: __('Button label', 'archdome-portal'), value: attributes.buttonLabel, onChange: (value) => setAttributes({ buttonLabel: value }) })
  ), save: () => null });
})();
