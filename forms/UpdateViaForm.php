<?php
/**
 * Item Order Update Via Form
 */

class ItemOrder_Form_UpdateViaForm extends Omeka_Form
{
    /**
     * Initialize the form.
     */
    public function init()
    {
        parent::init();

        $this->setAttrib('id', 'item-order');
        $this->setMethod('post');

        $this->addElement('textarea', 'item_order_list', array(
            'label' => __('Ordered list of record identifiers'),
            'description' => __('List of all record identifiers of a collection, one by line or separated with a ",".'),
            'id' => 'item-order-update',
            'required' => true,
        ));

        $this->addElement('submit', 'submit', array('label' => __('Submit')));
    }
}
