<div class="container">
    <div class="row">
        
        <!-- INFO -->
        <div class="document-detail-info col-12">
            <h4><?php echo $this->lang->line('info'); ?></h4>
            <hr>
            
            <!-- ID -->
            <div class="container">
                <div class="row">
                    <div class="document-detail-info-key col-4">
                        ID
                    </div>
                    <div class="document-detail-info-value col-8">
                        <?php echo $document['id']; ?>
                    </div>
                </div>
            </div> <!-- .container -->
            
            <!-- document_info -->
            <div class="container">
                <div class="row">
                    <?php foreach ($document['document_info'] as $key => $value): ?>
                        <div class="document-detail-info-key col-4">
                            <?php echo $key; ?>
                        </div>
                        <div class="document-detail-info-value col-8">
                            <?php echo $value; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div> <!-- .container -->
        </div> <!-- .document-detail-info -->

        <!-- METADATA -->
        <div class="document-detail-info col-12 mt-4">
            <h4><?php echo $this->lang->line('metadata'); ?></h4>
            <hr>
            
            <!-- document_metadata -->
            <div class="container">
                <div class="row">
                    <?php foreach ($document['document_metadata'] as $key => $value): ?>
                        <div class="document-detail-info-key col-4">
                            <?php echo $key; ?>
                        </div>
                        <div class="document-detail-info-value col-8">
                            <?php echo $value; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div> <!-- .container -->
        </div> <!-- .document-detail-info -->

        <!-- ATTACHMENT -->
        <div class="document-detail-info col-12 mt-4">
            <h4><?php echo $this->lang->line('attachment'); ?></h4>
            <hr>
            
            <!-- attachment -->
            <div class="container">
                <div class="row">
                    <?php foreach ($document['attachment'] as $key => $value): ?>
                        <div class="document-detail-info-key col-4">
                            <?php echo $key; ?>
                        </div>
                        <div class="document-detail-info-value col-8">
                            <?php if (strtolower($key) === 'content'): ?>
                                <div class="document-detail-content">
                                    <?php echo $value; ?>
                                </div>
                            <?php else: echo $value; endif;?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div> <!-- .container -->
        </div> <!-- .document-detail-info -->
        
    </div> <!-- .row -->
</div> <!-- .container -->
