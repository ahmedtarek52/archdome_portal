<?php

/**
 * Registered metadata and validation helpers.
 */
if (! defined('ABSPATH')) {
    exit;
}

/**
 * Authorize metadata changes against the owning post.
 *
 * @param  bool  $allowed  Existing authorization result.
 * @param  string  $meta_key  Metadata key.
 * @param  int  $object_id  Post ID.
 */
function archdome_authorize_post_meta(bool $allowed, string $meta_key, int $object_id): bool
{
    unset($allowed, $meta_key);

    return current_user_can('edit_post', $object_id);
}

/**
 * Normalize a decimal value without converting it to a float.
 *
 * @param  mixed  $value  Input value.
 * @param  float|null  $min  Optional minimum.
 * @param  float|null  $max  Optional maximum.
 */
function archdome_sanitize_decimal(mixed $value, ?float $min = null, ?float $max = null): string
{
    $normalized = str_replace(',', '.', trim((string) $value));

    if ($normalized === '' || ! is_numeric($normalized)) {
        return '';
    }

    $number = (float) $normalized;

    if (($min !== null && $number < $min) || ($max !== null && $number > $max)) {
        return '';
    }

    return rtrim(rtrim(number_format($number, 6, '.', ''), '0'), '.');
}

/**
 * Sanitize a latitude.
 *
 * @param  mixed  $value  Input value.
 */
function archdome_sanitize_latitude(mixed $value): string
{
    return archdome_sanitize_decimal($value, -90, 90);
}

/**
 * Sanitize a longitude.
 *
 * @param  mixed  $value  Input value.
 */
function archdome_sanitize_longitude(mixed $value): string
{
    return archdome_sanitize_decimal($value, -180, 180);
}

/**
 * Sanitize a non-negative decimal.
 *
 * @param  mixed  $value  Input value.
 */
function archdome_sanitize_non_negative_decimal(mixed $value): string
{
    return archdome_sanitize_decimal($value, 0);
}

/**
 * Sanitize and bound an attachment ID list.
 *
 * @param  mixed  $value  Input array or comma-delimited string.
 * @return int[]
 */
function archdome_sanitize_id_list(mixed $value): array
{
    $items = is_array($value) ? $value : explode(',', (string) $value);
    $ids = array_values(
        array_unique(
            array_filter(
                array_map('absint', $items),
                static fn(int $id): bool => get_post_type($id) === 'attachment',
            ),
        ),
    );

    return array_slice($ids, 0, 40);
}

/**
 * Sanitize a media attachment ID.
 *
 * @param  mixed  $value  Input value.
 */
function archdome_sanitize_attachment_id(mixed $value): int
{
    $attachment_id = absint($value);

    return get_post_type($attachment_id) === 'attachment' ? $attachment_id : 0;
}

/**
 * Sanitize a Development relationship ID.
 *
 * @param  mixed  $value  Input value.
 */
function archdome_sanitize_development_id(mixed $value): int
{
    $development_id = absint($value);

    return get_post_type($development_id) === 'development' ? $development_id : 0;
}

/**
 * Sanitize bounded key-stat rows.
 *
 * @param  mixed  $value  Input rows.
 * @return array<int, array{label: string, value: string}>
 */
function archdome_sanitize_key_stats(mixed $value): array
{
    if (! is_array($value)) {
        return [];
    }

    $rows = [];

    foreach (array_slice($value, 0, 12) as $row) {
        if (! is_array($row)) {
            continue;
        }

        $label = sanitize_text_field($row['label'] ?? '');
        $item = sanitize_text_field($row['value'] ?? '');

        if ($label === '' && $item === '') {
            continue;
        }

        $rows[] = [
            'label' => $label,
            'value' => $item,
        ];
    }

    return $rows;
}

/**
 * Sanitize an ISO-style currency code.
 *
 * @param  mixed  $value  Input value.
 */
function archdome_sanitize_currency(mixed $value): string
{
    $currency = strtoupper(sanitize_text_field((string) $value));

    return preg_match('/^[A-Z]{3}$/', $currency) ? $currency : '';
}

