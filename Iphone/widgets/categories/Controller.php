<?php

class Iphone_Widget_CategoriesController extends Engine_Content_Widget_Abstract
{
  public function indexAction()
  {
    // GET CATEGORIES
    $this->view->types = $types = Engine_Api::_()->getApi('core', 'iphone')->getTypes();
  }
  
}
