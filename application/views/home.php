<?php
echo $navbar;

// Security
$csrf = [
    'name' => $this->security->get_csrf_token_name(),
    'hash' => $this->security->get_csrf_hash()
];
?>

<div class="content-wrapper container">
    <div class="row">

        <!-- UPLOAD -->
        <div class="col-12 mt-4">        
            <div class="card text-center home-item-wrapper p-2">
                <div class="card-header">
                    <h3><?php echo $this->lang->line('upload_file'); ?></h3>
                </div>

                <form class="form-home" id="form-home-upload_file" 
                      class="form-horizontal" 
                      method="post" 
                      enctype="multipart/form-data"
                      action="<?= site_url('api/upload_document') ?>"
                      data-update="home_result home_error_messages">  

                    <!-- Hidden -->
                    <div>
                        <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>">
                    </div>

                    <!-- Upload -->
                    <div class="form-group mt-2 text-left">
                        <label class="btn btn-success btn-lg" for="file_to_upload">
                            <input id="file_to_upload" name="file_to_upload" type="file" style="display:none" 
                                   onchange="$('#upload-file-info').html(this.files[0].name)">
                            <?php echo $this->lang->line('select'); ?>
                        </label>
                        <span class="label label-info" id="upload-file-info"></span>
                    </div>

                    <!-- Buttons -->                                
                    <div class="home-item-buttons form-group">
                        <input type="submit" value="<?php echo $this->lang->line('load'); ?>" class="btn btn-primary">
                    </div>   
                </form>
            </div> <!-- .home-item-wrapper -->
        </div> <!-- .col -->

    </div> <!-- .row -->

    <!-- MESSAGES -->
    <div class="row mt-4">
        <div id="home_error_messages" class="col-12">

        </div>

        <div id="home_result" class="col-12">

        </div>
    </div>

</div> <!-- .content-wrapper -->