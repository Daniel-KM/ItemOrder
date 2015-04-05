<?php
class ItemOrder_IndexController extends Omeka_Controller_AbstractActionController
{
    public function init()
    {
        $this->_helper->db->setDefaultModelName('ItemOrder_ItemOrder');
    }

    public function indexAction()
    {
        $db = $this->_helper->db;

        // Set the collection.
        $collection = $db->getTable('Collection')->find($this->_getParam('collection_id'));

        // Refresh the collection items order and set the ordered items.
        $itemOrderTable = $db->getTable('ItemOrder_ItemOrder');
        $itemOrderTable->refreshItemOrder($this->_getParam('collection_id'));
        $items = $itemOrderTable->fetchOrderedItems($this->_getParam('collection_id'));

        $this->view->assign('collection', $collection);
        $this->view->assign('items', $items);
    }

    public function updateViaFormAction()
    {
        $form = $this->_getUpdateViaForm();
        $this->view->form = $form;

        // Set the collection to come back.
        $collection = $this->_helper->db->getTable('Collection')->find($this->_getParam('collection_id'));
        $this->view->collection = $collection;

        if (!$this->getRequest()->isPost()) {
            return;
        }

        if (!$form->isValid($this->getRequest()->getPost())) {
            $this->_helper->flashMessenger(__('Invalid form input. Please see errors below and try again.'), 'error');
            return;
        }

        $itemOrderList = $form->getValue('item_order_list');

        $this->_processUpdateViaForm($itemOrderList);
    }

    protected function _getUpdateViaForm()
    {
        require_once dirname(__FILE__) . '/../forms/UpdateViaForm.php';
        $form = new ItemOrder_Form_UpdateViaForm();
        return $form;
    }

    /**
     * Process ordering via form.
     *
     * @todo Use any identifier type (add a drop down to select it).
     */
    protected function _processUpdateViaForm($itemOrderList)
    {
        $view = get_view();
        $list = str_replace(array(',', ';', "\r", "\t"), "\n", $itemOrderList);
        $list = explode("\n", $list);
        // Transform list of identifier into a list of item ids.
        $ids = $view->getRecordsFromIdentifiers($list, false, 'Item', 'id', false);
        if ($ids) {
            // Need one item to get collection.
            $item = get_record_by_id('Item', reset($ids));
            $collection_id = $item->collection_id;
            $itemOrderTable = $this->_helper->db->getTable('ItemOrder_ItemOrder');
            $itemOrderTable->refreshItemOrder($collection_id);
            $itemOrderTable->updateOrder($collection_id, $ids);
            $this->_helper->flashMessenger(__('Collection has been reordered according to input.'), 'success');
        }
        else {
            $this->_helper->flashMessenger(__('No identifier has been recognized.'), 'error');
        }
    }

    /**
     * Order the items.
     */
    public function updateOrderAction()
    {
        // Allow only AJAX requests.
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->redirector->gotoUrl('/');
        }

        // Update the item orders.
        $this->_helper->db->getTable('ItemOrder_ItemOrder')->updateOrder($this->_getParam('collection_id'), $this->_getParam('items'));
        $this->_helper->json(true);
    }

    /**
     * Reset the order.
     */
    public function resetOrderAction()
    {
        $this->_helper->db->getTable('ItemOrder_ItemOrder')->resetOrder($this->_getParam('collection_id'));
        $this->_helper->flashMessenger('The items have been reset to their default order.', 'success');
        $this->_helper->redirector->gotoUrl('/collections/show/' . $this->_getParam('collection_id'));
    }
}
