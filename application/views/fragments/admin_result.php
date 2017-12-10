<?php if (!empty($result)): ?>
    <div class="row result p-2 mt-2">
        <?php foreach ($result as $k => $v): ?>
            <div><?php echo "$k = $v"; ?></div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>