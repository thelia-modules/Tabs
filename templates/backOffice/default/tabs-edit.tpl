<div class="tab-module">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">{intl l="[Tabs]Tabs"}</h3>
        </div>

        <div class="panel-body">

            <div class="form-container">
                {form name=$tabsFormName}

                    {* Display error message if exist *}
                {if $form_error}
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        {intl l="[Tabs]One of the tabs that you have changed or created contains one or more errors. The <strong>Title</strong> and <strong>Description</strong> fields are mandatory"}
                    </div>
                {/if}

                    <form action="{$formActionUrl}" method="POST" role="form">
                        <legend>{intl l="[Tabs]Tabs association"}</legend>

                        {include file="includes/inner-form-toolbar.html" hide_submit_buttons=true}

                        {form_hidden_fields form=$form}

                        {form_field form=$form field='locale'}
                            <input type="hidden" name="{$name}" value="{$edit_language_locale}" />
                        {/form_field}

                        {form_field form=$form field='success_url'}
                            <input type="hidden" name="{$name}" value="{$formSuccessUrl}" />
                        {/form_field}

                        <input type="hidden" name="current_tab" value="modules" />

                        {block name="parent-context"}
                            Put here the parent context input fields
                        {/block}

                        {form_field form=$form field='title'}
                            <div class="form-group">
                                <label for="{$label_attr.for}" class="control-label">{$label} : </label>
                                <input type="text" id="{$label_attr.for}" required="required" name="{$name}" class="form-control" value="{$value}" title="{$label}" placeholder="{intl l='title'}">
                            </div>
                        {/form_field}

                        {form_field form=$form field='description'}
                            <div class="form-group">
                                <label for="{$label_attr.for}" class="control-label">{$label} : </label>
                                <textarea id="{$label_attr.for}" name="{$name}" row="10" class="wysiwyg form-control">{$value}</textarea>
                            </div>
                        {/form_field}

                        <div class="row">
                            <div class="col-md-6">
                                {form_field form=$form field='visible'}
                                    <div class="form-group">
                                        <label for="{$label_attr.for}" class="control-label">{intl l='Visibility'}</label>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" id="{$label_attr.for}" name="{$name}" value="1" checked="checked">
                                                {$label}
                                            </label>
                                        </div>
                                    </div>
                                {/form_field}
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="control-group">
                                    <lablel>&nbsp;</lablel>
                                    <div class="controls">
                                        <p>{intl l='[Tabs]Tab created on %date_create. Last modification: %date_change' date_create="{format_date date=$CREATE_DATE}" date_change="{format_date date=$UPDATE_DATE}"}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">{intl l="Save"}</button>
                    </form>
                {/form}

                {ifloop rel="tabs"}
                    <br/>
                    <div class="panel-group" id="accordion">
                        {loop name="tabs" type="tabs" source=$tabSource source_id=$tabSourceId lang={$edit_language_id} order="manual"}
                            <div class="panel panel-default">
                                <div class="panel-heading clearfix">
                                    <h3 class="panel-title pull-left">
                                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse-{$ID}">
                                            {$TITLE}
                                        </a>
                                    </h3>
                                    {block name="parent-context-position-tab"}
                                        Put here the parent context input fields
                                    {/block}
                                    {loop type="auth" name="can_delete" role="ADMIN" resource="admin.tabs" access="DELETE"}
                                        <a class="btn btn-danger btn-xs tab-delete pull-right" title="{intl l='[Tabs]Delete this tab'}"  href="#tabs_delete_dialog" data-id="{$ID}" data-toggle="modal"><span class="glyphicon glyphicon-trash"></span></a>
                                    {/loop}
                                </div>

                                <div id="collapse-{$ID}" class="panel-collapse collapse">
                                    <div class="panel-body">

                                        <div class="form-container">
                                            {form name=$tabsFormName}
                                                <form action="{$formActionUrl}" method="POST" role="form">
                                                    <legend>{$TITLE}</legend>

                                                    {form_hidden_fields form=$form}

                                                    {form_field form=$form field='locale'}
                                                        <input type="hidden" name="{$name}" value="{$edit_language_locale}" />
                                                    {/form_field}

                                                    {form_field form=$form field='success_url'}
                                                        <input type="hidden" name="{$name}" value={$formSuccessUrl} />
                                                    {/form_field}

                                                    <input type="hidden" name="current_tab" value="modules" />

                                                    <input name="tab_id" type="hidden" value="{$ID}"/>
                                                    {block name="parent-context-loop"}
                                                        Put here the parent context input fields
                                                    {/block}

                                                    {form_field form=$form field='title'}
                                                        <div class="form-group">
                                                            <label for="{$label_attr.for}-{$ID}" class="control-label">{$label} : </label>
                                                            <input type="text" id="{$label_attr.for}-{$ID}" required="required" name="{$name}" class="form-control" value="{$TITLE}" title="{$label}" placeholder="{intl l='title'}">
                                                        </div>
                                                    {/form_field}

                                                    {form_field form=$form field='description'}
                                                        <div class="form-group">
                                                            <label for="{$label_attr.for}-{$ID}" class="control-label">{$label} : </label>
                                                            <textarea id="{$label_attr.for}-{$ID}" name="{$name}" row="10" class="wysiwyg form-control">{$DESCRIPTION}</textarea>
                                                        </div>
                                                    {/form_field}

                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            {form_field form=$form field='visible'}
                                                                <div class="form-group">
                                                                    <label for="{$label_attr.for}-{$ID}" class="control-label">{intl l='Visibility'}</label>
                                                                    <div class="checkbox">
                                                                        <label>
                                                                            <input type="checkbox" id="{$label_attr.for}-{$ID}" name="{$name}" value="1" {if $VISIBLE != 0}checked="checked"{/if}>
                                                                            {$label}
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            {/form_field}
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="control-group">
                                                                <lablel>&nbsp;</lablel>
                                                                <div class="controls">
                                                                    <p>{intl l='[Tabs]Tab created on %date_create. Last modification: %date_change' date_create="{format_date date=$CREATE_DATE}" date_change="{format_date date=$UPDATE_DATE}"}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <button type="submit" class="btn btn-primary">{intl l="Save"}</button>
                                                </form>
                                            {/form}
                                        </div>

                                    </div>
                                </div>
                            </div>
                        {/loop}
                    </div>
                {/ifloop}

            </div>

        </div>

    </div>

</div>

{* -- Delete keyword confirmation dialog ----------------------------------- *}

{capture "tabs_delete_dialog"}
    <input type="hidden" name="tab_id" id="tabs_delete_id" value="" />
{/capture}

{include
file = "includes/generic-confirm-dialog.html"

dialog_id       = "tabs_delete_dialog"
dialog_title    = {intl l="[Tabs]Delete tab"}
dialog_message  = {intl l="[Tabs]Do you really want to delete this tab ?"}

form_action         = {token_url path="/admin/modules/tabs/delete"}
form_content        = {$smarty.capture.tabs_delete_dialog nofilter}
}
