<?php

namespace Tabs\Smarty\Plugins;

use Thelia\Core\Security\SecurityContext;
use Thelia\Core\Template\TemplateHelperInterface;
use Thelia\Tools\URL;
use TheliaSmarty\Template\AbstractSmartyPlugin;
use TheliaSmarty\Template\SmartyPluginDescriptor;

class TabsPlugin extends AbstractSmartyPlugin
{

    private $securityContext;
    private $templateHelper;

    public function __construct(SecurityContext $securityContext, TemplateHelperInterface $templateHelper)
    {
        $this->securityContext = $securityContext;
        $this->templateHelper = $templateHelper;
    }

    public function getPluginDescriptors()
    {
        return array(
            new SmartyPluginDescriptor('function', 'admin_position_tabs', $this, 'generatePositionChangeBlock'),
        );
    }

    protected function fetchSnippet($smarty, $templateName, $variablesArray)
    {
        $snippet_content = file_get_contents(
            $this->templateHelper->getActiveAdminTemplate()->getTemplateFilePath(
                $templateName . '.html'
            )
        );

        $smarty->assign($variablesArray);

        $data = $smarty->fetch(sprintf('string:%s', $snippet_content));

        return $data;
    }

    public function generatePositionChangeBlock($params, &$smarty)
    {
        // The required permissions
        $resource = $this->getParam($params, 'resource');
        $module = $this->getParam($params, 'module');
        $access = $this->getParam($params, 'access');
        $productId =  $this->getParam($params, 'productid');

        // The base position change path
        $path = $this->getParam($params, 'path');

        // The URL parameter the object ID is assigned
        $url_parameter = $this->getParam($params, 'url_parameter');

        // The current object position
        $position = $this->getParam($params, 'position');

        // The object ID
        $id = $this->getParam($params, 'id');

        // The in place dition class
        $in_place_edit_class = $this->getParam($params, 'in_place_edit_class');

        /*
         <a href="{url path='/admin/configuration/currencies/positionUp' currency_id=$ID}"><i class="icon-arrow-up"></i></a>
        <span class="currencyPositionChange" data-id="{$ID}">{$POSITION}</span>
        <a href="{url path='/admin/configuration/currencies/positionDown' currency_id=$ID}"><i class="icon-arrow-down"></i></a>
        */

        if ($this->securityContext->isGranted(
            array("ADMIN"),
            $resource === null ? array() : array($resource),
            $module === null ? array() : array($module),
            array($access)
        )
        ) {
            return $this->fetchSnippet($smarty, 'includes/admin-utilities-position-block', array(
                'admin_utilities_go_up_url'           => URL::getInstance()->absoluteUrl($path, array('mode' => 'up', $url_parameter => $id, "product_id" => $productId)),
                'admin_utilities_in_place_edit_class' => $in_place_edit_class,
                'admin_utilities_object_id'           => $id,
                'admin_utilities_current_position'    => $position,
                'admin_utilities_go_down_url'         => URL::getInstance()->absoluteUrl($path, array('mode' => 'down', $url_parameter => $id, "product_id" => $productId))
            ));
        } else {
            return $position;
        }
    }
}