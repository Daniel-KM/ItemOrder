<?php
$head = array('title' => 'Item Order', 'bodyclass' => 'primary');
echo head($head);
?>
<div>
    <?php echo flash();?>
</div>
<div id="primary">
    <h2>Order Items in Collection "<?php echo html_escape(metadata($collection, array('Dublin Core', 'Title'))); ?>"</h2>
    <p>Order can be done via a form or via drag-and-drop. <a href="<?php echo url('item-order?collection_id=' . $collection->id); ?>">Click here</a> to order via drag-and-drop.</p>
    <p><a href="<?php echo url('collections/show/' . $collection->id); ?>">Click here</a> to return to the collection show page.</p>
    <?php echo $form; ?>
</div>
<?php echo foot(); ?>
