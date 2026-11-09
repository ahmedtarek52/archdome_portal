(() => {
  'use strict';

  const { registerBlockType } = wp.blocks;
  const { InspectorControls, MediaUpload, MediaUploadCheck, RichText, useBlockProps } = wp.blockEditor;
  const { Button, PanelBody, SelectControl, TextControl, TextareaControl } = wp.components;
  const { createElement: el, Fragment } = wp.element;
  const { __ } = wp.i18n;

  registerBlockType('archdome/hero', {
    edit: ({ attributes, setAttributes }) => {
      const blockProps = useBlockProps({ className: 'archdome-hero-editor' });
      const headingTag = attributes.headingLevel === 2 ? 'h2' : 'h1';

      return el(Fragment, {},
        el(InspectorControls, {},
          el(PanelBody, { title: __('Hero settings', 'archdome-portal'), initialOpen: true },
            el(SelectControl, {
              label: __('Heading level', 'archdome-portal'),
              value: attributes.headingLevel,
              options: [
                { label: __('Heading 1', 'archdome-portal'), value: 1 },
                { label: __('Heading 2', 'archdome-portal'), value: 2 }
              ],
              onChange: (value) => setAttributes({ headingLevel: Number(value) })
            }),
            el(TextControl, { label: __('Primary action label', 'archdome-portal'), value: attributes.primaryLabel, onChange: (value) => setAttributes({ primaryLabel: value }) }),
            el(TextControl, { label: __('Primary action URL', 'archdome-portal'), type: 'url', value: attributes.primaryUrl, onChange: (value) => setAttributes({ primaryUrl: value }) }),
            el(TextControl, { label: __('Secondary action label', 'archdome-portal'), value: attributes.secondaryLabel, onChange: (value) => setAttributes({ secondaryLabel: value }) }),
            el(TextControl, { label: __('Secondary action URL', 'archdome-portal'), type: 'url', value: attributes.secondaryUrl, onChange: (value) => setAttributes({ secondaryUrl: value }) })
          )
        ),
        el('section', blockProps,
          el('div', { className: 'archdome-hero-editor__content' },
            el(RichText, { tagName: 'p', className: 'archdome-hero__eyebrow', placeholder: __('Optional eyebrow', 'archdome-portal'), value: attributes.eyebrow, allowedFormats: [], onChange: (value) => setAttributes({ eyebrow: value }) }),
            el(RichText, { tagName: headingTag, placeholder: __('Hero heading', 'archdome-portal'), value: attributes.heading, allowedFormats: [], onChange: (value) => setAttributes({ heading: value }) }),
            el(TextareaControl, { label: __('Supporting text', 'archdome-portal'), value: attributes.text, onChange: (value) => setAttributes({ text: value }) }),
            el(MediaUploadCheck, {},
              el(MediaUpload, {
                allowedTypes: ['image'],
                value: attributes.mediaId,
                onSelect: (media) => setAttributes({ mediaId: media.id }),
                render: ({ open }) => el(Button, { variant: 'secondary', onClick: open }, attributes.mediaId ? __('Replace image', 'archdome-portal') : __('Choose image', 'archdome-portal'))
              })
            ),
            attributes.mediaId ? el(Button, { variant: 'link', isDestructive: true, onClick: () => setAttributes({ mediaId: 0 }) }, __('Remove image', 'archdome-portal')) : null
          )
        )
      );
    },
    save: () => null
  });
})();
