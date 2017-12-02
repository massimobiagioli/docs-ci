<?php if (!empty($error_messages)): ?>
    <div class="row error-messages p-2 mt-2">
        <ul>
            <?php foreach ($error_messages as $error_message): ?>
                <li><?php echo $error_message; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>