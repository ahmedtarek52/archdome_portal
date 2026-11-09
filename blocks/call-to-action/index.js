(() => {
  'use strict';
  const { registerBlockType } = wp.blocks;
  const { InspectorControls, MediaUpload, MediaUploadCheck, RichText, useBlockProps } = wp.blockEditor;
  const { Button, PanelBody, SelectControl, TextControl, TextareaControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { __ } = wp.i18n;

  registerBlockType('archdome/call-to-action', {
    edit: ({ attributes, setAttributes }) => {
      const image = wp.data.useSelect(
        (select) => attributes.backgroundImageId ? select('core').getMedia(attributes.backgroundImageId) : null,
        [attributes.backgroundImageId]
      );
      const imageUrl = image?.media_details?.sizes?.large?.source_url || image?.source_url;
      return el(Fragment, {},
      el(InspectorControls, {}, el(PanelBody, { title: __('Action settings', 'archdome-portal') },
        el(SelectControl, { label: __('Tone', 'archdome-portal'), value: attributes.tone, options: [
          { label: __('Brand', 'archdome-portal'), value: 'brand' },
          { label: __('Light', 'archdome-portal'), value: 'light' }
        ], onChange: (value) => setAttributes({ tone: value }) }),
        el(MediaUploadCheck, {}, el(MediaUpload, {
          allowedTypes: ['image'],
          value: attributes.backgroundImageId || undefined,
          onSelect: (media) => setAttributes({ backgroundImageId: media.id }),
          render: ({ open }) => el(Button, { variant: 'secondary', onClick: open }, attributes.backgroundImageId ? __('Replace background image', 'archdome-portal') : __('Choose background image', 'archdome-portal'))
        })),
        attributes.backgroundImageId ? el(Button, {
          variant: 'link',
          isDestructive: true,
          onClick: () => setAttributes({ backgroundImageId: 0 })
        }, __('Remove background image', 'archdome-portal')) : null,
        el(TextControl, { label: __('Primary action label', 'archdome-portal'), value: attributes.primaryLabel, onChange: (value) => setAttributes({ primaryLabel: value }) }),
        el(TextControl, { label: __('Primary action URL', 'archdome-portal'), type: 'url', value: attributes.primaryUrl, onChange: (value) => setAttributes({ primaryUrl: value }) }),
        el(TextControl, { label: __('Secondary action label', 'archdome-portal'), value: attributes.secondaryLabel, onChange: (value) => setAttributes({ secondaryLabel: value }) }),
        el(TextControl, { label: __('Secondary action URL', 'archdome-portal'), type: 'url', value: attributes.secondaryUrl, onChange: (value) => setAttributes({ secondaryUrl: value }) })
      )),
      el('section', useBlockProps({ className: `archdome-cta-editor is-${attributes.tone}` + (imageUrl ? ' has-image' : '') }),
        imageUrl ? el('img', { className: 'archdome-cta-editor__background', src: imageUrl, alt: '' }) : null,
        imageUrl ? el('div', { className: 'archdome-cta-editor__overlay', 'aria-hidden': true }) : null,
        el(RichText, { tagName: 'h2', placeholder: __('Call-to-action heading', 'archdome-portal'), value: attributes.heading, allowedFormats: [], onChange: (value) => setAttributes({ heading: value }) }),
        el(TextareaControl, { label: __('Supporting text', 'archdome-portal'), value: attributes.text, onChange: (value) => setAttributes({ text: value }) })
      )
    ); },
    save: () => null
  });
})();
