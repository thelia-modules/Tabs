# Tabs

Attach custom content tabs — a title and a rich description — to products, contents,
categories and folders. Tabs are managed from the back office and rendered in the front
office through theme hooks or the API.

- **Version** 5.0.0
- **Requires** Thelia 3.0+ / PHP 8.3

## Installation

Drop the module in `local/modules/Tabs`, then activate it:

```bash
git submodule add https://github.com/thelia-modules/Tabs.git local/modules/Tabs
php Thelia module:refresh
php Thelia module:activate Tabs
```

Activation creates the `item_associated_tab` table (see [Migrating from 4.x](#migrating-from-4x)
if the module was already installed).

## Back office

A **Tabs association** CRUD panel is rendered in the *Modules* tab of the product, content,
category and folder edition pages. From there an administrator can:

Tabs are translated: the title and description are saved against the language currently
selected in the edition language switcher.

## Front office

### Theme hooks

`Tabs\Hook\Theme\TabsThemeHook` renders the tabs automatically on the four matching hooks:

| Hook | Item |
|------|------|
| `product.details.bottom` | product |
| `category.bottom` | category |
| `folder.bottom` | folder |
| `content.bottom` | content |

The markup lives in `templates/theme-hook/tabs.html.twig` (a `<details>` accordion with
`TabsModule-*` classes) and can be overridden by the theme.

Only visible tabs are rendered, ordered by position.

### `get_tabs()` Twig function

To render tabs yourself, call the `get_tabs()` function. It takes the same parameters as
the `/api/front/tabs` collection endpoint:

```twig
{% set tabs = get_tabs({
    itemType: 'product',
    itemId: product.id,
    'order[position]': 'asc',
}) %}

{% for tab in tabs %}
    <h2>{{ tab.i18ns.title }}</h2>
    <div>{{ tab.i18ns.description|raw }}</div>
{% endfor %}
```

`itemType` is one of `product`, `content`, `category`, `folder`.

## API

The `Tab` resource is exposed on two endpoints:

| Endpoint | Operations | Notes |
|----------|-----------|-------|
| `/api/admin/tabs` | `GET`, `POST`, `PATCH`, `DELETE` | full CRUD |
| `/api/front/tabs` | `GET` | read-only, invisible tabs filtered out |

Available filters: `id`, `itemType`, `itemId` (search), `visible` (boolean),
`order[id]`, `order[position]` (order).

The front endpoint is restricted to visible tabs by `Tabs\Api\Extension\VisibleTabQueryExtension`,
whatever the `visible` filter asks for.

## Data model

A single polymorphic table holds every tab:

```
item_associated_tab      id, item_type, item_id, position, visible, timestamps
item_associated_tab_i18n id, locale, title, description
```

## Migrating from 4.x

`Config/update/5.0.0.sql` **drops** the four former per-type tables
(`product_associated_tab`, `content_associated_tab`, `category_associated_tab`,
`folder_associated_tab` and their `_i18n` counterparts) and creates the polymorphic one.

This is deliberate: 4.x could not create a tab at all — `position` was `NOT NULL` with no
default and no code ever set it — so there is no data to carry over.

## License

GPL-3.0+ — see [LICENSE.txt](LICENSE.txt).
