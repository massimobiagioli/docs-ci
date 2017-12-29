<div id="home_dlg_document_delete_csrf" 
     data-csrftokenname="<?php echo $this->security->get_csrf_token_name(); ?>" 
     data-csrfhash="<?php echo $this->security->get_csrf_hash(); ?>"></div>
<div id="home_dlg_document_delete_docid" data-docid="<?php echo $document['id']; ?>"></div>
<span><?php echo $this->lang->line('question_delete_document'); ?> <b><?php echo $document['document_info']['original_filename']; ?></b>?</span>