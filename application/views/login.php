<div class="container">
    <div class="row justify-content-center">
        
        <img src="<?php echo base_url() . 'public/img/logo.png'; ?>" width="60" height="60" class="d-inline-block align-top" alt="logo">
        
        <hr class="w-100">
        
        <form id="form-login" 
              class="form-horizontal" 
              method="post" 
              action="<?= site_url('login/verify') ?>"
              data-update="login_error_messages">
            
            <!-- Login -->
            <div class="form-group">
                <label for="login"><?php echo $this->lang->line('Login'); ?></label>
                <input type="text" name="login" class="form-control" required>
            </div>
            
            <!-- Password -->
            <div class="form-group">
                <label for="password"><?php echo $this->lang->line('Password'); ?></label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <!-- Buttons -->
            <div class="form-group mt-4">
                <input type="submit" value="<?php echo $this->lang->line('Login'); ?>" class="btn btn-primary">
            </div>
            
        </form>
    </div> <!-- .row -->
    
    <div id="login_error_messages">
        <?php echo $login_error_messages; ?>
    </div>
    
</div> <!-- .container -->
