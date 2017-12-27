<div class="document-detail-info">
    <h4><?php echo $this->lang->line('info'); ?></h4>
    <table class="table table-striped">        
        <tbody>
            <?php foreach ($document['_source']['document_info'] as $key => $value): ?>
            <tr>
                <th scope="row"><?php echo $key; ?></th>
                <td><?php echo $value; ?></td>            
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>   
</div> <!-- .document-info-metadata -->

<div class="document-detail-metadata">
    <h4><?php echo $this->lang->line('metadata'); ?></h4>
    <table class="table table-striped">       
        <tbody>
            <?php foreach ($document['_source']['document_metadata'] as $key => $value): ?>
            <tr>
                <th scope="row"><?php echo $key; ?></th>
                <td><?php echo $value; ?></td>            
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div> <!-- .document-info-metadata -->