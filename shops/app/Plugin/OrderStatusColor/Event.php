<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\OrderStatusColor;

use Eccube\Event\TemplateEvent;

class Event
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function onRenderOrderList(TemplateEvent $event)
    {
        $parameters = $event->getParameters();
        $pagination = $parameters['pagination'];

        if (!$pagination || count($pagination) === 0) {
            return;
        }

        $snippet = ' class="order_status_color_{{ Order.OrderStatus.id }}"';
        $search = '<tr id="result_list_main__item--{{ Order.id }}"';
        $replace = $search.$snippet;
        $source = str_replace($search, $replace, $event->getSource());

        $snippet2 = '
            <style>
            {% for OrderStatusColor in OrderStatusColors %}
            .order_status_color_{{ OrderStatusColor.id }} td { background-color: {{OrderStatusColor.name }} !important;}
            {% endfor %}
            </style>
        ';
        $search2 = '{% endblock stylesheet %}';
        $replace2 = $snippet2.$search2;
        $source = str_replace($search2, $replace2, $source);

        $event->setSource($source);

        $parameters['OrderStatusColors'] = $this->app['orm.em']->getRepository('Eccube\Entity\Master\OrderStatusColor')->findAll();
        $event->setParameters($parameters);
    }
}
