<?php if (!empty($error_messages)): ?>
    <div class="row error-messages p-2 mt-2">
        <?php foreach ($error_messages as $error_message): ?>
            <div><?php echo $error_message; ?></div>
        <?php endforeach; ?>
    </div>    
<?php endif; ?>