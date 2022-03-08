# Module Tabs Thelia 2

This module allows you to create content tabs for contents, products, categories & folders

## How to install

This module must be into your ```modules/``` directory (thelia/local/modules/).

You can download the .zip file of this module or create a git submodule into your project like this :

```
cd /path-to-thelia
git submodule add https://github.com/thelia-modules/Tabs.git local/modules/Tabs
```

Next, go to your Thelia admin panel for module activation.

## The loop tags

### Parameters

|Argument |Description |
|---      |--- |
|**id** | Return tag with this ID |
|**source** | Source of associated objects. The possibles values are `product`, `category`, `content`, `folder`. Giving a value missing from this list will not cause an error. |
|**source_id** | ID of associated objects |
|**visible**           | Whether your selection will be visible or not. Default : true |
|**position**          | The position of the selection you wish to display |


## How to use

You can manage your tabs into the "Modules" tab of content and product editing view.

This module allow you to use a new loop : tabs.

Here is an example of using :

__Use the tabs loop (list of tabs related to a content id)__
```html
{loop name="tabs" type="tabs" source="content" source_id=$ID order="manual_reverse"}
    <article>
        <h1>{$TITLE}</h1>
        <div class="description">{$DESCRIPTION nofilter}</div>
    </article>
{/loop}
```

__Use the tabs loop (list of tabs related to a product id)__
```html
{loop name="tabs" type="tabs" source="product" source_id=$ID  order="manual_reverse"}
    <article>
        <h1>{$TITLE}</h1>
        <div class="description">{$DESCRIPTION nofilter}</div>
    </article>
{/loop}
```
__Use the tabs loop (list of tabs related to a category id)__
```html
{loop name="tabs" type="tabs" source="category" source_id=$ID  order="manual_reverse"}
    <article>
        <h1>{$TITLE}</h1>
        <div class="description">{$DESCRIPTION nofilter}</div>
    </article>
{/loop}
```
__Use the tabs loop (list of tabs related to a folder id)__
```html
{loop name="tabs" type="tabs" source="folder" source_id=$ID  order="manual_reverse"}
    <article>
        <h1>{$TITLE}</h1>
        <div class="description">{$DESCRIPTION nofilter}</div>
    </article>
{/loop}
```