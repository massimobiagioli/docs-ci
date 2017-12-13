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
            <h3>UPLOAD DOCUMENT</h3>

            <form class="form-admin" id="form-admin-create-index" 
                  class="form-horizontal" 
                  method="post" 
                  enctype="multipart/form-data"
                  action="<?= site_url('api/test_upload') ?>"
                  data-update="home_result home_error_messages">  

                <!-- Hidden -->
                <div>
                    <input type="hidden" name="<?php echo $csrf['name']; ?>" value="<?php echo $csrf['hash']; ?>">
                </div>

                <!-- Upload -->
                <label class="btn btn-primary btn-lg" for="file_to_upload">
                    <input id="file_to_upload" name="file_to_upload" type="file" style="display:none" 
                           onchange="$('#upload-file-info').html(this.files[0].name)">
                    Seleziona ...
                </label>
                <span class='label label-info' id="upload-file-info"></span>

                <!-- Buttons -->                                
                <div class="admin-item-buttons form-group">
                    <input type="submit" value="CARICA" class="btn btn-primary">
                </div>   
            </form>

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