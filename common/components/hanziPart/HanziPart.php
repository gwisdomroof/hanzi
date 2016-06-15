<?php

namespace common\components\hanziPart;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use common\components\hanziPart\Components;

class HanziPart extends Widget
{
    public $components = [];
    
    public function init()
    {
        $this->components = Components::items();
        parent::init();      
    }
    /**
     * @inheritdoc
     */
    public function run()
    {
        if (empty($this->components)) {
            return null;
        }
        echo $this->renderTemplate();
        $this->registerClientScript();
    }
    /**
     * @return string the items that are need to be rendered.
     */
    public function renderItems()
    {
        $items = [];
        foreach ($this->components as $stock_num => $stock_array) {
            $items[] = "<span class='stock-item'>$stock_num</span>";
            $i = 1;
            foreach ($stock_array as $key => $value) {
                if (mb_strlen($key,'utf-8') == 1) {
                    $items[] = "<span class='component-item' value='" . $value . "'>" . $key . "</span>";
                } else {
                    $path =  '/img/components/' . $key . '.png';
                    $items[] = "<span><img class='component-img' src='$path' alt='$value'></span>";
                } 
            }
        }
        return implode("\n", $items);
    }

    /**
     * Renders the template to display
     * @return string the template
     */
    public function renderTemplate()
    {
        $template = [];
        $template[] = '<span title="显示" class="component-show glyphicon glyphicon-align-justify pull-right" id="component-show"></span>';
        $template[] = '<div class="pull-right hanzi-component" id="hanzi-component">';
        $template[] = '<span title="隐藏" class="glyphicon glyphicon-align-justify pull-right component-hide" id="component-hide"></span>';
        $template[] = '<div class="component-search">';
        $template[] = '<div class="input-group add-on">';
        $template[] = '<input class="form-control" placeholder="请输入笔画、笔顺检索部件..." name="srch-term" id="search" type="text" oninput="FindMatch()">';
        $template[] = '<div class="input-group-btn">';
        $template[] = '<a class="btn btn-default search-help" title="帮助" href="/article/component-help" target="blank">?</a>';
        $template[] = '</div>';
        $template[] = '</div>';
        $template[] = '<div id="msg" style="color:#cc0000"></div>';
        $template[] = '</div>';
        $template[] = ' <div id="output">';
        $template[] = $this->renderItems();
        $template[] = '</div>';
        $template[] = '</div>';

        return implode("\n", $template);
    }
    /**
     * Registers the client script required for the plugin
     */
    public function registerClientScript()
    {
        $view = $this->getView();
        HanziPartAsset::register($view);
    }
}