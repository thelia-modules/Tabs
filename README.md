# Module Tabs Thelia 2

This module allows you to create content tabs for contents and products.

## How to install

This module must be into your ```modules/``` directory (thelia/local/modules/).

You can download the .zip file of this module or create a git submodule into your project like this :

```
cd /path-to-thelia
git submodule add https://github.com/thelia-modules/Tabs.git local/modules/Tabs
```

Next, go to your Thelia admin panel for module activation.

## How to use

You can manage your tabs into the "Modules" tab of content and product editing view.

This module allow you to use a new loop : tabs.

Here is an example of using :

__Use the tabs loop (list of tabs related to a content id)__
```html
{loop name="tabs" type="tabs" content="$ID" order="manual_reverse"}
    <article>
        <h1>{$TITLE}</h1>
        <div class="description">{$DESCRIPTION nofilter}</div>
    </article>
{/loop}
```

__Use the tabs loop (list of tabs related to a product id)__
```html
{loop name="tabs" type="tabs" product="$ID" order="manual_reverse"}
    <article>
        <h1>{$TITLE}</h1>
        <div class="description">{$DESCRIPTION nofilter}</div>
    </article>
{/loop}
```