/**
 * Sanitize the supported area units.
 *
 * @param  mixed  $value  Input value.
 */
function archdome_sanitize_area_unit(mixed $value): string
{
    $unit = sanitize_key((string) $value);

    return in_array($unit, ['sqm', 'sqft'], true) ? $unit : 'sqm';
}

/**
 * Register public metadata for real-estate records.
 */
function archdome_register_post_meta(): void
{
    $common = [
        'single' => true,
        'auth_callback' => 'archdome_authorize_post_meta',
        'revisions_enabled' => true,
    ];

    $id_list_schema = [
        'schema' => [
            'type' => 'array',
            'items' => ['type' => 'integer'],
        ],
    ];

    register_post_meta(
        'development',
        '_archdome_gallery_ids',
        array_merge(
            $common,
            [
                'type' => 'array',
                'default' => [],
                'show_in_rest' => $id_list_schema,
                'sanitize_callback' => 'archdome_sanitize_id_list',
            ],
        ),
    );

    register_post_meta(
        'development',
        '_archdome_key_stats',
        array_merge(
            $common,
            [
                'type' => 'array',
                'default' => [],
                'show_in_rest' => [
                    'schema' => [
                        'type' => 'array',
                        'items' => [
                            'type' => 'object',
                            'properties' => [
                                'label' => ['type' => 'string'],
                                'value' => ['type' => 'string'],
                            ],
                        ],
                    ],
                ],
                'sanitize_callback' => 'archdome_sanitize_key_stats',
            ],
        ),
    );

    $development_meta = [
        '_archdome_master_plan_id' => ['integer', 'archdome_sanitize_attachment_id', 0],
        '_archdome_brochure_id' => ['integer', 'archdome_sanitize_attachment_id', 0],
        '_archdome_latitude' => ['string', 'archdome_sanitize_latitude', ''],
        '_archdome_longitude' => ['string', 'archdome_sanitize_longitude', ''],
        '_archdome_featured' => ['boolean', 'rest_sanitize_boolean', false],
        '_archdome_inquiry_label' => ['string', 'sanitize_text_field', ''],
    ];

    foreach ($development_meta as $key => [$type, $sanitize, $default]) {
        register_post_meta(
            'development',
            $key,
            array_merge(
                $common,
                [
                    'type' => $type,
                    'default' => $default,
                    'show_in_rest' => true,
                    'sanitize_callback' => $sanitize,
                ],
            ),
        );
    }

    register_post_meta(
        'property',
        '_archdome_gallery_ids',
        array_merge(
            $common,
            [
                'type' => 'array',
                'default' => [],
                'show_in_rest' => $id_list_schema,
                'sanitize_callback' => 'archdome_sanitize_id_list',
            ],
        ),
    );

    $property_meta = [
        '_archdome_development_id' => ['integer', 'archdome_sanitize_development_id', 0],
        '_archdome_price' => ['string', 'archdome_sanitize_non_negative_decimal', ''],
        '_archdome_price_on_request' => ['boolean', 'rest_sanitize_boolean', false],
        '_archdome_currency' => ['string', 'archdome_sanitize_currency', ''],
        '_archdome_bedrooms' => ['integer', 'absint', 0],
        '_archdome_bathrooms' => ['string', 'archdome_sanitize_non_negative_decimal', ''],
        '_archdome_area' => ['string', 'archdome_sanitize_non_negative_decimal', ''],
        '_archdome_area_unit' => ['string', 'archdome_sanitize_area_unit', 'sqm'],
        '_archdome_floor_plan_id' => ['integer', 'archdome_sanitize_attachment_id', 0],
        '_archdome_featured' => ['boolean', 'rest_sanitize_boolean', false],
        '_archdome_reference' => ['string', 'sanitize_text_field', ''],
    ];

    foreach ($property_meta as $key => [$type, $sanitize, $default]) {
        register_post_meta(
            'property',
            $key,
            array_merge(
                $common,
                [
                    'type' => $type,
                    'default' => $default,
                    'show_in_rest' => true,
                    'sanitize_callback' => $sanitize,
                ],
            ),
        );
    }
}
add_action('init', 'archdome_register_post_meta', 7);
