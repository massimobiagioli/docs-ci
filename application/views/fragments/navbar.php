<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <a class="navbar-brand">
        <img src="<?php echo base_url() . 'public/img/logo.png'; ?>" width="40" height="40" class="d-inline-block align-middle" alt="logo">
        <span class="logged-user ml-5"><i class="fa fa-user mr-2" aria-hidden="true"></i><?php echo $logged_user['user_login']; ?></span>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item <?php echo ($current === 'home' ? 'active' : '');  ?>">
                <a class="nav-link" href="<?php echo site_url('home'); ?>"><?php echo $this->lang->line('home'); ?>
                    <?php if($current === 'home'): ?>
                        <span class="sr-only">(current)</span>
                    <?php endif; ?>
                </a>
            </li>
            
            <?php if($logged_user['user_admin'] === '1'): ?>
                <li class="nav-item <?php echo ($current === 'admin' ? 'active' : '');  ?>">
                    <a class="nav-link" href="<?php echo site_url('admin'); ?>"><?php echo $this->lang->line('administration'); ?>
                        <?php if($current === 'admin'): ?>
                            <span class="sr-only">(current)</span>
                        <?php endif; ?>
                    </a>
                </li>
            <?php endif; ?>
            
            <li class="nav-item">
                <a class="nav-link" href="<?php echo site_url('home/logout'); ?>"><?php echo $this->lang->line('logout'); ?></a>
            </li>
        </ul>
    </div>
</nav>