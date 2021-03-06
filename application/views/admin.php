<?php 
    echo $navbar; 
    
    // Security
    $csrf = [
        'name' => $this->security->get_csrf_token_name(),
        'hash' => $this->security->get_csrf_hash()
    ];
?>

<div class="content-wrapper container-fluid">
    <div class="row">
        
        <!-- CREATE INDEX -->
        <div class="col-sm-6 col-md-4 mt-4">            
            <div class="card text-center admin-item-wrapper p-2">
                <div class="card-header">
                    <h3><?php echo $this->lang->line('index_creation'); ?></h3>
                </div>
                <div class="admin-item-content card-block mt-2">
                    <div class="admin-item-icon">
                        <i class="fa fa-plus-circle fa-4x" aria-hidden="true"></i>
                    </div> <!-- .admin-item-icon -->
                    <div class="admin-item-params mt-3">
                        <form class="form-admin" id="form-admin-create-index" 
                            class="form-horizontal" 
                            method="post" 
                            action="<?= site_url('admin/create_index') ?>"
                            data-update="admin_result admin_error_messages">

                            <!-- Hidden -->
                            <div>
                                <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>">
                            </div>

                            <!-- Index name -->
                            <div class="form-group">
                                <label for="index_name"><?php echo $this->lang->line('index_name'); ?></label>
                                <input type="text" name="index_name" class="form-control" required>
                            </div>            

                            <!-- Buttons -->                                
                            <div class="admin-item-buttons form-group">
                                <input type="submit" value="<?php echo $this->lang->line('create_index'); ?>" class="btn btn-primary">
                            </div>                                
                        </form>
                    </div> <!-- .admin-item-params -->
                </div> <!-- .admin-item-content -->               
            </div>
        </div> <!-- .col -->
        
        <!-- DELETE INDEX -->
        <div class="col-sm-6 col-md-4 mt-4">            
            <div class="card text-center admin-item-wrapper p-2">
                <div class="card-header">
                    <h3><?php echo $this->lang->line('index_deletion'); ?></h3>
                </div>
                <div class="admin-item-content card-block mt-2">
                    <div class="admin-item-icon">
                        <i class="fa fa-minus-circle fa-4x" aria-hidden="true"></i>
                    </div> <!-- .admin-item-icon -->
                    <div class="admin-item-params mt-3">
                        <form class="form-admin" id="form-admin-create-index" 
                            class="form-horizontal" 
                            method="post" 
                            action="<?= site_url('admin/delete_index') ?>"
                            data-update="admin_result admin_error_messages">

                            <!-- Hidden -->
                            <div>
                                <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>">
                            </div>

                            <!-- Index name -->
                            <div class="form-group">
                                <label for="index_name"><?php echo $this->lang->line('index_name'); ?></label>
                                <input type="text" name="index_name" class="form-control" required>
                            </div>        

                            <!-- Buttons -->                                
                            <div class="admin-item-buttons form-group">
                                <input type="submit" value="<?php echo $this->lang->line('delete_index'); ?>" class="btn btn-primary">
                            </div>                                
                        </form>
                    </div> <!-- .admin-item-params -->
                </div> <!-- .admin-item-content -->      
            </div>
        </div> <!-- .col -->
        
        <!-- MIGRATIONS -->
        <div class="col-sm-6 col-md-4 mt-4">            
            <div class="card text-center admin-item-wrapper p-2">
                <div class="card-header">
                    <h3><?php echo $this->lang->line('migrations'); ?></h3>
                </div>
                <div class="admin-item-content card-block mt-2">
                    <div class="admin-item-icon">
                        <i class="fa fa-database fa-4x" aria-hidden="true"></i>
                    </div> <!-- .admin-item-icon -->
                    <div class="admin-item-params mt-3">
                        <form class="form-admin" id="form-admin-create-index" 
                            class="form-horizontal" 
                            method="post" 
                            action="<?= site_url('admin/migrate') ?>"
                            data-update="admin_result admin_error_messages">

                            <!-- Hidden -->
                            <div>
                                <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>">
                            </div>

                            <!-- Buttons -->                                
                            <div class="admin-item-buttons form-group">
                                <input type="submit" value="<?php echo $this->lang->line('execute'); ?>" class="btn btn-primary">
                            </div>      
                        </form>
                    </div> <!-- .admin-item-params -->
                </div> <!-- .admin-item-content -->              
            </div>
        </div> <!-- .col -->
        
    </div> <!-- .row -->
    
    <!-- MESSAGES -->
    <div class="row mt-4 mr-2 ml-2">
        <div id="admin_error_messages" class="col-12">
            <?php echo $admin_error_messages; ?>
        </div>

        <div id="admin_result" class="col-12">
            <?php echo $admin_result; ?>
        </div>
    </div>
    
</div> <!-- .content-wrapper -->